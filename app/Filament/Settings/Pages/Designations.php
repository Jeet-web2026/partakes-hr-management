<?php

namespace App\Filament\Settings\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\MastDsigService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class Designations extends Page
{
    use HasCompanyHeader;

    protected static ?string $navigationLabel = 'Designations';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static ?int $navigationSort = 11;
    protected static ?string $slug = 'designations';

    protected string $view = 'filament.settings.pages.designations';

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
    public string $formName = '';
    public string $formDesc = '';
    public string $formStatus = '';

    public function mount(): void
    {
        $this->userUin = (string) Session::get('partaker_user_uin');
        $this->orgaUin = (string) Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));
        }

        $this->formStatus = (string) MastDsigService::STATUS_ACTIVE;
    }

    #[On('company-switched')]
    public function refreshOrganisation(): void
    {
        $this->orgaUin = (string) Session::get('selected_orga_uin');
        unset($this->designations);
    }

    #[Computed]
    public function designations(): Collection
    {
        return app(MastDsigService::class)->listForOrganisation($this->orgaUin, $this->search);
    }

    #[Computed]
    public function statusOptions(): array
    {
        return app(MastDsigService::class)->statusOptions();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(string $uin): void
    {
        $designation = $this->findDesignation($uin);

        $this->resetErrorBag();
        $this->editingUin = (string) $designation->getKey();
        $this->formName = $designation->Dsig_Name;
        $this->formDesc = (string) $designation->Dsig_Desp;
        $this->formStatus = (string) $designation->Dsig_Stau;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->formName = trim($this->formName);

        $this->validate([
            'formName' => ['required', 'string', 'max:150'],
            'formDesc' => ['nullable', 'string', 'max:255'],
            'formStatus' => ['required', Rule::in(array_keys($this->statusOptions))],
        ]);

        $service = app(MastDsigService::class);

        if ($service->nameExists($this->orgaUin, $this->formName, $this->editingUin)) {
            $this->addError('formName', 'This designation already exists.');

            return;
        }

        if ($this->editingUin) {
            $service->update($this->findDesignation($this->editingUin), [
                'Dsig_Name' => $this->formName,
                'Dsig_Desp' => $this->formDesc ?: null,
                'Dsig_Stau' => (int) $this->formStatus,
            ], $this->userUin);

            session()->now('success', 'Designation updated successfully.');
        } else {
            $service->create(
                $this->orgaUin,
                $this->formName,
                $this->formDesc ?: null,
                (int) $this->formStatus,
                $this->userUin,
            );

            session()->now('success', 'Designation added successfully.');
        }

        unset($this->designations);
        $this->resetForm();
    }

    public function confirmDelete(string $uin): void
    {
        $this->deletingUin = (string) $this->findDesignation($uin)->getKey();
    }

    public function cancelDelete(): void
    {
        $this->deletingUin = null;
    }

    public function delete(): void
    {
        $designation = $this->findDesignation((string) $this->deletingUin);

        app(MastDsigService::class)->delete($designation);

        unset($this->designations);
        $this->deletingUin = null;
        session()->now('success', 'Designation deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin', 'formName', 'formDesc']);
        $this->formStatus = (string) MastDsigService::STATUS_ACTIVE;
        $this->resetErrorBag();
    }

    protected function findDesignation(string $uin)
    {
        $designation = app(MastDsigService::class)->find($this->orgaUin, $uin);

        abort_unless($designation, 404);

        return $designation;
    }
}
