<?php

namespace App\Filament\Settings\Pages;

use App\Enums\InterviewStepStatus;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\InterviewStep;
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

    // default step names, display-only; locked so the client can't change them
    #[Locked]
    public array $editPlaceholders = [];

    public string $search = '';

    /** user-defined names, same positions as $editPlaceholders */
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

        // search across the names shown in the table (user name if set, else placeholder)
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
        $this->editPlaceholders = $step->placeholder_list;

        $names = $step->name_list;
        $this->editNames = collect($this->editPlaceholders)
            ->keys()
            ->map(fn($i) => (string) ($names[$i] ?? ''))   // blank box -> placeholder is shown
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
            'editNames'   => ['array', 'size:' . count($this->editPlaceholders)],
            'editNames.*' => ['nullable', 'string', 'max:100'],
            'editStatus'  => ['required', Rule::enum(InterviewStepStatus::class)],
            'editStage'   => [
                'required',
                Rule::exists('tbl_mast_intv_stage', 'id')->where('Orga_UIN', $this->orgaUin),
            ],
        ]);

        $names  = array_map(fn($n) => trim((string) $n), array_values($this->editNames));
        $filled = array_filter($names, fn($n) => $n !== '');

        if (count($filled) !== count(array_unique(array_map('mb_strtolower', $filled)))) {
            $this->addError('editNames', 'Step names must be unique.');
            return;
        }

        $step = $this->findStep((string) $this->editingUin);

        try {
            app(MastIntvStepService::class)->updateStep($step, [
                'Intv_Step_Name' => $filled ? $names : null,   // null = all defaults
                'Intv_Step_Stau' => $this->editStatus,
                'Intv_Stage'     => $this->editStage,
            ], $this->userUin);
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

    #[Computed]
    public function stages(): Collection
    {
        return InterviewStep::query()
            ->forOrganisation($this->orgaUin)
            ->orderBy('Stage_Name')
            ->get();
    }
}
