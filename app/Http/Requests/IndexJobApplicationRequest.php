<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'     => ['nullable', 'string', 'max:100'],
            'company_id' => ['nullable', 'integer', 'min:1'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page'   => ['nullable', 'integer', Rule::in([50, 100, 250])],
        ];
    }

    public function messages(): array
    {
        return [
            'job_type.in'            => 'Select a valid job type.',
            'date_to.after_or_equal' => 'The end date must be on or after the start date.',
            'per_page.in'            => 'Rows per page must be 50, 100 or 250.',
        ];
    }

    public function attributes(): array
    {
        return [
            'company_id' => 'company',
            'job_type'   => 'job type',
            'date_from'  => 'start date',
            'date_to'    => 'end date',
            'per_page'   => 'rows per page',
        ];
    }
}
