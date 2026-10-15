<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidAgreementRefNo implements ValidationRule
{
    public function __construct(
        private readonly string $table,
        private readonly int|string $companyId,
        private readonly ?int $ignoreId = null,
        private readonly string $idColumn = 'id',
        private readonly string $refColumn = 'Agmt_Ref_No',
        private readonly string $companyColumn = 'Orga_UIN',
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^[A-Za-z0-9#\/]+$/', (string) $value)) {
            $fail('The :attribute may only contain letters, numbers, # and /.');
            return;
        }

        $query = DB::table($this->table)
            ->where($this->companyColumn, $this->companyId)
            ->where($this->refColumn, $value);

        if ($this->ignoreId) {
            $query->where($this->idColumn, '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('This :attribute is already used by another agreement in your company.');
        }
    }
}
