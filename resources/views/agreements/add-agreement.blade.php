<x-base-component tailwind="true" jQuery="true" subheading="Add Agreement" metadata="Add Agreement — Renewal Tracker" metadescription="description" bIcons="true">
    <x-secondary-header backurl="/dashboard/agreements" :companyInformation="$companyInformation" :userData="$userData" title="Agreements" backurlforSettings="https://partakers.in/dashboard" />

    <main class="mx-auto max-w-5xl px-6 py-8">

        <x-globalresponse />

        <form class="space-y-6" action="{{ route('agreements.store') }}"
            method="POST"
            enctype="multipart/form-data" id="agreementForm">
            @csrf

            <input type="hidden" name="company_id" value="{{ request('company') }}">

            <!-- Classification -->
            <section class="card p-6">
                <h2 class="section-title">Agreement Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Agreement Category -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Category</label>

                        <select class="input"
                            name="agreement_category"
                            id="agreementCategory">
                            @php
                                $allChildren = $agreementCategories
                                    ->flatMap(function ($parent) {
                                        return $parent->children->map(function ($child) use ($parent) {
                                            $child->parentName = $parent->Agmt_Catg_Name;
                                            return $child;
                                        });
                                    })
                                    ->sortBy('Agmt_Catg_Name'); // ascending, a to z
                            @endphp

                            @foreach($allChildren as $child)
                            <option value="{{ $child->Agmt_Catg_UIN }}" class="bg-slate-900"
                                {{ old('agreement_category') == $child->Agmt_Catg_UIN ? 'selected' : '' }}>
                                {{ $child->Agmt_Catg_Name }} &lt; {{ $child->parentName }}
                            </option>
                            @endforeach

                        </select>

                        @error('agreement_category')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Type -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Type</label>

                        <select id="agreementType"
                            class="input"
                            name="agreement_type">

                        </select>
                        <input type="hidden" id="currentAgreementType" 
                                    value="{{ old('agreement_type', $agreement->Agmt_Type_UIN ?? '') }}">

                        @error('agreement_type')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Name -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="label">Agreement Name</label>

                        <input
                            type="text"
                            class="input"
                            name="agreement_name"
                            value="{{ old('agreement_name') }}"
                            placeholder="e.g. Courier Service Agreement — FY26">

                        @error('agreement_name')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            <!-- Parties -->
            <section class="card p-6">
                <h2 class="section-title">Agreement Parties</h2>

                <div class="space-y-3" id="parties">

                    <div class="grid grid-cols-[90px_1fr_1fr_80px] gap-3 px-1 text-xs uppercase tracking-wider text-slate-400">
                        <div>Role</div>
                        <div>Organization Name</div>
                        <div>Authorised Signatory</div>
                        <div class="text-right">Vs</div>
                    </div>

                    @php
                    $partiesData = old('parties', [
                    ['role' => '1st Party', 'org_name' => '', 'signatory' => '', 'is_primary' => true],
                    ['role' => '2nd Party', 'org_name' => '', 'signatory' => '', 'is_primary' => false],
                    ]);
                    @endphp

                    @foreach ($partiesData as $index => $party)
                    <div class="party-row grid grid-cols-[90px_1fr_1fr_80px] gap-3 items-center" data-index="{{ $index }}">
                        <div class="text-sm font-medium text-slate-400">
                            {{ $party['role'] ?? ($index == 0 ? '1st Party' : ($index == 1 ? '2nd Party' : $index + 1 . 'th Party')) }}
                            <input type="hidden" name="parties[{{ $index }}][role]"
                                value="{{ $party['role'] ?? '' }}">
                        </div>

                        <div>
                            <input
                                class="input"
                                name="parties[{{ $index }}][org_name]"
                                value="{{ $party['org_name'] ?? '' }}"
                                placeholder="Organization">

                            @error("parties.$index.org_name")
                            <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>

                        <div>
                            <input
                                class="input"
                                name="parties[{{ $index }}][signatory]"
                                value="{{ $party['signatory'] ?? '' }}"
                                placeholder="Authorised Signatory">

                            @error("parties.$index.signatory")
                            <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end">
                            <input
                                type="checkbox"
                                class="checkbox vs-toggle"
                                name="parties[{{ $index }}][is_primary]"
                                value="1">
                            @if ($index > 1)
                            <button type="button" class="btn btn-outline-danger" onclick="removeParty(this)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>

                    </div>
                    @endforeach

                </div>

                <div>
                    @error('parties')
                    <small class="text-red-500">{{ $message }}</small>
                    @enderror
                </div>

                <button type="button" class="btn btn-outline mt-3" onclick="addParty()">
                    <i class="bi bi-plus-lg"></i> Add More Party
                </button>
            </section>

            <!-- Dates & Version -->
            <section class="card p-6">
                <h2 class="section-title">Agreement Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Agreement Date -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Date</label>
                        <input
                            type="date"
                            class="input"
                            name="agreement_date"
                            value="{{ old('agreement_date') }}"
                            max="{{ date('Y-m-d') }}">

                        @error('agreement_date')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiry Date -->
                    <div class="space-y-1.5">
                        <label class="label">Expiry Date</label>
                        <input
                            type="date"
                            class="input"
                            name="expiry_date"
                            id="expiry_date"
                            value="{{ old('expiry_date') }}">

                        @error('expiry_date')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Signing Date -->
                    <div class="space-y-1.5">
                        <label class="label">Signing Date</label>
                        <input
                            type="date"
                            class="input"
                            name="signing_date"
                            value="{{ old('signing_date') }}">

                        @error('signing_date')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notary Date -->
                    <div class="space-y-1.5">
                        <label class="label">Notary Date</label>
                        <input
                            type="date"
                            class="input"
                            name="notary_date"
                            value="{{ old('notary_date') }}">

                        @error('notary_date')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Version -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Version</label>
                        <input
                            type="text"
                            class="input"
                            name="agreement_version"
                            value="{{ old('agreement_version') }}"
                            placeholder="e.g. v1.0">

                        @error('agreement_version')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Drafted By -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Drafted By</label>
                        <input
                            type="text"
                            class="input"
                            name="drafted_by"
                            value="{{ old('drafted_by') }}"
                            placeholder="Person / Team">

                        @error('drafted_by')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    

                </div>
                <div class="flex flex-col lg:flex-row items-center gap-4 mt-3">
                        <!-- Jurisdiction -->
                        <div class="space-y-1.5 md:col-span-2 lg:w-1/2 w-full">
                            <label class="label">Jurisdiction</label>
                            <input
                                type="text"
                                class="input"
                                name="jurisdiction"
                                value="{{ old('jurisdiction') }}"
                                placeholder="e.g. Kolkata, West Bengal">

                            @error('jurisdiction')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                       <div class="space-y-1.5 md:col-span-2 lg:w-1/2 w-full">
                            <h2 class="label mb-2">Deposit Amount</h2>
                            <input
                                type="text"
                                class="input"
                                name="deposit_amount"
                                value="{{ old('deposit_amount') }}">

                            @error('deposit_amount')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                        <div class="w-full mt-4">
                            <h2 class="label mb-2">Attachment</h2>
                            <input
                                type="file"
                                name="attachment"
                                accept="image/jpeg,image/png,application/pdf,.doc,.docx,.xls,.xlsx"
                                id="attachment"
                                class="block w-full text-sm text-slate-300 border border-white/10 rounded-md cursor-pointer bg-transparent focus:outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-white/10 file:text-white file:hover:bg-white/20 file:cursor-pointer" />
                                <div class="text-xs text-slate-400 my-2 w-full">
                                    *PDF, Word, Excel, JPG, PNG, JPEG documents only · Max 1 MB
                                </div>
                                <div class="w-full">
                                    @error('attachment')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                        </div>
            </section>

            <!-- Additional Documents -->
            <x-additional-documents />

            <!-- Remarks (Rich Text Editor) -->
            <x-rich-text-editor :editable="true" />

            <div class="flex flex-wrap justify-end gap-3 pt-2">
                <a href="/dashboard/agreements" class="btn btn-ghost">Cancel</a>
                <button id="addBtn" type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Submit</button>
            </div>
        </form>
    </main>

    @section('extrajscdns')
    <script src="{{ asset('assets/agreement-page-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection

    @section('extrajs')
    <script>
        window.partyIndex = {{ count($partiesData) }};
    </script>
    @endsection

    @section('extracsscdns')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    @endsection

    @section('extracss')
    <style>
        body {
            background: #0b1220;
            color: #e2e8f0;
        }

        .card {
            background: #111a2e;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem .875rem;
            border-radius: .5rem;
            font-size: .875rem;
            font-weight: 500;
            transition: .15s;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-ghost {
            background: transparent;
            color: #cbd5e1;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-icon {
            padding: .5rem;
        }

        .input,
        select.input,
        textarea.input {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: .5rem;
            padding: .5rem .75rem;
            color: #e2e8f0;
            font-size: .875rem;
        }

        .input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        }

        .label {
            font-size: .75rem;
            color: #94a3b8;
        }

        .section-title {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        select.input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2rem;
        }

        input[type="date"].input {
            color-scheme: dark;
        }

        /* Rich text editor toolbar */
        .rte-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .25rem;
            padding: .5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: none;
            border-top-left-radius: .5rem;
            border-top-right-radius: .5rem;
            background: rgba(255, 255, 255, 0.02);
        }

        .rte-toolbar button {
            padding: .375rem .5rem;
            border-radius: .375rem;
            color: #cbd5e1;
            font-size: .875rem;
        }

        .rte-toolbar button:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .rte-toolbar .sep {
            width: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: .125rem .25rem;
        }

        .rte-content {
            min-height: 180px;
            padding: .75rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom-left-radius: .5rem;
            border-bottom-right-radius: .5rem;
            font-size: .875rem;
            outline: none;
        }

        .rte-content:focus {
            border-color: #3b82f6;
        }

        .color-swatch {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
        }

        .party-row {
            display: grid;
            grid-template-columns: 90px 1fr 1fr 80px;
            gap: .75rem;
            align-items: center;
            padding: .75rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: .5rem;
        }

        .checkbox {
            width: 1rem;
            height: 1rem;
            accent-color: #3b82f6;
        }

        /* Editor container */
        .ql-container {
            background: #0f172a;
            border: 1px solid #334155 !important;
            color: #fff;
            font-size: 15px;
        }

        /* Editable area */
        .ql-editor {
            min-height: 250px;
            color: #fff;
            font-family: inherit;
            width: 100%;
        }

        /* Placeholder */
        .ql-editor.ql-blank::before {
            color: #94a3b8;
            font-style: normal;
        }

        /* Toolbar */
        .ql-toolbar {
            background: #1e293b;
            border: 1px solid #334155 !important;
        }

        /* Toolbar icons */
        .ql-toolbar .ql-stroke {
            stroke: #e2e8f0;
        }

        .ql-toolbar .ql-fill {
            fill: #e2e8f0;
        }

        .ql-toolbar .ql-picker {
            color: #e2e8f0;
        }

        .ql-toolbar button:hover,
        .ql-toolbar button.ql-active {
            background: #334155;
        }

        .ql-toolbar .ql-picker-options {
            background: #1e293b;
            color: white;
        }
    </style>
    @endsection

</x-base-component>