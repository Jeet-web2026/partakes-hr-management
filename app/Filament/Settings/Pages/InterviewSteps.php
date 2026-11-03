<?php

namespace App\Filament\Settings\Pages;

use App\Enums\InterviewStepStatus;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\InterviewStep;
use App\Models\MastIntvStep;
use App\Services\ApplicationManagement\MastIntvStepService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
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

    public ?string $editStage = null;

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingUin = null;

    /** Default step names, display-only; locked so the client can't change them. */
    #[Locked]
    public array $editPlaceholders = [];

    public string $search = '';

    /** User-defined names, same positions as $editPlaceholders. */
    public array $editNames = [];

    public string $editStatus = 'active';

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
                $names = $step->name_list;

                return collect($step->placeholder_list)
                    ->map(fn($placeholder, $i) => filled($names[$i] ?? null) ? $names[$i] : $placeholder)
                    ->contains(fn($name) => str_contains(mb_strtolower((string) $name), $term));
            })
            ->values();
    }

    public function openEdit(string $uin): void
    {
        $step = $this->findStep($uin);
        $this->editingUin       = $step->getKey();
        $this->editPlaceholders = MastIntvStep::query()
            ->where('Intv_Stage', $step->Intv_Stage)
            ->pluck('Intv_Step_Placeholder_Type')
            ->values()
            ->all();

        $names = (array) $step->name_list;

        $this->editNames = collect($this->editPlaceholders)
            ->keys()
            ->map(fn($i) => (string) ($names[$i] ?? ''))
            ->all();

        $this->editStatus = $step->Intv_Step_Stau->value;
        $this->editStage  = $step->Intv_Stage !== null ? (string) $step->Intv_Stage : null;
        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->reset(['editingUin', 'editNames', 'editPlaceholders', 'editStage']);
        $this->editStatus = 'active';
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'editNames'   => ['required', 'array', 'size:' . count($this->editPlaceholders)],
            'editNames.*' => ['nullable', 'string', 'max:100'],
            'editStatus'  => ['required', Rule::enum(InterviewStepStatus::class)],
            'editStage'   => [
                'required',
                Rule::exists('tbl_mast_intv_stage', 'Stage_UIN')->where('Orga_UIN', $this->orgaUin),
            ],
        ]);

        $names = collect($this->editPlaceholders)
            ->map(function ($placeholder, $i) {
                $name = trim((string) ($this->editNames[$i] ?? ''));

                return $name !== '' ? $name : trim((string) $placeholder);
            })
            ->all();

        if (count($names) !== count(array_unique(array_map('mb_strtolower', $names)))) {
            $this->addError('editNames', 'Step names must be unique.');
            return;
        }

        $steps = $this->findrelatedSteps((string) $this->editStage);

        try {
            foreach ($steps as $step) {
                app(MastIntvStepService::class)->updateStep($step, [
                    'Intv_Step_Name' => $names,
                    'Intv_Step_Stau' => $this->editStatus,
                    'Intv_Stage'     => $this->editStage,
                ], $this->userUin);
            }
        } catch (ValidationException $e) {
            $this->addError('editNames', $e->validator->errors()->first());
            return;
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not update the interview step. Please try again.');
            return;
        }

        unset($this->steps);
        $this->closeEdit();

        session()->now('success', 'Interview step updated successfully.');
    }

    protected function findStep(string $uin)
    {
        $step = app(MastIntvStepService::class)
            ->listForOrganisation($this->orgaUin)
            ->firstWhere('Intv_Step_UIN', $uin);

        abort_unless($step, 404);

        return $step;
    }

    protected function findrelatedSteps(string $intStageUIN)
    {
        $steps = app(MastIntvStepService::class)
            ->listForOrganisation($this->orgaUin)
            ->where('Intv_Stage', $intStageUIN);

        abort_unless($steps, 404);

        return $steps;
    }

    #[Computed]
    public function stages(): Collection
    {
        return InterviewStep::query()
            ->forOrganisation($this->orgaUin)
            ->orderBy('Stage_Name')
            ->get();
    }
}
