<?php

namespace App\Filament\Settings\Pages;

use App\Enums\InterviewStepStatus;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\MastIntvQstnService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class InterviewQuestions extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Interview Questions';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static ?int $navigationSort = 11;
    protected static ?string $slug = 'settings/interview-questions';

    protected string $view = 'filament.settings.pages.interview-questions';

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingUin = null;

    #[Locked]
    public ?string $deletingUin = null;

    public bool $showForm = false;
    public string $search = '';
    public string $formText = '';
    public string $formStatus = 'active';

    public function mount(): void
    {
        $this->userUin = (string) Session::get('partaker_user_uin');
        $this->orgaUin = (string) Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));
        }
    }

    #[On('company-switched')]
    public function refreshOrganisation(): void
    {
        $this->orgaUin = (string) Session::get('selected_orga_uin');
        unset($this->questions);
    }

    #[Computed]
    public function questions(): Collection
    {
        return app(MastIntvQstnService::class)->listForOrganisation($this->orgaUin, $this->search);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(string $uin): void
    {
        $question = $this->findQuestion($uin);

        $this->resetErrorBag();
        $this->editingUin = $question->getKey();
        $this->formText = $question->Question_Text;
        $this->formStatus = $question->Intv_qstn_Stau->value;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'formText' => ['required', 'string', 'max:2000'],
            'formStatus' => ['required', Rule::enum(InterviewStepStatus::class)],
        ]);

        $service = app(MastIntvQstnService::class);

        if ($this->editingUin) {
            $service->update($this->findQuestion($this->editingUin), [
                'Question_Text' => $this->formText,
                'Intv_qstn_Stau' => $this->formStatus,
            ], $this->userUin);

            session()->now('success', 'Question updated successfully.');
        } else {
            $service->create($this->orgaUin, $this->formText, $this->formStatus, $this->userUin);

            session()->now('success', 'Question added successfully.');
        }

        unset($this->questions);
        $this->resetForm();
    }

    public function confirmDelete(string $uin): void
    {
        $this->deletingUin = $this->findQuestion($uin)->getKey();
    }

    public function cancelDelete(): void
    {
        $this->deletingUin = null;
    }

    public function delete(): void
    {
        $question = $this->findQuestion((string) $this->deletingUin);

        app(MastIntvQstnService::class)->delete($question, $this->userUin);

        unset($this->questions);
        $this->deletingUin = null;
        session()->now('success', 'Question deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin', 'formText']);
        $this->formStatus = 'active';
        $this->resetErrorBag();
    }

    protected function findQuestion(string $uin)
    {
        $question = app(MastIntvQstnService::class)->find($this->orgaUin, $uin);

        abort_unless($question, 404);

        return $question;
    }
}
