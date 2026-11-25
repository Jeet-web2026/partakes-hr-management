<?php

namespace App\Filament\Candidatemanagement\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\EmplPosting;
use App\Models\HrMastJobAply;
use App\Models\MastStau;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class EmployeePosting extends Page
{
    use HasCompanyHeader;
    use WithPagination;

    // Organisation column of each lookup table (null = no org filter)
    private const ORG_COLUMNS = [
        'branchOffice'  => 'Orga_UIN',
        'designation'   => 'Org_UIN',
        'department'    => 'Orga_UIN',
        'project'       => 'Orga_UIN',
        'reportingHead' => null, // set this to your user table's org column
        'status'        => 'Orga_UIN',
    ];

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static ?string $navigationLabel = 'Employee Posting';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.candidatemanagement.pages.employee-posting';
    protected static ?string $slug = 'employee-posting';

    #[Locked]
    public ?string $userUin = null;

    #[Locked]
    public ?string $orgaUin = null;

    public string $search = '';
    public string $statusFilter = '';
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public int $perPage = 100;
    public string $sortField = 'Strt_Dt';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->userUin = Session::get('partaker_user_uin');
        $this->orgaUin = Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'statusFilter', 'fromDate', 'toDate', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['Strt_Dt', 'End_Dt'], true)) {
            return;
        }

        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('statusFilter', 'fromDate', 'toDate');
        $this->resetPage();
    }

    #[Computed]
    public function statuses(): array
    {
        return MastStau::query()
            ->where('Orga_UIN', $this->orgaUin)
            ->orderBy('Stau_Name')
            ->pluck('Stau_Name', 'Stau_UIN')
            ->all();
    }

    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        $sortField = in_array($this->sortField, ['Strt_Dt', 'End_Dt'], true) ? $this->sortField : 'Strt_Dt';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $perPage = in_array($this->perPage, [50, 100, 250], true) ? $this->perPage : 100;
        $searchable = ['branchOffice', 'designation', 'department', 'project', 'reportingHead'];

        $relations = ['posting' => fn($q) => $q->where('Orga_UIN', $this->orgaUin)];

        foreach (array_keys(EmplPosting::LOOKUPS) as $relation) {
            $relations[] = "posting.{$relation}";
        }

        $hasFilters = $this->statusFilter !== '' || $this->fromDate || $this->toDate;

        return HrMastJobAply::query()
            ->with($relations)
            ->where('Orga_UIN', $this->orgaUin)
            ->whereNotNull('IS_Vf')
            ->when($hasFilters, fn($query) => $query->whereHas('posting', fn($q) => $q
                ->where('Orga_UIN', $this->orgaUin)
                ->when($this->statusFilter !== '', fn($q) => $q->where('Stau_UIN', $this->statusFilter))
                ->when($this->fromDate, fn($q) => $q->whereDate('Strt_Dt', '>=', $this->fromDate))
                ->when($this->toDate, fn($q) => $q->whereDate('Strt_Dt', '<=', $this->toDate))))
            ->when(trim($this->search) !== '', function ($query) use ($searchable) {
                $term = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($searchable, $term) {
                    $q->where('FaNm', 'like', $term)
                        ->orWhere('MiNm', 'like', $term)
                        ->orWhere('SuNm', 'like', $term);

                    foreach ($searchable as $relation) {
                        $column = EmplPosting::LOOKUPS[$relation][1];

                        $q->orWhereHas("posting.{$relation}", fn($r) => $r->where($column, 'like', $term));
                    }
                });
            })
            ->orderBy(
                EmplPosting::query()
                    ->select($sortField)
                    ->whereColumn(
                        (new EmplPosting)->qualifyColumn('Aply_UIN'),
                        (new HrMastJobAply)->qualifyColumn('Aply_UIN')
                    )
                    ->where('Orga_UIN', $this->orgaUin)
                    ->limit(1),
                $direction
            )
            ->orderByDesc('Aply_UIN')
            ->paginate($perPage);
    }

    public function editPostingAction(): Action
    {
        return Action::make('editPosting')
            ->modalHeading(fn(array $arguments) => 'Edit Posting: ' . $this->findApplication($arguments)->full_name)
            ->modalSubmitActionLabel('Save')
            ->modalWidth(Width::ThreeExtraLarge)
            ->fillForm(function (array $arguments): array {
                $posting = $this->findPosting($this->findApplication($arguments));

                $data = [
                    'Strt_Dt' => $posting?->Strt_Dt?->format('Y-m-d'),
                    'End_Dt'  => $posting?->End_Dt?->format('Y-m-d'),
                ];

                foreach (array_keys(self::ORG_COLUMNS) as $key) {
                    $column = $this->lookup($key)->getForeignKeyName();
                    $data[$column] = $posting?->getAttribute($column);
                }

                return $data;
            })
            ->schema([
                Grid::make(2)->schema([
                    $this->lookupSelect('branchOffice', 'Branch Office'),
                    $this->lookupSelect('designation', 'Designation'),
                    $this->lookupSelect('department', 'Department'),
                    $this->lookupSelect('project', 'Project'),
                    $this->lookupSelect('reportingHead', 'Reporting Head'),
                    $this->lookupSelect('status', 'Status'),
                    DatePicker::make('Strt_Dt')->label('Start Date'),
                    DatePicker::make('End_Dt')->label('End Date')->afterOrEqual('Strt_Dt'),
                ]),
            ])
            ->action(function (array $data, array $arguments): void {
                $application = $this->findApplication($arguments);
                $posting = $this->findPosting($application) ?? new EmplPosting;

                if (! $posting->exists) {
                    $posting->forceFill(['CrBy' => $this->userUin]);
                }

                $posting->forceFill($data + [
                    'Aply_UIN' => $application->Aply_UIN,
                    'Orga_UIN' => $this->orgaUin,
                    'MoBy'     => $this->userUin,
                ])->save();

                unset($this->applications);

                Notification::make()->title('Posting saved')->success()->send();
            });
    }

    protected function findApplication(array $arguments): HrMastJobAply
    {
        return HrMastJobAply::query()
            ->where('Orga_UIN', $this->orgaUin)
            ->whereNotNull('IS_Vf')
            ->findOrFail($arguments['application'] ?? 0);
    }

    protected function findPosting(HrMastJobAply $application): ?EmplPosting
    {
        return $application->posting()->where('Orga_UIN', $this->orgaUin)->first();
    }

    protected function lookup(string $key): BelongsTo
    {
        return (new EmplPosting)->{$key}();
    }

    protected function lookupSelect(string $key, string $label): Select
    {
        return Select::make($this->lookup($key)->getForeignKeyName())
            ->label($label)
            ->options(function () use ($key): array {
                $relation = $this->lookup($key);
                $column = EmplPosting::LOOKUPS[$key][1];
                $orgColumn = self::ORG_COLUMNS[$key];

                return $relation->getRelated()->newQuery()
                    ->when($orgColumn, fn($q) => $q->where($orgColumn, $this->orgaUin))
                    ->orderBy($column)
                    ->pluck($column, $relation->getOwnerKeyName())
                    ->all();
            })
            ->searchable()
            ->native(false);
    }
}
