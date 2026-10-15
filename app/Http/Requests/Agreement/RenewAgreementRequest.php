<?php

namespace App\Http\Requests\Agreement;

use App\Rules\ValidAgreementRefNo;
use App\Rules\ValidDocumentUpload;
use Illuminate\Foundation\Http\FormRequest;

class RenewAgreementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'agreement_category' => 'required|exists:tbl_mast_agmt_catg,Agmt_Catg_UIN',

            'agreement_type' => 'required|exists:tbl_mast_agmt_type,Agmt_Type_UIN',

            'agreement_name' => 'required|string|max:255',

            'file_no' => 'nullable|string|max:4',

            'agreement_date' => 'required|date|before_or_equal:today',

            'expiry_date' => 'nullable|date|after_or_equal:agreement_date',

            'signing_date' => 'nullable|date|after_or_equal:agreement_date',

            'notary_date' => 'nullable|date|before_or_equal:expiry_date',

            'agreement_version' => 'nullable|string|max:50',

            'drafted_by' => 'nullable|string|max:255',

            'jurisdiction' => 'nullable|string|max:255',

            'attachment' =>  [
                'nullable',
                new ValidDocumentUpload,
            ],

            'remarks' => 'nullable|string',

            'parties'              => [
                'required',
                'array',
                'min:2',
                function ($attribute, $value, $fail) {
                    $primaryCount = collect($value)->filter(
                        fn($party) =>
                        filter_var($party['is_primary'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    )->count();

                    if ($primaryCount === 0) {
                        $fail('At least one party must be marked as VS.');
                    }
                },
            ],
            'parties.*.role'       => 'required|string|max:50',
            'parties.*.org_name'   => 'required|string|max:255',
            'parties.*.signatory'  => 'nullable|string|max:255',
            'parties.*.is_primary' => 'nullable',
            'deposit_amount' => 'nullable|numeric',
            'ref_no' => [
                'required',
                new ValidAgreementRefNo(
                    table: 'tbl_tran_agmt',
                    companyId: $this->input('company_id'),
                ),
            ],
            'agreement_signed' => 'nullable',
            'signed_document_received_date' => 'nullable',
            'agreement_received_type' => 'required'
        ];
    }

    public function messages(): array
    {
        return [

            'agreement_category.required' => 'Please select an agreement category.',

            'agreement_category.exists' => 'The selected agreement category is invalid.',

            'agreement_type.required' => 'Please select an agreement type.',

            'agreement_type.exists' => 'The selected agreement type is invalid.',

            'agreement_name.required' => 'Agreement name is required.',

            'agreement_name.max' => 'Agreement name may not exceed 255 characters.',

            'expiry_date.after_or_equal' => 'Expiry date must be after or equal to the agreement date.',

            'attachment.mimes' => 'Only PDF files are allowed.',

            'attachment.max' => 'The PDF must not exceed 20 MB.',

            'parties.required' => 'At least two parties are required.',
            'parties.min'      => 'At least two parties are required.',

            'parties.*.role.required'      => 'Party role is required.',
            'parties.*.org_name.required'  => 'Organization name is required.',
            'parties.*.signatory.required' => 'Authorised signatory is required.',
        ];
    }
}
