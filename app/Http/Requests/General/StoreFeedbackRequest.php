<?php

namespace App\Http\Requests\General;

use App\Models\PinCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_load_speed'     => ['required', 'in:very_good,good,ok,poor,very_poor'],
            'page_design'         => ['required', 'in:very_good,good,ok,poor,very_poor'],
            'ease_of_use'         => ['required', 'in:very_good,good,ok,poor,very_poor'],
            'information_clarity' => ['required', 'in:very_good,good,ok,poor,very_poor'],
            'overall_experience'  => ['required', 'in:very_good,good,ok,poor,very_poor'],
            'feedback_type'       => ['required', 'string', 'max:255'],
            'comments'            => ['nullable', 'string'],
            'pincode'             => [
                'required',
                Rule::when(
                    $this->input('country_uin') === "101107",
                    [Rule::exists((new PinCode())->getTable(), 'pin_code_No')]
                ),
            ],
            'orga_uin'  => ['required'],
            'attachment' => ['nullable', 'mimes:pdf,jpg,jpeg,png', 'max:1000'],
            'page_uin'  => ['required', 'string', 'max:255'],
            'Lokn_for'  => ['required', 'string', 'max:255'],
            'Rcmd_Page' => ['required', 'string', 'max:255'],
            'Cnta_Us'   => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'page_uin.required'             => 'Please select a page name to review.',
            'page_load_speed.required'      => 'Please rate the page load speed.',
            'page_design.required'          => 'Please rate the page design.',
            'ease_of_use.required'          => 'Please rate the ease of use.',
            'information_clarity.required'  => 'Please rate the information clarity.',
            'overall_experience.required'   => 'Please rate the overall experience.',
            'Lokn_for.required'             => 'Please select an option.',
            'Rcmd_Page.required'            => 'Please select an option.',
            'feedback_type.required'        => 'Please select a feedback type.',
            'pincode.required'              => 'Please enter your 6-digit pincode.',
            'pincode.exists'                => 'The given pincode is invalid.',
            'Cnta_Us.required'              => 'Please select an option.',
        ];
    }
}
