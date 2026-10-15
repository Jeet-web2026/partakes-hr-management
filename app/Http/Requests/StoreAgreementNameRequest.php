<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgreementNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agreement_category' => ['required', 'integer', 'exists:tbl_mast_agmt_catg,Agmt_Catg_UIN'],
            'agreement_type' => ['required', 'integer', 'exists:tbl_mast_agmt_type,Agmt_Type_UIN'],
            'agmt_Name'     => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'integer', 'exists:tbl_mast_orga,Orga_UIN']
        ];
    }
}
