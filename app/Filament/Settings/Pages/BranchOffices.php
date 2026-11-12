<?php

namespace App\Filament\Settings\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\MastOfic;
use App\Services\OfficeManagement\MastOficService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Throwable;

class BranchOffices extends Page
{
    use HasCompanyHeader, WithPagination;

    protected static ?string $navigationLabel = 'Branch Office';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;
    protected static ?int $navigationSort = 14;
    protected static ?string $slug = 'branch-offices';
    public const PER_PAGE_OPTIONS = [50, 100, 250];

    public int $perPage = 50;

    protected string $view = 'filament.settings.pages.branch-offices';

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
    public string $typeFilter = '';

    public array $form = [];

    public function mount(): void
    {
        $this->userUin = (string) Session::get('partaker_user_uin');
        $this->orgaUin = (string) Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));

            return;
        }

        $this->form = $this->blankForm();
    }

    protected function blankForm(): array
    {
        return [
            'Hiky_UIN' => '',
            'Parn_Ofic_UIN' => '0',
            'Ofic_Code' => '',
            'Ofic_Name' => '',
            'Ofic_Strt_Dt' => '',
            'Ofic_Clos_Dt' => '',
            'Cutr_UIN' => '',
            'Stat_UIN' => '',
            'Dist_UIN' => '',
            'PinCod_UIN' => '',
            'Loca_UIN' => '',
            'Stet_UIN' => '',
            'Bild_No_Name' => '',
            'Ofic_LdLi' => '',
            'Ofic_Emai' => '',
            'Lon' => '',
            'Lat' => '',
            'Stau_UIN' => '100201',
        ];
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

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    // Dependent selects: type -> parent, country -> state -> district
    public function updatedForm(mixed $value, string $key): void
    {
        match ($key) {
            'Hiky_UIN' => $this->form['Parn_Ofic_UIN'] = '0',
            'Cutr_UIN' => $this->form = array_merge($this->form, ['Stat_UIN' => '', 'Dist_UIN' => '']),
            'Stat_UIN' => $this->form['Dist_UIN'] = '',
            default => null,
        };
    }

    #[On('company-switched')]
    public function refreshOrganisation(): void
    {
        $this->orgaUin = (string) Session::get('selected_orga_uin');
        unset($this->offices, $this->officeTypes);
    }

    #[Computed]
    public function offices(): LengthAwarePaginator
    {
        return app(MastOficService::class)->paginate($this->orgaUin, $this->search, $this->typeFilter, $this->perPage);
    }

    #[Computed]
    public function officeTypes(): Collection
    {
        return app(MastOficService::class)->officeTypes($this->orgaUin);
    }

    #[Computed]
    public function parentOffices(): Collection
    {
        $level = $this->officeTypes->firstWhere('Hiky_UIN', (int) ($this->form['Hiky_UIN'] ?: 0))?->Hiky_Levl;

        return $level
            ? app(MastOficService::class)->parentOptions($this->orgaUin, (int) $level, $this->editingUin)
            : collect();
    }

    #[Computed]
    public function countries(): Collection
    {
        return app(MastOficService::class)->countries();
    }

    #[Computed]
    public function states(): Collection
    {
        return app(MastOficService::class)->states($this->form['Cutr_UIN'] ?: null);
    }

    #[Computed]
    public function districts(): Collection
    {
        return app(MastOficService::class)->districts($this->form['Stat_UIN'] ?: null);
    }

    public function openCreate(): void
    {
        $this->resetForm();

        if ($this->officeTypes->isEmpty()) {
            session()->now('error', 'Create an Office Type first (Settings → Office Type).');

            return;
        }

        $this->showForm = true;
    }

    public function openEdit(string $uin): void
    {
        $o = $this->findOffice($uin);

        $this->resetErrorBag();
        $this->editingUin = (string) $o->getKey();
        $this->form = [
            'Hiky_UIN' => (string) $o->Hiky_UIN,
            'Parn_Ofic_UIN' => (string) $o->Parn_Ofic_UIN,
            'Ofic_Code' => (string) $o->Ofic_Code,
            'Ofic_Name' => (string) $o->Ofic_Name,
            'Ofic_Strt_Dt' => $o->Ofic_Strt_Dt?->format('Y-m-d') ?? '',
            'Ofic_Clos_Dt' => $o->Ofic_Clos_Dt?->format('Y-m-d') ?? '',
            'Cutr_UIN' => (string) ($o->Cutr_UIN ?? ''),
            'Stat_UIN' => (string) ($o->Stat_UIN ?? ''),
            'Dist_UIN' => (string) ($o->Dist_UIN ?? ''),
            'PinCod_UIN' => (string) ($o->PinCod_UIN ?? ''),
            'Loca_UIN' => (string) ($o->Loca_UIN ?? ''),
            'Stet_UIN' => (string) ($o->Stet_UIN ?? ''),
            'Bild_No_Name' => (string) ($o->Bild_No_Name ?? ''),
            'Ofic_LdLi' => (string) ($o->Ofic_LdLi ?? ''),
            'Ofic_Emai' => (string) ($o->Ofic_Emai ?? ''),
            'Lon' => (string) ($o->Lon ?? ''),
            'Lat' => (string) ($o->Lat ?? ''),
            'Stau_UIN' => (string) ($o->Stau_UIN ?? '100201'),
        ];
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate([
            'form.Hiky_UIN' => ['required', Rule::in($this->officeTypes->pluck('Hiky_UIN')->all())],
            'form.Parn_Ofic_UIN' => ['required', Rule::in(array_merge([0], $this->parentOffices->pluck('Ofic_UIN')->all()))],
            'form.Ofic_Code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tbl_mast_ofic', 'Ofic_Code')
                    ->where('Orga_UIN', $this->orgaUin)
                    ->whereNull('DeOn')
                    ->ignore($this->editingUin, 'Ofic_UIN'),
            ],
            'form.Ofic_Name' => ['required', 'string', 'max:255'],
            'form.Ofic_Strt_Dt' => ['nullable', 'date'],
            'form.Ofic_Clos_Dt' => ['nullable', 'date', 'after_or_equal:form.Ofic_Strt_Dt'],
            'form.Cutr_UIN' => ['nullable', 'integer'],
            'form.Stat_UIN' => ['nullable', 'integer'],
            'form.Dist_UIN' => ['nullable', 'integer'],
            'form.PinCod_UIN' => ['nullable', 'string', 'max:20'],
            'form.Loca_UIN' => ['nullable', 'string', 'max:255'],
            'form.Stet_UIN' => ['nullable', 'string', 'max:255'],
            'form.Bild_No_Name' => ['nullable', 'string', 'max:255'],
            'form.Ofic_LdLi' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'form.Ofic_Emai' => ['nullable', 'email', 'max:255'],
            'form.Lon' => ['nullable', 'numeric', 'between:-180,180'],
            'form.Lat' => ['nullable', 'numeric', 'between:-90,90'],
            'form.Stau_UIN' => ['required', Rule::in(['100201', '100202'])],
        ]);

        $data = array_map(fn($v) => $v === '' ? null : $v, $this->form);
        $data['Parn_Ofic_UIN'] = (int) ($data['Parn_Ofic_UIN'] ?? 0);

        $service = app(MastOficService::class);

        try {
            if ($this->editingUin) {
                $service->update($this->findOffice($this->editingUin), $data, $this->userUin);
                $message = 'Branch office updated successfully.';
            } else {
                $service->create($this->orgaUin, $data, $this->userUin);
                $message = 'Branch office added successfully.';
            }
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not save the branch office. Please try again.');

            return;
        }

        $this->resetForm();
        unset($this->offices);

        session()->now('success', $message);
    }

    public function confirmDelete(string $uin): void
    {
        $this->deletingUin = (string) $this->findOffice($uin)->getKey();
    }

    public function cancelDelete(): void
    {
        $this->deletingUin = null;
    }

    public function delete(string $uin): void
    {
        $office = $this->findOffice($uin);

        try {
            app(MastOficService::class)->delete($office, $this->userUin);
        } catch (ValidationException $e) {
            $this->deletingUin = null;
            session()->now('error', $e->validator->errors()->first());

            return;
        } catch (Throwable $e) {
            report($e);
            $this->deletingUin = null;
            session()->now('error', 'Could not delete the branch office. Please try again.');

            return;
        }

        $this->deletingUin = null;
        unset($this->offices);

        if ($this->offices->isEmpty() && $this->getPage() > 1) {
            $this->previousPage();
            unset($this->offices);
        }

        session()->now('success', 'Branch office deleted successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset(['showForm', 'editingUin']);
        $this->form = $this->blankForm();
        $this->resetErrorBag();
    }

    // Always look the record up inside the current organisation, never trust a posted ID alone.
    protected function findOffice(string $uin): MastOfic
    {
        $office = app(MastOficService::class)->find($this->orgaUin, $uin);

        abort_unless($office, 404);

        return $office;
    }
}
