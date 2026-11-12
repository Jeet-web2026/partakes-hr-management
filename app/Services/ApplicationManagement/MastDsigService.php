<?php

namespace App\Services\ApplicationManagement;

use App\Models\HrMastJobAply;
use App\Models\MastDsig;
use App\Repositories\Contracts\MastDsigRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class MastDsigService
{
    public const STATUS_ACTIVE = 100201;
    public const STATUS_INACTIVE = 100202;

    public function __construct(
        protected MastDsigRepositoryInterface $designations,
    ) {}

    /** @return array<int, string> */
    public function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public function listForOrganisation(string $orgUin, string $search = ''): Collection
    {
        return $this->designations->getByOrganisation((int) $orgUin, $search ?: null);
    }

    /** Returns null if the record does not belong to this organisation. */
    public function find(string $orgUin, string $dsigUin): ?MastDsig
    {
        $designation = $this->designations->findById((int) $dsigUin);

        return $designation && (int) $designation->Org_UIN === (int) $orgUin
            ? $designation
            : null;
    }

    public function nameExists(string $orgUin, string $name, ?string $ignoreDsigUin = null): bool
    {
        return $this->designations->nameExists(
            (int) $orgUin,
            $name,
            $ignoreDsigUin ? (int) $ignoreDsigUin : null,
        );
    }

    /**
     * Parent dropdown options: [Dsig_UIN => Dsig_Name] for the selected type in this organisation.
     * "Primary Designation" (0) is added by the page. While editing, the record itself and all of
     * its descendants are removed so a designation can never become its own ancestor.
     *
     * @return array<int, string>
     */
    public function parentOptions(string $orgUin, string $designationType, ?string $exceptDsigUin = null): array
    {
        $exclude = $exceptDsigUin
            ? array_merge([(int) $exceptDsigUin], $this->descendantIds((int) $exceptDsigUin))
            : [];

        return MastDsig::query()
            ->where('Org_UIN', (int) $orgUin)
            ->where('Dsig_Type', $designationType)
            ->when($exclude, fn($q) => $q->whereNotIn('Dsig_UIN', $exclude))
            ->orderBy('Dsig_Name')
            ->pluck('Dsig_Name', 'Dsig_UIN')
            ->all();
    }

    /** @return int[] */
    protected function descendantIds(int $dsigUin): array
    {
        $all = [];
        $frontier = [$dsigUin];

        while ($frontier) {
            $next = MastDsig::whereIn('Parn_Dsig_UIN', $frontier)->pluck('Dsig_UIN')->all();
            $frontier = array_values(array_diff($next, $all, [$dsigUin]));
            $all = array_merge($all, $frontier);
        }

        return $all;
    }

    public function create(
        string $orgUin,
        string $name,
        ?string $description,
        int $status,
        string $userUin,
        string $designationType,
        int $parentUin = 0,
    ): MastDsig {
        return $this->designations->create([
            'Dsig_Name' => $name,
            'Dsig_Desp' => $description,
            'Dsig_Stau' => $status,
            'Org_UIN' => (int) $orgUin,
            'CrBy' => (int) $userUin,
            'MoBy' => (int) $userUin,
            'Dsig_Type' => $designationType,
            'Parn_Dsig_UIN' => $parentUin,
        ]);
    }

    public function update(MastDsig $designation, array $data, string $userUin): MastDsig
    {
        return $this->designations->update($designation->getKey(), [
            ...$data,
            'MoBy' => (int) $userUin,
        ]);
    }

    public function delete(MastDsig $designation): bool
    {
        if (MastDsig::where('Parn_Dsig_UIN', $designation->getKey())->exists()) {
            throw ValidationException::withMessages([
                'designation' => sprintf(
                    'Cannot delete "%s": it is the parent of other designations.',
                    $designation->Dsig_Name
                ),
            ]);
        }

        $usedBy = HrMastJobAply::query()
            ->where('Job_Intr', $designation->getKey())
            ->count();

        if ($usedBy > 0) {
            throw ValidationException::withMessages([
                'designation' => sprintf(
                    'Cannot delete "%s": %d candidate application%s use this designation.',
                    $designation->Dsig_Name,
                    $usedBy,
                    $usedBy === 1 ? '' : 's'
                ),
            ]);
        }

        return $this->designations->delete($designation->getKey());
    }

    public function getActiveByOrganisation(string $orgUin, string $search = ''): Collection
    {
        return $this->designations->getActiveByOrganisation((int) $orgUin, $search ?: null);
    }

    public function paginateForOrganisation(string $orgaUin, string $search = '', int $perPage = 50): LengthAwarePaginator
    {
        $page = $this->designations->paginateByOrganisation($orgaUin, $search, $perPage);

        $page->getCollection()->load('parent');

        return $page;
    }
}
