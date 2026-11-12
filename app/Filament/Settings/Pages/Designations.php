<?php

namespace App\Filament\Settings\Pages;

use App\Enums\DesignationType;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\MastDsigService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Throwable;

class Designations extends Page
{
    use HasCompanyHeader, WithPagination;

    protected static ?string $navigationLabel = 'Designations';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static ?int $navigationSort = 11;
    protected static ?string $slug = 'designations';

    public const PER_PAGE_OPTIONS = [50, 100, 250];

    public int $perPage = 100;

    protected string $view = 'filament.settings.pages.designations';

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingUin = null;

    public bool $showForm = false;
    public string $search = '';
    public string $formName = '';
    public string $formDesc = '';
    public string $formStatus = '';
    public string $formType = '';
    public string $formParent = '0'; // 0 = Primary Designation

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
        $this->resetPage();
        unset($this->designations);
    }

    #[Computed]
    public function typeOptions(): array
    {
        return collect(DesignationType::cases())
            ->mapWithKeys(fn($t) => [$t->value => $t->label()])
            ->all();
    }

    // [Dsig_UIN => Dsig_Name] for the selected type; "Primary Designation" is added in the view
    #[Computed]
    public function parentOptions(): array
    {
        if ($this->formType === '') {
            return [];
        }

        return app(MastDsigService::class)->parentOptions(
            $this->orgaUin,
            $this->formType,
            $this->editingUin,
        );
    }

    #[Computed]
    public function designations(): LengthAwarePaginator
    {
        return app(MastDsigService::class)->paginateForOrganisation(
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

    // Each type has its own hierarchy, so a different type goes back to Primary Designation
    public function updatedFormType(): void
    {
        $this->formParent = '0';
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
        $this->formType = $designation->Dsig_Type?->value ?? '';
        $this->formParent = (string) ($designation->Parn_Dsig_UIN ?? 0);
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
            'formType'   => ['required', Rule::enum(DesignationType::class)],
            // only Primary (0) or a designation of the same type / organisation (never itself or its children)
            'formParent' => ['required', Rule::in(array_merge([0], array_keys($this->parentOptions)))],
        ]);

        $service = app(MastDsigService::class);

        if ($service->nameExists($this->orgaUin, $this->formName, $this->editingUin)) {
            $this->addError('formName', 'This designation already exists.');

            return;
        }

        try {
            if ($this->editingUin) {
                $service->update($this->findDesignation($this->editingUin), [
                    'Dsig_Name' => $this->formName,
                    'Dsig_Desp' => $this->formDesc ?: null,
                    'Dsig_Stau' => (int) $this->formStatus,
                    'Dsig_Type' => $this->formType,
                    'Parn_Dsig_UIN' => (int) $this->formParent,
                ], $this->userUin);

                session()->now('success', 'Designation updated successfully.');
            } else {
                $service->create(
                    $this->orgaUin,
                    $this->formName,
                    $this->formDesc ?: null,
                    (int) $this->formStatus,
                    $this->userUin,
                    $this->formType,
                    (int) $this->formParent,
                );

                session()->now('success', 'Designation added successfully.');
            }
        } catch (ValidationException $e) {
            // service-level duplicate check
            $this->addError('formName', $e->validator->errors()->first());

            return;
        }

        unset($this->designations);
        $this->resetForm();
    }

    public function delete(string $uin): void
    {
        $designation = $this->findDesignation($uin);

        try {
            app(MastDsigService::class)->delete($designation);
        } catch (ValidationException $e) {
            // e.g. 'Cannot delete "Manager": it is the parent of other designations.'
            session()->now('error', $e->validator->errors()->first());

            return;
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not delete the designation. Please try again.');

            return;
        }

        unset($this->designations);

        // deleted the last row on this page -> go back one page
        if ($this->designations->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
            unset($this->designations);
        }

        session()->now('success', 'Designation deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin', 'formName', 'formDesc', 'formType', 'formParent']);
        $this->formStatus = (string) MastDsigService::STATUS_ACTIVE;
        $this->formParent = '0';
        $this->resetErrorBag();
    }

    protected function findDesignation(string $uin)
    {
        $designation = app(MastDsigService::class)->find($this->orgaUin, $uin);

        abort_unless($designation, 404);

        return $designation;
    }
}
