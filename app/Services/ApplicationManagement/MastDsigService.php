<?php

namespace App\Services\ApplicationManagement;

use App\Models\MastDsig;
use App\Repositories\Contracts\MastDsigRepositoryInterface;
use Illuminate\Support\Collection;

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

    public function create(string $orgUin, string $name, ?string $description, int $status, string $userUin): MastDsig
    {
        return $this->designations->create([
            'Dsig_Name' => $name,
            'Dsig_Desp' => $description,
            'Dsig_Stau' => $status,
            'Org_UIN' => (int) $orgUin,
            'CrBy' => (int) $userUin,
            'MoBy' => (int) $userUin,
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
        return $this->designations->delete($designation->getKey());
    }
}
