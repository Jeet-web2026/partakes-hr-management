<?php

namespace App\Http\Requests;

use App\Models\CountryMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreJobApplicationRequest extends FormRequest
{
    public const GENDERS         = ['male', 'female', 'other'];
    public const MOTHER_TONGUES  = ['english', 'hindi', 'bengali', 'marathi', 'tamil', 'telugu', 'kannada', 'malayalam', 'gujarati', 'punjabi', 'urdu', 'odia', 'other'];
    public const MOBILE_TYPES    = ['Smart Phone', 'Featured Phone', 'Satellite Phone'];
    public const MOBILE_OS       = ['Android', 'iOS'];
    public const LAPTOP_OS       = ['Android', 'iOS', 'Windows'];
    public const LICENCE_TYPES   = ['Two Wheeler', 'Four Wheeler', 'Heavy Vehicle'];
    public const RELOCATION_AREA = ['Within State', 'Outside State', 'Outside Country'];
    public const JOB_TYPES       = ['Full Time', 'Part Time', 'Both', 'Consultant'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'FaNm'  => ['required', 'string', 'max:100'],
            'MiNm'  => ['nullable', 'string', 'max:100'],
            'SuNm'  => ['required', 'string', 'max:100'],
            'Gend'  => ['required', Rule::in(self::GENDERS)],
            'DOB'   => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'MoTu'  => ['required', Rule::in(self::MOTHER_TONGUES)],

            'Cutr_UIN'   => ['required', Rule::exists((new CountryMaster)->getTable(), 'Admn_Cutr_Mast_UIN')],
            'PinCod_UIN' => ['required', 'string', 'max:10'],
            'Mo_No'      => ['required', 'string', 'max:15', 'regex:/^[0-9+\-\s()]{7,15}$/'],
            'Emai'       => ['required', 'email', 'max:255'],

            'Own_Mo'  => ['required', Rule::in(['0', '1'])],
            'Mo_Type' => ['required_if:Own_Mo,1', 'nullable', Rule::in(self::MOBILE_TYPES)],
            'MO_OS'   => ['required_if:Mo_Type,Smart Phone', 'nullable', Rule::in(self::MOBILE_OS)],

            'Own_Lptp' => ['required', Rule::in(['0', '1'])],
            'Lptp_OS'  => ['required_if:Own_Lptp,1', 'nullable', Rule::in(self::LAPTOP_OS)],

            'Drvi_Lisc'      => ['required', Rule::in(['0', '1'])],
            'Vcle_Type_Lisc' => ['required_if:Drvi_Lisc,1', 'nullable', Rule::in(self::LICENCE_TYPES)],

            'Rlca'      => ['required', Rule::in(['0', '1'])],
            'Rlca_Area' => ['required_if:Rlca,1', 'nullable', Rule::in(self::RELOCATION_AREA)],

            'Job_Intr' => ['required', 'string', 'max:255'],
            'Job_Type' => ['required', Rule::in(self::JOB_TYPES)],

            'Bio_Data_Path' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'Distr' => ['required', 'string'],
            'State' => ['required', 'string']
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->isIndia() || $this->filled('PinCod_UIN') === false) {
                return;
            }

            if (! preg_match('/^\d{6}$/', (string) $this->input('PinCod_UIN'))) {
                $validator->errors()->add('PinCod_UIN', 'The pincode must be 6 digits for India.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'MO_OS.required_if'          => 'Select the operating system for your smart phone.',
            'Lptp_OS.required_if'        => 'Select the operating system for your laptop.',
            'Mo_Type.required_if'        => 'Select the type of your mobile phone.',
            'Vcle_Type_Lisc.required_if' => 'Select the vehicle licence type.',
            'Rlca_Area.required_if'      => 'Select where you are willing to relocate.',
            'Bio_Data_Path.mimes'        => 'The bio data must be a PDF file.',
            'Bio_Data_Path.max'          => 'The bio data must not be larger than 2 MB.',
            'Mo_No.regex'                => 'Enter a valid mobile number.',
        ];
    }

    private function isIndia(): bool
    {
        $countryId = $this->input('Cutr_UIN');

        if (! $countryId) {
            return false;
        }

        return CountryMaster::where('Admn_Cutr_Mast_UIN', $countryId)->value('Name') === 'India';
    }

    public function attributes(): array
    {
        return [
            'Own_Mo'         => 'own mobile',
            'Mo_Type'        => 'mobile type',
            'MO_OS'          => 'mobile operating system',
            'Own_Lptp'       => 'own laptop',
            'Lptp_OS'        => 'laptop operating system',
            'Drvi_Lisc'      => 'driving licence',
            'Vcle_Type_Lisc' => 'vehicle licence type',
            'Rlca'           => 'relocation answer',
            'Rlca_Area'      => 'relocation area',
            'Job_Intr'       => 'job interest',
            'Job_Type'       => 'job type',
            'Bio_Data_Path'  => 'bio data',
            'Cutr_UIN'       => 'country',
            'PinCod_UIN'     => 'pincode',
            'Mo_No'          => 'mobile number',
            'Emai'           => 'email address',
        ];
    }
}
