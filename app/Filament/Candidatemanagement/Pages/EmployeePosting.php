<?php

namespace App\Filament\Candidatemanagement\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Models\EmplPosting;
use App\Models\MastStau;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class EmployeePosting extends Page
{
    use HasCompanyHeader;
    use WithPagination;

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
    public function postings(): LengthAwarePaginator
    {
        $sortField = in_array($this->sortField, ['Strt_Dt', 'End_Dt'], true) ? $this->sortField : 'Strt_Dt';
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';
        $perPage = in_array($this->perPage, [50, 100, 250], true) ? $this->perPage : 100;
        $searchable = ['employee', 'branchOffice', 'designation', 'department', 'project', 'reportingHead'];

        return EmplPosting::query()
            ->with(array_keys(EmplPosting::LOOKUPS))
            ->where('Orga_UIN', $this->orgaUin)
            ->when($this->statusFilter !== '', fn($q) => $q->where('Stau_UIN', $this->statusFilter))
            ->when($this->fromDate, fn($q) => $q->whereDate('Strt_Dt', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('Strt_Dt', '<=', $this->toDate))
            ->when(trim($this->search) !== '', function ($query) use ($searchable) {
                $term = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($searchable, $term) {
                    foreach ($searchable as $relation) {
                        $column = EmplPosting::LOOKUPS[$relation][1];

                        $q->orWhereHas($relation, fn($r) => $r->where($column, 'like', $term));
                    }
                });
            })
            ->orderBy($sortField, $direction)
            ->orderByDesc('Post_UIN')
            ->paginate($perPage);
    }
}
