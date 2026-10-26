<?php

namespace App\Filament\Settings\Pages;

use App\Enums\HrFlagStage;
use App\Enums\InterviewStepStatus;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\MastHrFlagService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class CandidateFlags extends Page
{
    use HasCompanyHeader, WithPagination;

    protected static ?string $navigationLabel = 'Candidate Flags';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;
    protected static ?int $navigationSort = 12;
    protected static ?string $slug = 'candidate-flags';
    public const PER_PAGE_OPTIONS = [50, 100, 250];

    public int $perPage = 50;

    protected string $view = 'filament.settings.pages.candidate-flags';

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
    public string $stageFilter = '';
    public string $formStage = '';
    public string $formName = '';
    public string $formStatus = 'active';

    public function mount(): void
    {
        $this->userUin = (string) Session::get('partaker_user_uin');
        $this->orgaUin = (string) Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));
        }
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, self::PER_PAGE_OPTIONS, true)) {
            $this->perPage = self::PER_PAGE_OPTIONS[0];
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStageFilter(): void
    {
        $this->resetPage();
    }

    #[On('company-switched')]
    public function refreshOrganisation(): void
    {
        $this->orgaUin = (string) Session::get('selected_orga_uin');
        unset($this->flags);
    }

    #[Computed]
    public function flags(): LengthAwarePaginator
    {
        $all = app(MastHrFlagService::class)->listForOrganisation(
            $this->orgaUin,
            $this->search,
            HrFlagStage::tryFrom($this->stageFilter),
        );

        $page = LengthAwarePaginator::resolveCurrentPage('page');

        return new LengthAwarePaginator(
            $all->forPage($page, $this->perPage)->values(),
            $all->count(),
            $this->perPage,
            $page,
        );
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->formStage = HrFlagStage::cases()[0]->value;
        $this->showForm = true;
    }

    public function openEdit(string $uin): void
    {
        $flag = $this->findFlag($uin);

        $this->resetErrorBag();
        $this->editingUin = $flag->getKey();
        $this->formStage = $flag->hr_flag_stge_name->value;
        $this->formName = $flag->hr_flag_name;
        $this->formStatus = $flag->hr_flag_stau->value;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'formStage' => ['required', Rule::enum(HrFlagStage::class)],
            'formName' => ['required', 'string', 'max:100'],
            'formStatus' => ['required', Rule::enum(InterviewStepStatus::class)],
        ]);

        $service = app(MastHrFlagService::class);

        try {
            if ($this->editingUin) {
                $service->update($this->findFlag($this->editingUin), [
                    'hr_flag_stge_name' => $this->formStage,
                    'hr_flag_name' => $this->formName,
                    'hr_flag_stau' => $this->formStatus,
                ], $this->userUin);

                $message = 'Flag updated successfully.';
            } else {
                $service->create($this->orgaUin, $this->formStage, $this->formName, $this->formStatus, $this->userUin);

                $message = 'Flag added successfully.';
            }
        } catch (ValidationException $e) {
            // Duplicate name inside the stage: show it under the field
            $this->addError('formName', $e->validator->errors()->first());
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->now('error', 'Could not save the flag. Please try again.');
            return;
        }

        unset($this->flags);
        $this->resetForm();

        session()->now('success', $message);
    }

    public function confirmDelete(string $uin): void
    {
        $this->deletingUin = $this->findFlag($uin)->getKey();
    }

    public function cancelDelete(): void
    {
        $this->deletingUin = null;
    }

    public function delete(): void
    {
        $flag = $this->findFlag((string) $this->deletingUin);

        app(MastHrFlagService::class)->delete($flag, $this->userUin);

        unset($this->flags);
        $this->deletingUin = null;

        session()->now('success', 'Flag deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin', 'formStage', 'formName']);
        $this->formStatus = 'active';
        $this->resetErrorBag();
    }

    // Always look the record up inside the current organisation, never trust a posted ID alone.
    protected function findFlag(string $uin)
    {
        $flag = app(MastHrFlagService::class)->find($this->orgaUin, $uin);

        abort_unless($flag, 404);

        return $flag;
    }
}
