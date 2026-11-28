<?php

namespace App\Filament\Candidatemanagement\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\EmplPosting;
use App\Models\HrMastJobAply;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class EditEmployeePosting extends Page
{
    use HasCompanyHeader;

    private const FIELDS = [
        'branchOffice'  => ['Branch Office', 'Orga_UIN'],
        'designation'   => ['Designation', 'Org_UIN'],
        'department'    => ['Department', 'Orga_UIN'],
        'project'       => ['Project', 'Orga_UIN'],
        'reportingHead' => ['Reporting Head', null],
        'status'        => ['Status', 'Orga_UIN'],
    ];

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'employee-posting/{record}/edit';
    protected static ?string $title = 'Edit Posting';
    protected static string $layout = 'filament.candidatemanagement.layouts.header-only';
    protected string $view = 'filament.candidatemanagement.pages.edit-employee-posting';

    #[Locked]
    public ?string $userUin = null;

    #[Locked]
    public ?string $orgaUin = null;

    #[Locked]
    public int $applicationId = 0;

    #[Locked]
    public string $employeeName = '';

    #[Locked]
    public ?string $updatedOn = null;

    public array $form = [];

    // The Blade file has its own header
    public function getHeading(): string
    {
        return '';
    }

    #[Computed]
    public function selectedCompany(): ?object
    {
        return $this->companyInformation
            ?->firstWhere('Orga_UIN', $this->orgaUin)
            ?->organisation;
    }

    public function mount(int|string $record): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));

            return;
        }

        $this->applicationId = (int) $record;

        $application = $this->findApplication();
        $posting = $this->findPosting($application);

        $this->employeeName = (string) $application->full_name;
        $this->updatedOn = $posting?->getAttribute('MoOn')
            ? (string) $posting->getAttribute('MoOn')
            : null;

        foreach (array_keys(self::FIELDS) as $key) {
            $column = $this->lookup($key)->getForeignKeyName();
            $this->form[$column] = $posting?->getAttribute($column);
        }

        $this->form['Strt_Dt'] = $posting?->Strt_Dt?->format('Y-m-d');
        $this->form['End_Dt'] = $posting?->End_Dt?->format('Y-m-d');
    }

    public function booted(): void
    {
        $this->leaveIfCompanyChanged();
    }

    public function rendering(): void
    {
        $this->leaveIfCompanyChanged();
    }

    protected function leaveIfCompanyChanged(): bool
    {
        $selected = Session::get('selected_orga_uin');

        if (! $this->orgaUin || (string) $selected === (string) $this->orgaUin) {
            return false;
        }

        $this->redirect(EmployeePosting::getUrl());

        return true;
    }

    // Dropdown fields for the view
    #[Computed]
    public function fields(): array
    {
        $fields = [];

        foreach (self::FIELDS as $key => [$label, $orgColumn]) {
            $relation = $this->lookup($key);
            $column = EmplPosting::LOOKUPS[$key][1];

            $fields[$relation->getForeignKeyName()] = [
                'label'   => $label,
                'options' => $relation->getRelated()->newQuery()
                    ->when($orgColumn, fn($q) => $q->where($orgColumn, $this->orgaUin))
                    ->orderBy($column)
                    ->pluck($column, $relation->getOwnerKeyName())
                    ->all(),
            ];
        }

        return $fields;
    }

    public function save(): void
    {
        if ($this->leaveIfCompanyChanged()) {
            return;
        }

        // Empty values should be saved as NULL
        $this->form = array_map(fn($value) => $value === '' ? null : $value, $this->form);

        $rules = [
            'form.Strt_Dt' => ['nullable', 'date'],
            'form.End_Dt'  => ['nullable', 'date'],
        ];

        $attributes = [
            'form.Strt_Dt' => 'Start Date',
            'form.End_Dt'  => 'End Date',
        ];

        if (! empty($this->form['Strt_Dt'])) {
            $rules['form.End_Dt'][] = 'after_or_equal:form.Strt_Dt';
        }

        foreach ($this->fields as $column => $field) {
            $rules["form.{$column}"] = ['nullable', Rule::in(array_keys($field['options']))];
            $attributes["form.{$column}"] = $field['label'];
        }

        $validated = $this->validate($rules, [], $attributes)['form'];

        $application = $this->findApplication();
        $posting = $this->findPosting($application) ?? new EmplPosting;

        if (! $posting->exists) {
            $posting->forceFill(['CrBy' => $this->userUin]);
        }

        $posting->forceFill($validated + [
            'Aply_UIN' => $application->Aply_UIN,
            'Orga_UIN' => $this->orgaUin,
            'MoBy'     => $this->userUin,
        ])->save();

        session()->flash('success', 'Posting updated successfully.');

        $this->redirect(EmployeePosting::getUrl());
    }

    protected function findApplication(): HrMastJobAply
    {
        return HrMastJobAply::query()
            ->where('Orga_UIN', $this->orgaUin)
            ->whereNotNull('IS_Vf')
            ->findOrFail($this->applicationId);
    }

    protected function findPosting(HrMastJobAply $application): ?EmplPosting
    {
        return $application->posting()->where('Orga_UIN', $this->orgaUin)->first();
    }

    protected function lookup(string $key): BelongsTo
    {
        return (new EmplPosting)->{$key}();
    }
}
