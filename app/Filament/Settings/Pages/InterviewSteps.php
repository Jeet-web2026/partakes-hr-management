<?php

namespace App\Filament\Settings\Pages;

use App\Enums\InterviewStepStatus;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\MastIntvStepService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class InterviewSteps extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Interview Steps';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'interview-steps';

    protected string $view = 'filament.settings.pages.interview-steps';

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingUin = null;

    public string $search = '';
    public string $editName = '';
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
        $term = mb_strtolower(trim($this->search));

        if ($term === '') {
            return $steps;
        }

        return $steps
            ->filter(fn($step) => str_contains(mb_strtolower($step->Intv_Step_Name), $term))
            ->values();
    }

    public function openEdit(string $uin): void
    {
        $step = $this->findStep($uin);

        $this->editingUin = $step->getKey();
        $this->editName = $step->Intv_Step_Name;
        $this->editStatus = $step->Intv_Step_Stau->value;
        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->reset(['editingUin', 'editName']);
        $this->editStatus = 'active';
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:100'],
            'editStatus' => ['required', Rule::enum(InterviewStepStatus::class)],
        ]);

        $step = $this->findStep((string) $this->editingUin);

        try {
            app(MastIntvStepService::class)->updateStep($step, [
                'Intv_Step_Name' => $this->editName,
                'Intv_Step_Stau' => $this->editStatus,
            ], $this->userUin);
        } catch (ValidationException $e) {
            $this->addError('editName', $e->validator->errors()->first());
            return;
        }

        unset($this->steps);
        $this->closeEdit();

        Notification::make()->success()->title('Interview step updated')->send();
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
