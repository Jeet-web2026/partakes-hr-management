@php
$seoTitle = 'Job Application | ' . config('app.name');
$seoDescription = 'Submit your job application. Provide your personal details, availability and attach your bio data in PDF format.';

$input = 'w-full rounded-lg border border-white/10 bg-[#0b1220] px-3 py-3 text-sm text-white placeholder:text-slate-500 transition focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500';
$inputError = 'w-full rounded-md border border-red-500/60 bg-[#0b1220] px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500';
$fieldClass = fn (string $name) => $errors->has($name) ? $inputError : $input;

$label = 'mb-1.5 block text-sm font-medium text-slate-300';
$error = 'mt-1.5 text-xs text-red-400';
$hint = 'mt-1.5 text-xs text-slate-500';
$sectionTitle = 'text-sm font-semibold uppercase tracking-wider text-blue-400';
$card = 'rounded-lg border border-white/10 bg-[#111a2b]';
$chip = 'inline-block min-w-24 rounded-md border border-white/10 bg-[#0b1220] px-4 py-2 text-center text-sm text-slate-300 transition hover:border-white/20 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-blue-300 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500';

$genders = ['male' => 'Male', 'female' => 'Female', 'other' => 'Other'];
$motherTongues = [
'english' => 'English', 'hindi' => 'Hindi', 'bengali' => 'Bengali',
'marathi' => 'Marathi', 'tamil' => 'Tamil', 'telugu' => 'Telugu',
'kannada' => 'Kannada', 'malayalam' => 'Malayalam', 'gujarati' => 'Gujarati',
'punjabi' => 'Punjabi', 'urdu' => 'Urdu', 'odia' => 'Odia', 'other' => 'Other',
];
$mobileTypes = ['Smart Phone' => 'Smart Phone', 'Featured Phone' => 'Feature Phone', 'Satellite Phone' => 'Satellite Phone'];
$mobileOs = ['Android' => 'Android', 'iOS' => 'iOS'];
$laptopOs = ['Android' => 'Android', 'iOS' => 'iOS', 'Windows' => 'Windows'];
$licenceTypes = ['Two Wheeler' => 'Two Wheeler', 'Four Wheeler' => 'Four Wheeler', 'Heavy Vehicle' => 'Heavy Vehicle'];
$relocationAreas = ['Within State' => 'Within State', 'Outside State' => 'Outside State', 'Outside Country' => 'Outside Country'];
$jobTypes = ['Full Time' => 'Full Time', 'Part Time' => 'Part Time', 'Both' => 'Both', 'Consultant' => 'Consultant'];
$yesNo = ['1' => 'Yes', '0' => 'No'];
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>

<body class="min-h-full bg-[#0b1220] font-sans text-slate-200 antialiased">
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">

        <div class="mb-6">
            <h1 class="text-xl font-semibold text-white">Job application</h1>
            <p class="mt-1 text-sm text-slate-400">
                Fields marked <span class="text-red-500" aria-hidden="true">*</span> are required.
            </p>
        </div>

        @if ($errors->any())
        <div role="alert" class="mb-6 rounded-md border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-300">
            <p class="font-semibold">There are some errors in your submission:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST"
            action="{{ route('application-management.store', ['token' => $token]) }}"
            enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            {{-- Personal details --}}
            <section aria-labelledby="personal-heading" class="{{ $card }}">
                <div class="border-b border-white/10 px-6 py-4">
                    <h2 id="personal-heading" class="{{ $sectionTitle }}">Personal details</h2>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div>
                        <label for="FaNm" class="{{ $label }}">First name <span class="text-red-500">*</span></label>
                        <input id="FaNm" name="FaNm" type="text" maxlength="100" autocomplete="given-name" required
                            value="{{ old('FaNm') }}" class="{{ $fieldClass('FaNm') }}"
                            @error('FaNm') aria-invalid="true" aria-describedby="FaNm-error" @enderror>
                        @error('FaNm') <p id="FaNm-error" class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="MiNm" class="{{ $label }}">Middle name</label>
                        <input id="MiNm" name="MiNm" type="text" maxlength="100" autocomplete="additional-name"
                            value="{{ old('MiNm') }}" class="{{ $fieldClass('MiNm') }}">
                    </div>

                    <div>
                        <label for="SuNm" class="{{ $label }}">Surname <span class="text-red-500">*</span></label>
                        <input id="SuNm" name="SuNm" type="text" maxlength="100" autocomplete="family-name" required
                            value="{{ old('SuNm') }}" class="{{ $fieldClass('SuNm') }}"
                            @error('SuNm') aria-invalid="true" aria-describedby="SuNm-error" @enderror>
                        @error('SuNm') <p id="SuNm-error" class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="Gend" class="{{ $label }}">Gender <span class="text-red-500">*</span></label>
                        <select id="Gend" name="Gend" required class="{{ $fieldClass('Gend') }}">
                            <option value="">Select gender</option>
                            @foreach ($genders as $value => $text)
                            <option value="{{ $value }}" @selected(old('Gend')===$value)>{{ $text }}</option>
                            @endforeach
                        </select>
                        @error('Gend') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="DOB" class="{{ $label }}">Date of birth <span class="text-red-500">*</span></label>
                        <input id="DOB" name="DOB" type="date" required max="{{ now()->toDateString() }}"
                            value="{{ old('DOB') }}" class="{{ $fieldClass('DOB') }}">
                        @error('DOB') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="MoTu" class="{{ $label }}">Mother tongue <span class="text-red-500">*</span></label>
                        <select id="MoTu" name="MoTu" required class="{{ $fieldClass('MoTu') }}">
                            <option value="">Select mother tongue</option>
                            @foreach ($motherTongues as $value => $text)
                            <option value="{{ $value }}" @selected(old('MoTu')===$value)>{{ $text }}</option>
                            @endforeach
                        </select>
                        @error('MoTu') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="Cutr_UIN" class="{{ $label }}">Country <span class="text-red-500">*</span></label>
                        <select id="Cutr_UIN" name="Cutr_UIN" required class="{{ $fieldClass('Cutr_UIN') }}">
                            <option value="">Select country</option>
                            @foreach ($countries as $country)
                            <option value="{{ $country->Admn_Cutr_Mast_UIN }}" @selected(old('Cutr_UIN')==$country->Admn_Cutr_Mast_UIN)>{{ $country->Name }}</option>
                            @endforeach
                        </select>
                        @error('Cutr_UIN') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="PinCod_UIN" class="{{ $label }}">Pincode of present location <span class="text-red-500">*</span></label>
                        <input id="PinCod_UIN" name="PinCod_UIN" type="text" maxlength="10" inputmode="numeric"
                            autocomplete="postal-code" placeholder="700024" required
                            value="{{ old('PinCod_UIN') }}" class="{{ $fieldClass('PinCod_UIN') }}"
                            aria-describedby="PinCod_UIN-hint">
                        <p id="PinCod_UIN-hint" class="{{ $hint }}">6 digits for India.</p>
                        @error('PinCod_UIN') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="Mo_No" class="{{ $label }}">Mobile number <span class="text-red-500">*</span></label>
                        <input id="Mo_No" name="Mo_No" type="tel" maxlength="15" autocomplete="tel" required
                            value="{{ old('Mo_No') }}" class="{{ $fieldClass('Mo_No') }}">
                        @error('Mo_No') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="Emai" class="{{ $label }}">Personal email address <span class="text-red-500">*</span></label>
                        <input id="Emai" name="Emai" type="email" maxlength="255" autocomplete="email" required
                            value="{{ old('Emai') }}" class="{{ $fieldClass('Emai') }}">
                        @error('Emai') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Devices, licence and relocation --}}
            <section aria-labelledby="devices-heading" class="{{ $card }}">
                <div class="border-b border-white/10 px-6 py-4">
                    <h2 id="devices-heading" class="{{ $sectionTitle }}">Devices, licence and relocation</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">

                    {{-- Own mobile --}}
                    <fieldset class="space-y-3">
                        <legend class="{{ $label }}">Do you have your own mobile? <span class="text-red-500">*</span></legend>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($yesNo as $value => $text)
                            <label class="cursor-pointer">
                                <input type="radio" name="Own_Mo" value="{{ $value }}" required
                                    @checked(old('Own_Mo')===$value) class="peer sr-only">
                                <span class="{{ $chip }}">{{ $text }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('Own_Mo') <p class="{{ $error }}">{{ $message }}</p> @enderror

                        <div data-show-when="Own_Mo" data-show-value="1" class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="Mo_Type" class="{{ $label }}">Type of mobile <span class="text-red-500">*</span></label>
                                <select id="Mo_Type" name="Mo_Type" required class="{{ $fieldClass('Mo_Type') }}">
                                    <option value="">Select type</option>
                                    @foreach ($mobileTypes as $value => $text)
                                    <option value="{{ $value }}" @selected(old('Mo_Type')===$value)>{{ $text }}</option>
                                    @endforeach
                                </select>
                                @error('Mo_Type') <p class="{{ $error }}">{{ $message }}</p> @enderror
                            </div>

                            <div data-show-when="Mo_Type" data-show-value="smart_phone">
                                <label for="MO_OS" class="{{ $label }}">Operating system <span class="text-red-500">*</span></label>
                                <select id="MO_OS" name="MO_OS" required class="{{ $fieldClass('MO_OS') }}">
                                    <option value="">Select OS</option>
                                    @foreach ($mobileOs as $value => $text)
                                    <option value="{{ $value }}" @selected(old('MO_OS')===$value)>{{ $text }}</option>
                                    @endforeach
                                </select>
                                @error('MO_OS') <p class="{{ $error }}">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Own laptop --}}
                    <fieldset class="space-y-3">
                        <legend class="{{ $label }}">Do you have your own laptop for personal use in running condition? <span class="text-red-500">*</span></legend>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($yesNo as $value => $text)
                            <label class="cursor-pointer">
                                <input type="radio" name="Own_Lptp" value="{{ $value }}" required
                                    @checked(old('Own_Lptp')===$value) class="peer sr-only">
                                <span class="{{ $chip }}">{{ $text }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('Own_Lptp') <p class="{{ $error }}">{{ $message }}</p> @enderror

                        <div data-show-when="Own_Lptp" data-show-value="1" class="sm:w-1/2">
                            <label for="Lptp_OS" class="{{ $label }}">Laptop OS <span class="text-red-500">*</span></label>
                            <select id="Lptp_OS" name="Lptp_OS" required class="{{ $fieldClass('Lptp_OS') }}">
                                <option value="">Select OS</option>
                                @foreach ($laptopOs as $value => $text)
                                <option value="{{ $value }}" @selected(old('Lptp_OS')===$value)>{{ $text }}</option>
                                @endforeach
                            </select>
                            @error('Lptp_OS') <p class="{{ $error }}">{{ $message }}</p> @enderror
                        </div>
                    </fieldset>

                    {{-- Driving licence --}}
                    <fieldset class="space-y-3">
                        <legend class="{{ $label }}">Do you have a valid driving licence? <span class="text-red-500">*</span></legend>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($yesNo as $value => $text)
                            <label class="cursor-pointer">
                                <input type="radio" name="Drvi_Lisc" value="{{ $value }}" required
                                    @checked(old('Drvi_Lisc')===$value) class="peer sr-only">
                                <span class="{{ $chip }}">{{ $text }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('Drvi_Lisc') <p class="{{ $error }}">{{ $message }}</p> @enderror

                        <div data-show-when="Drvi_Lisc" data-show-value="1" class="sm:w-1/2">
                            <label for="Vcle_Type_Lisc" class="{{ $label }}">Vehicle type licence <span class="text-red-500">*</span></label>
                            <select id="Vcle_Type_Lisc" name="Vcle_Type_Lisc" required class="{{ $fieldClass('Vcle_Type_Lisc') }}">
                                <option value="">Select licence type</option>
                                @foreach ($licenceTypes as $value => $text)
                                <option value="{{ $value }}" @selected(old('Vcle_Type_Lisc')===$value)>{{ $text }}</option>
                                @endforeach
                            </select>
                            @error('Vcle_Type_Lisc') <p class="{{ $error }}">{{ $message }}</p> @enderror
                        </div>
                    </fieldset>

                    {{-- Relocation --}}
                    <fieldset class="space-y-3">
                        <legend class="{{ $label }}">Are you ready to relocate for your job? <span class="text-red-500">*</span></legend>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($yesNo as $value => $text)
                            <label class="cursor-pointer">
                                <input type="radio" name="Rlca" value="{{ $value }}" required
                                    @checked(old('Rlca')===$value) class="peer sr-only">
                                <span class="{{ $chip }}">{{ $text }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('Rlca') <p class="{{ $error }}">{{ $message }}</p> @enderror

                        <div data-show-when="Rlca" data-show-value="1" class="sm:w-1/2">
                            <label for="Rlca_Area" class="{{ $label }}">Relocation area <span class="text-red-500">*</span></label>
                            <select id="Rlca_Area" name="Rlca_Area" required class="{{ $fieldClass('Rlca_Area') }}">
                                <option value="">Select area</option>
                                @foreach ($relocationAreas as $value => $text)
                                <option value="{{ $value }}" @selected(old('Rlca_Area')===$value)>{{ $text }}</option>
                                @endforeach
                            </select>
                            @error('Rlca_Area') <p class="{{ $error }}">{{ $message }}</p> @enderror
                        </div>
                    </fieldset>

                </div>
            </section>

            {{-- Job preferences and documents --}}
            <section aria-labelledby="job-heading" class="{{ $card }}">
                <div class="border-b border-white/10 px-6 py-4">
                    <h2 id="job-heading" class="{{ $sectionTitle }}">Job preferences and documents</h2>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="Job_Intr" class="{{ $label }}">Interested in job <span class="text-red-500">*</span></label>
                        <input id="Job_Intr" name="Job_Intr" type="text" maxlength="255" placeholder="e.g. Backend Developer" required
                            value="{{ old('Job_Intr') }}" class="{{ $fieldClass('Job_Intr') }}">
                        @error('Job_Intr') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="Job_Type" class="{{ $label }}">Job type <span class="text-red-500">*</span></label>
                        <select id="Job_Type" name="Job_Type" required class="{{ $fieldClass('Job_Type') }}">
                            <option value="">Select job type</option>
                            @foreach ($jobTypes as $value => $text)
                            <option value="{{ $value }}" @selected(old('Job_Type')===$value)>{{ $text }}</option>
                            @endforeach
                        </select>
                        @error('Job_Type') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="Bio_Data_Path" class="{{ $label }}">Attached bio data <span class="text-red-500">*</span></label>
                        <input id="Bio_Data_Path" name="Bio_Data_Path" type="file" accept="application/pdf,.pdf" required
                            aria-describedby="Bio_Data_Path-hint"
                            class="block w-full cursor-pointer rounded-md border border-white/10 bg-[#0b1220] text-sm text-slate-300 file:mr-3 file:cursor-pointer file:border-0 file:bg-blue-600 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-white hover:file:bg-blue-700">
                        <p id="Bio_Data_Path-hint" class="{{ $hint }}">PDF only, up to 2 MB.</p>
                        @error('Bio_Data_Path') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Submit bar --}}
            <div class="flex flex-col gap-4 rounded-lg border border-white/10 bg-[#111a2b] p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-white">Ready to submit?</p>
                    <p class="mt-1 text-xs text-slate-400">Check your details before sending. Your bio data is stored privately.</p>
                </div>

                <button type="submit"
                    class="inline-flex shrink-0 items-center justify-center rounded-md bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus-visible:ring-offset-[#111a2b] disabled:cursor-not-allowed disabled:opacity-60">
                    Submit application
                </button>
            </div>
        </form>
    </main>

    <script>
        (function() {
            function valueOf(name) {
                var checked = document.querySelector('input[name="' + name + '"]:checked');
                if (checked) return checked.value;

                var select = document.querySelector('select[name="' + name + '"]');
                return select ? select.value : '';
            }

            function syncConditionalFields() {
                document.querySelectorAll('[data-show-when]').forEach(function(wrap) {
                    var parent = wrap.parentElement.closest('[data-show-when]');
                    var parentShown = !parent || !parent.classList.contains('hidden');
                    var show = parentShown && valueOf(wrap.dataset.showWhen) === wrap.dataset.showValue;

                    wrap.classList.toggle('hidden', !show);
                    wrap.querySelectorAll('input, select').forEach(function(field) {
                        field.disabled = !show;
                    });
                });
            }

            document.querySelectorAll('input[type="radio"], select').forEach(function(el) {
                el.addEventListener('change', syncConditionalFields);
            });

            syncConditionalFields();
        })();
    </script>
</body>

</html>