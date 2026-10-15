<?php

namespace App\Http\Requests;

use App\Rules\ValidDocumentUpload;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDraftAgreementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id'         => ['required'],
            'agreement_category' => ['required'],
            'agreement_type'     => ['required'],
            'agreement_name'     => ['required', 'string', 'max:255'],
            'draft_version_no'   => ['nullable', 'string', 'max:50'],
            'approved_by'        => ['nullable', 'string', 'max:255'],
            'approved_date'      => ['nullable', 'date'],
            'draft_file'         => [
                'nullable',
                new ValidDocumentUpload,
            ],
            'checklist_file'     => [
                'nullable',
                new ValidDocumentUpload,
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_id'         => 'company',
            'agreement_category' => 'agreement category',
            'agreement_type'     => 'agreement type',
            'agreement_name'     => 'agreement name',
            'draft_version_no'   => 'draft version no',
            'approved_by'        => 'approved by',
            'approved_date'      => 'approved date',
            'draft_file'         => 'draft file',
            'checklist_file'     => 'checklist file',
        ];
    }
}
