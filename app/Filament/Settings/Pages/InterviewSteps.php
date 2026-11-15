<?php

namespace App\Filament\Settings\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\InterviewStep;
use App\Models\MastIntvStep;
use App\Models\MastStau;
use App\Services\ApplicationManagement\MastIntvStepService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Throwable;

class InterviewSteps extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Interview Steps';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'interview-steps';

    protected string $view = 'filament.settings.pages.interview-steps';

    /** Selected stage (Stage_UIN). Read-only when editing, selectable when adding. */
    public ?string $editStage = null;

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingUin = null;

    /** True while the modal is in "Add" mode. */
    #[Locked]
    public bool $isAdding = false;

    /** Intv_Step_UINs being edited, in display order; locked so the client can't change them. */
    #[Locked]
    public array $editStepUins = [];

    /** Default step names (Intv_Step_Placeholder_Type), display-only. Also defines the row count. */
    #[Locked]
    public array $editPlaceholders = [];

    /** Status dropdown options (Stau_UIN => Stau_Name); locked so validation can trust them. */
    #[Locked]
    public array $statusOptions = [];

    public string $search = '';

    /** Saved names (Intv_Step_Name), same positions as $editPlaceholders ('' = not set). */
    public array $editNames = [];

    /** Status (Stau_UIN) per row, same positions as $editPlaceholders. */
    public array $editStatuses = [];

    public function mount(): void
    {
        $this->userUin = (string) Session::get('partaker_user_uin');
        $this->orgaUin = (string) Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));
        }
    }

    #[Computed]
    public function steps(): Collection
    {
        $steps = app(MastIntvStepService::class)->listForOrganisation($this->orgaUin);
        $term  = mb_strtolower(trim($this->search));

        if ($term === '') {
            return $steps;
        }

        return $steps
            ->filter(function ($step) use ($term) {
                $names = (array) $step->Intv_Step_Name;

                return collect((array) $step->placeholder_list)
                    ->map(fn($placeholder, $i) => filled($names[$i] ?? null) ? $names[$i] : $placeholder)
                    ->contains(fn($name) => str_contains(mb_strtolower((string) $name), $term));
            })
            ->values();
    }

    /** Stau_UIN => Stau_Name, used by the table badge and the modal dropdown. */
    #[Computed]
    public function statusLabels(): array
    {
        return MastStau::query()
            ->whereIn('Stau_Name', ['Active', 'Inactive'])
            ->pluck('Stau_Name', 'Stau_UIN')
            ->all();
    }

    #[Computed]
    public function stages(): Collection
    {
        return InterviewStep::query()
            ->forOrganisation($this->orgaUin)
            ->orderBy('Stage_Name')
            ->get();
    }

    /** Active stages that don't have any steps yet (the only ones that can be added). */
    #[Computed]
    public function addableStages(): Collection
    {
        $used = MastIntvStep::query()
            ->where('Orga_UIN', $this->orgaUin)
            ->pluck('Intv_Stage')
            ->map(fn($v) => (string) $v)
            ->unique();

        return $this->stages
            ->filter(fn($stage) => (int) $stage->Stage_Status === MastIntvStepService::STATUS_ACTIVE
                && ! $used->contains((string) $stage->Stage_UIN))
            ->values();
    }

    /**
     * Fixed rows for a stage: row index (0-based) => fixed step name.
     * Empty for normal stages; Offer Accepted => last three rows. Resolved server-side.
     */
    public function fixedRowLabels(?string $stageUin): array
    {
        if (! $stageUin) {
            return [];
        }

        $stage = $this->stages->firstWhere('Stage_UIN', $stageUin);

        return $stage ? MastIntvStepService::fixedStepMap($stage->Stage_Name) : [];
    }

    /** Opens the modal with all 7 default steps, regardless of the selected stage. */
    public function openAdd(): void
    {
        $this->closeEdit();

        $count = count(MastIntvStepService::DEFAULT_STEPS);

        $this->isAdding         = true;
        $this->statusOptions    = $this->statusLabels;
        $this->editPlaceholders = MastIntvStepService::DEFAULT_STEPS;
        $this->editNames        = array_fill(0, $count, '');
        $this->editStatuses     = array_fill(0, $count, (string) MastIntvStepService::STATUS_ACTIVE);
    }

    public function openEdit(string $uin): void
    {
        $clicked = $this->findStep($uin);

        // One DB row per step in this stage, in step order.
        $rows = MastIntvStep::query()
            ->where('Intv_Stage', $clicked->Intv_Stage)
            ->orderBy('Intv_Step_Numb')
            ->get();

        $this->isAdding         = false;
        $this->editingUin       = (string) $clicked->getKey();
        $this->editStage        = $clicked->Intv_Stage !== null ? (string) $clicked->Intv_Stage : null;
        $this->editStepUins     = $rows->pluck('Intv_Step_UIN')->map(fn($v) => (string) $v)->values()->all();
        $this->editPlaceholders = $rows->pluck('Intv_Step_Placeholder_Type')->values()->all();

        // Saved name -> shown as value; empty -> placeholder hint shows.
        $this->editNames = $rows
            ->map(fn($row) => trim((string) $row->Intv_Step_Name))
            ->values()
            ->all();

        $this->editStatuses = $rows
            ->map(fn($row) => (string) $row->Intv_Step_Stau)
            ->values()
            ->all();

        $this->statusOptions = $this->statusLabels;

        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->reset([
            'isAdding',
            'editingUin',
            'editStepUins',
            'editNames',
            'editStatuses',
            'editPlaceholders',
            'statusOptions',
            'editStage',
        ]);

        $this->resetErrorBag();
    }

    public function save(): void
    {
        $count = count($this->editPlaceholders);

        if ($count === 0) {
            $this->addError('editStage', 'Please select a stage.');
            return;
        }

        // Add: stage must be one of the addable stages. Edit: stage must belong to this org.
        $stageRule = $this->isAdding
            ? Rule::in($this->addableStages->pluck('Stage_UIN')->map(fn($v) => (string) $v)->all())
            : Rule::exists('tbl_mast_intv_stage', 'Stage_UIN')->where('Orga_UIN', $this->orgaUin);

        $this->validate([
            'editNames'      => ['required', 'array', 'size:' . $count],
            'editNames.*'    => ['nullable', 'string', 'max:100'],
            'editStatuses'   => ['required', 'array', 'size:' . $count],
            'editStatuses.*' => ['required', Rule::in(array_keys($this->statusOptions))],
            'editStage'      => ['required', $stageRule],
        ], [
            'editStage.required'      => 'Please select a stage.',
            'editStage.in'            => 'Invalid stage selected.',
            'editStatuses.*.required' => 'Please select a status.',
            'editStatuses.*.in'       => 'Invalid status selected.',
        ]);

        // Fixed rows (Offer Accepted: last three) always use their fixed names, whatever the client sent.
        $fixedMap = $this->fixedRowLabels($this->editStage);

        // Blank stays blank (placeholder is only a hint, never saved as a name).
        $names = collect(range(0, $count - 1))
            ->map(fn($i) => $fixedMap[$i] ?? trim((string) ($this->editNames[$i] ?? '')))
            ->all();

        $filled = array_filter($names, fn($n) => $n !== '');

        if (count($filled) !== count(array_unique(array_map('mb_strtolower', $filled)))) {
            $this->addError('editNames', 'Step names must be unique.');
            return;
        }

        $wasAdding = $this->isAdding;

        try {
            if ($wasAdding) {
                app(MastIntvStepService::class)->addForStage(
                    $this->orgaUin,
                    $this->userUin,
                    (string) $this->editStage,
                    $names,
                    $this->editStatuses
                );
            } else {
                $rows = MastIntvStep::query()
                    ->whereIn('Intv_Step_UIN', $this->editStepUins)
                    ->get()
                    ->keyBy(fn($row) => (string) $row->Intv_Step_UIN);

                if ($rows->count() !== $count) {
                    $this->addError('editNames', 'Steps changed while editing. Please close and reopen.');
                    return;
                }

                DB::transaction(function () use ($rows, $names, $fixedMap) {
                    $service = app(MastIntvStepService::class);

                    foreach ($this->editStepUins as $i => $stepUin) {
                        // Fixed rows are never updated.
                        if (array_key_exists($i, $fixedMap)) {
                            continue;
                        }

                        $step = $rows[$stepUin];

                        $service->updateStep($step, [
                            'Intv_Step_Name' => $names[$i],               // this row's name ('' allowed)
                            'Intv_Step_Stau' => $this->editStatuses[$i],  // this row's status
                            'Intv_Stage'     => $step->Intv_Stage,        // keep each row's own stage
                        ], $this->userUin);
                    }
                });
            }
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $key    = array_key_exists('editStage', $errors) ? 'editStage' : 'editNames';

            $this->addError($key, (string) collect($errors)->flatten()->first());
            return;
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not save the interview steps. Please try again.');
            return;
        }

        unset($this->steps, $this->addableStages);
        $this->closeEdit();

        session()->now('success', $wasAdding
            ? 'Interview steps added successfully.'
            : 'Interview step updated successfully.');
    }

    protected function findStep(string $uin)
    {
        $step = app(MastIntvStepService::class)
            ->listForOrganisation($this->orgaUin)
            ->firstWhere('Intv_Step_UIN', $uin);

        abort_unless($step, 404);

        return $step;
    }
}