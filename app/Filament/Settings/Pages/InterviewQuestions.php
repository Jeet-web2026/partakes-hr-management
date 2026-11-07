<?php

namespace App\Filament\Settings\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\MastIntvQstnService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Throwable;

class InterviewQuestions extends Page
{
    use HasCompanyHeader, WithPagination;

    protected static ?string $navigationLabel = 'Interview Questions';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static ?int $navigationSort = 11;
    protected static ?string $slug = 'interview-questions';

    public const PER_PAGE_OPTIONS = [50, 100, 250];

    public int $perPage = 50;

    protected string $view = 'filament.settings.pages.interview-questions';

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingUin = null;

    public bool $showForm = false;
    public string $search = '';
    public string $formText = '';
    public string $formMarks = '';          // string because an empty <input> sends ''
    public string $formStatus = '';

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
        $this->resetPage();
        unset($this->questions);
    }

    #[Computed]
    public function questions(): LengthAwarePaginator
    {
        return app(MastIntvQstnService::class)->paginateForOrganisation(
            $this->orgaUin,
            $this->search,
            $this->perPage,
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        // never trust the browser: any other value falls back to 50
        $this->perPage = in_array((int) $value, self::PER_PAGE_OPTIONS, true) ? (int) $value : 50;
        $this->resetPage();
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
        $this->formText   = (string) $question->Question_Text;
        $this->formMarks  = $question->Qstn_Mark !== null ? (string) $question->Qstn_Mark : '';
        $this->formStatus = (string) $question->Intv_qstn_Stau;   // cast is integer, so no ->value
        $this->showForm   = true;
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->formText  = trim($this->formText);
        $this->formMarks = trim($this->formMarks);

        $this->validate([
            'formText'   => ['required', 'string', 'max:2000'],
            'formMarks'  => ['required', 'integer', 'min:0', 'max:100'],
            'formStatus' => ['required'],
        ], [
            'formMarks.required' => 'Please enter the marks.',
            'formMarks.integer'  => 'Marks must be a whole number.',
            'formMarks.min'      => 'Marks cannot be negative.',
            'formMarks.max'      => 'Marks cannot be more than 100.',
        ]);

        $marks   = (int) $this->formMarks;
        $service = app(MastIntvQstnService::class);

        try {
            if ($this->editingUin) {
                $service->update($this->findQuestion($this->editingUin), [
                    'Question_Text'  => $this->formText,
                    'Qstn_Mark' => $marks,
                    'Intv_qstn_Stau' => $this->formStatus,
                ], $this->userUin);

                session()->now('success', 'Question updated successfully.');
            } else {
                $service->create(
                    $this->orgaUin,
                    $this->formText,
                    $this->formStatus,
                    $this->userUin,
                    $marks,
                );

                session()->now('success', 'Question added successfully.');
            }
        } catch (ValidationException $e) {
            $this->addError('formText', $e->validator->errors()->first());

            return;
        }

        unset($this->questions);
        $this->resetForm();
    }

    /**
     * Called from the SweetAlert confirm button in the table.
     * SweetAlert is only a UI prompt, so the record is still looked up
     * server-side and scoped to the current organisation (404 otherwise).
     */
    public function delete(string $uin): void
    {
        $question = $this->findQuestion($uin);

        try {
            app(MastIntvQstnService::class)->delete($question, $this->userUin);
        } catch (ValidationException $e) {
            session()->now('error', $e->validator->errors()->first());

            return;
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not delete the question. Please try again.');

            return;
        }

        unset($this->questions);

        // deleted the last row on this page -> go back one page
        if ($this->questions->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
            unset($this->questions);
        }

        session()->now('success', 'Question deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin', 'formText', 'formMarks']);
        $this->formStatus = 100201;
        $this->resetErrorBag();
    }

    protected function findQuestion(string $uin)
    {
        $question = app(MastIntvQstnService::class)->find($this->orgaUin, $uin);

        abort_unless($question, 404);

        return $question;
    }
}
