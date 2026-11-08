<?php

namespace App\Filament\Settings\Pages;

use App\Enums\OfficeLevel;
use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\HierKey;
use App\Services\OfficeManagement\MastHierKeyService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Throwable;

class OfficeLevels extends Page
{
    use HasCompanyHeader, WithPagination;

    protected static ?string $navigationLabel = 'Office Level';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?int $navigationSort = 13;
    protected static ?string $slug = 'office-levels';
    public const PER_PAGE_OPTIONS = [50, 100, 250];

    public int $perPage = 50;

    protected string $view = 'filament.settings.pages.office-levels';

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
    public string $levelFilter = '';
    public string $formLevel = '';
    public string $formName = '';
    public string $formStatus = '1';

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

    public function updatedLevelFilter(): void
    {
        $this->resetPage();
    }

    // Pre-fill the name from the chosen level (only while it's empty or still a default label)
    public function updatedFormLevel(string $value): void
    {
        $level = OfficeLevel::tryFrom((int) $value);
        $defaults = array_map(fn(OfficeLevel $l) => $l->label(), OfficeLevel::cases());

        if ($level && ($this->formName === '' || in_array($this->formName, $defaults, true))) {
            $this->formName = $level->label();
        }
    }

    #[On('company-switched')]
    public function refreshOrganisation(): void
    {
        $this->orgaUin = (string) Session::get('selected_orga_uin');
        unset($this->levels, $this->availableLevels);
    }

    #[Computed]
    public function levels(): LengthAwarePaginator
    {
        $all = app(MastHierKeyService::class)->listForOrganisation(
            $this->orgaUin,
            $this->search,
            $this->levelFilter !== '' ? OfficeLevel::tryFrom((int) $this->levelFilter) : null,
        );

        $page = LengthAwarePaginator::resolveCurrentPage('page');

        return new LengthAwarePaginator(
            $all->forPage($page, $this->perPage)->values(),
            $all->count(),
            $this->perPage,
            $page,
        );
    }

    // Levels not yet created for this organisation (plus the one being edited, if any)
    #[Computed]
    public function availableLevels(): array
    {
        $used = app(MastHierKeyService::class)->usedLevels($this->orgaUin, $this->editingUin);

        return array_values(array_filter(
            OfficeLevel::cases(),
            fn(OfficeLevel $level) => ! in_array($level->value, $used, true),
        ));
    }

    public function openCreate(): void
    {
        $this->resetForm();

        if (count($this->availableLevels) === 0) {
            session()->now('error', 'All office levels have already been created.');

            return;
        }

        $this->showForm = true;
    }

    public function openEdit(string $uin): void
    {
        $level = $this->findLevel($uin);

        $this->resetErrorBag();
        $this->editingUin = (string) $level->getKey();
        $this->formLevel = (string) $level->Hiky_Levl;
        $this->formName = $level->Hiky_Levl_Name ?? '';
        $this->formStatus = (string) $level->Stau_UIN;
        $this->showForm = true;

        unset($this->availableLevels);
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'formLevel' => ['required', Rule::in(array_column(OfficeLevel::cases(), 'value'))],
            'formName' => ['required', 'string', 'max:100'],
            'formStatus' => ['required', Rule::in(['1', '0'])],
        ]);

        $service = app(MastHierKeyService::class);

        try {
            if ($this->editingUin) {
                $service->update($this->findLevel($this->editingUin), [
                    'Hiky_Levl' => (int) $this->formLevel,
                    'Hiky_Levl_Name' => $this->formName,
                    'Stau_UIN' => (int) $this->formStatus,
                ], $this->userUin);

                $message = 'Office level updated successfully.';
            } else {
                $service->create(
                    $this->orgaUin,
                    (int) $this->formLevel,
                    $this->formName,
                    (int) $this->formStatus,
                    $this->userUin,
                );

                $message = 'Office level added successfully.';
            }
        } catch (ValidationException $e) {
            // Duplicate level for this organisation: show it under the field
            $this->addError('formLevel', $e->validator->errors()->first());
            return;
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not save the office level. Please try again.');
            return;
        }

        $this->resetForm();
        unset($this->levels, $this->availableLevels);

        session()->now('success', $message);
    }

    public function confirmDelete(string $uin): void
    {
        $this->deletingUin = (string) $this->findLevel($uin)->getKey();
    }

    public function cancelDelete(): void
    {
        $this->deletingUin = null;
    }

    public function delete(string $uin): void
    {
        $level = $this->findLevel($uin);

        try {
            app(MastHierKeyService::class)->delete($level, $this->userUin);
        } catch (ValidationException $e) {
            $this->deletingUin = null;
            session()->now('error', $e->validator->errors()->first());

            return;
        } catch (Throwable $e) {
            report($e);
            $this->deletingUin = null;
            session()->now('error', 'Could not delete the office level. Please try again.');

            return;
        }

        $this->deletingUin = null;
        unset($this->levels, $this->availableLevels);

        if ($this->levels->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
            unset($this->levels);
        }

        session()->now('success', 'Office level deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin', 'formLevel', 'formName']);
        $this->formStatus = '1';
        $this->resetErrorBag();

        unset($this->availableLevels);
    }

    // Always look the record up inside the current organisation, never trust a posted ID alone.
    protected function findLevel(string $uin): HierKey
    {
        $level = app(MastHierKeyService::class)->find($this->orgaUin, $uin);

        abort_unless($level, 404);

        return $level;
    }
}
