<x-base-component tailwind="true" jQuery="true" subheading="Edit Agreement" metadata="Edit Agreement — Renewal Tracker" metadescription="description" bIcons="true">

    <x-secondary-header backurl="/dashboard/agreements" :companyInformation="$agreement->organisationDetails" :userData="$userData" title="Agreements" backurlforSettings="https://partakers.in/dashboard" />

    <main class="mx-auto max-w-5xl px-6 py-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="mb-6">
                <p class="mt-1 text-sm text-slate-400 hidden" id="page-subtitle">Reference No. <strong>({{ $agreement->Agmt_Ref_No ?? '' }})</strong></p>
            </div>
            <x-a-greement-history :agreementId="$agreement->Agmt_UIN" />
        </div>

        <x-globalresponse />

        <form
            action="{{ route('agreements.update', $agreement->Agmt_UIN) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
            id="agreementForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="company_id" value="{{ $agreement->Orga_UIN }}">
            <!-- Classification -->
            <section class="card p-6">
                <h2 class="section-title">Agreement Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="label">Agreement Category</label>
                        <select class="input js-lock" name="category" id="agreementCategory">
                            @php
                                $allChildren = $agreementCategories
                                    ->flatMap(function ($parent) {
                                        return $parent->children->map(function ($child) use ($parent) {
                                            $child->parentName = $parent->Agmt_Catg_Name;
                                            return $child;
                                        });
                                    })
                                    ->sortBy('Agmt_Catg_Name')
                                    ->values();
                            @endphp

                            @foreach($allChildren as $child)
                                <option
                                    class="bg-slate-900"
                                    value="{{ $child->Agmt_Catg_UIN }}"
                                    @selected(old('agreement_category', $agreement->Agmt_Catg_UIN) == $child->Agmt_Catg_UIN)>
                                    {{ $child->Agmt_Catg_Name }} &lt; {{ $child->parentName }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="label">Agreement Type</label>
                        <select class="input js-lock" name="type" id="agreementType">
                            {{-- populated dynamically via AJAX --}}
                        </select>
                        <input type="hidden" id="currentAgreementType" 
                                    value="{{ old('agreement_type', $agreement->Agmt_Type_UIN ?? '') }}">
                        @error('type')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label class="label">Agreement Name</label>
                        <input class="input js-lock" name="name" value="{{ old('name', $agreement->Agmt_Name) }}" />
                        @error('name')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Store the agreement's currently-saved type so JS can pre-select it after AJAX loads --}}
                <input type="hidden" id="currentAgreementType" value="{{ old('type', $agreement->Agmt_Type_UIN) }}">
            </section>

            <!-- Parties -->
            <section class="card p-6">
                <h2 class="section-title">Agreement Parties</h2>

                <div class="space-y-3" id="parties">

                    <div class="grid grid-cols-[90px_1fr_1fr_60px_40px] gap-3 px-1 text-xs uppercase tracking-wider text-slate-400">
                        <div>Role</div>
                        <div>Organization Name</div>
                        <div>Authorised Signatory</div>
                        <div class="text-right">Vs</div>
                        <div></div>
                    </div>
                    @php
                    $existingCount = $agreement->parties->count();
                    $oldParties = old('parties', []);
                    $totalCount = max($existingCount, count($oldParties));
                    @endphp

                    @foreach ($agreement->parties as $index => $party)
                    <div class="party-row grid grid-cols-[90px_1fr_1fr_80px_40px] gap-3 items-center" data-index="{{ $index }}">
                        <div class="space-y-1">
                            <input
                                class="input js-lock text-sm"
                                name="parties[{{ $index }}][role]"
                                value="{{ old("parties.$index.role", $party->Agmt_Prty_No) }}" />
                            <input type="hidden" name="parties[{{ $index }}][id]" value="{{ $party->Agmt_Prty_UIN }}" />
                        </div>
                        <div>
                            <input
                                class="input js-lock"
                                name="parties[{{ $index }}][org_name]"
                                value="{{ old("parties.$index.org_name", $party->Agmt_Prty_Org_Name) }}" />
                            @error("parties.$index.org_name")
                            <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>
                        <div>
                            <input
                                class="input js-lock"
                                name="parties[{{ $index }}][signatory]"
                                value="{{ old("parties.$index.signatory", $party->Agmt_Athr_Sgnt) }}" />
                            @error("parties.$index.signatory")
                            <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <input
                                type="checkbox"
                                class="checkbox js-lock vs-toggle"
                                name="parties[{{ $index }}][is_primary]"
                                value="1"
                                @checked(old("parties.$index.is_primary", $party->Agmt_Prty_UIN == $agreement->Agmt_Prty_UIN)) />
                        </div>
                        <div class="flex items-center justify-end">
                            @if ($index > 1)
                            <button type="button" class="btn btn-icon js-lock-btn" onclick="removeParty(this)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- Re-render any extra rows the user added client-side that failed validation
     and aren't yet saved to the agreement --}}
                    @for ($index = $existingCount; $index < $totalCount; $index++)
                        <div class="party-row grid grid-cols-[90px_1fr_1fr_80px_40px] gap-3 items-center" data-index="{{ $index }}">
                        <div class="space-y-1">
                            <input
                                class="input text-sm"
                                name="parties[{{ $index }}][role]"
                                value="{{ old("parties.$index.role", ($index + 1) . 'th Party') }}" />
                        </div>
                        <div>
                            <input
                                class="input"
                                name="parties[{{ $index }}][org_name]"
                                value="{{ old("parties.$index.org_name") }}" />
                            @error("parties.$index.org_name")
                            <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>
                        <div>
                            <input
                                class="input"
                                name="parties[{{ $index }}][signatory]"
                                value="{{ old("parties.$index.signatory") }}" />
                            @error("parties.$index.signatory")
                            <small class="text-red-500">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <input
                                type="checkbox"
                                class="checkbox vs-toggle"
                                name="parties[{{ $index }}][is_primary]"
                                value="1"
                                @checked(old("parties.$index.is_primary")) />
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="button" class="btn btn-icon" onclick="removeParty(this)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                </div>
                @endfor

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
                        <div class="space-y-1.5"><label class="label">Agreement Date</label>
                            <input type="date" class="input" name="agreement_date" value="{{ old('agreement_date', optional($agreement->Agmt_Strt_Date)->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" />
                            @error('agreement_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1.5"><label class="label">Expiry Date</label>
                            <input type="date" class="input" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', optional($agreement->Agmt_Expy_Date)->format('Y-m-d')) }}" />
                            @error('expiry_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1.5"><label class="label">Signing Date</label>
                            <input type="date" class="input" name="signing_date" value="{{ old('signing_date', optional($agreement->Agmt_Sign_Date)->format('Y-m-d')) }}" />
                            @error('signing_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1.5"><label class="label">Notary Date</label>
                            <input type="date" class="input" name="notary_date" value="{{ old('notary_date', optional($agreement->Agmt_Ntry_Date)->format('Y-m-d')) }}" />
                            @error('notary_date')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1.5"><label class="label">Agreement Version</label>
                            <input class="input" name="version" value="{{ old('version', $agreement->Agmt_Vson) }}" />
                            @error('agreement_version')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1.5"><label class="label">Agreement Drafted By</label>
                            <input class="input js-lock" name="drafted_by" value="{{ old('drafted_by', $agreement->Agmt_Drft_By) }}" />
                             @error('drafted_by')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-col lg:flex-row items-center gap-4 mt-3">
                        <div class="space-y-1.5 md:col-span-2 w-1/2"><label class="label">Jurisdiction</label>
                            <input class="input js-lock" name="jurisdiction" placeholder="e.g. Kolkata, West Bengal" value="{{ old('jurisdiction', $agreement->Agmt_Judn) }}" />
                            @error('jurisdiction')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex-1 mt-4">
                            <h2 class="label mb-2">Deposit Amount</h2>
                            <input
                                type="text"
                                class="input"
                                name="deposit_amount"
                                value="{{ old('deposit_amount', $agreement->Agmt_Depst_Amt ) }}">

                            @error('deposit_amount')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                        <div class="w-full mt-4">
                            <h2 class="label mb-2">Attachment</h2>

                            <div class="flex items-center gap-3">
                                @if($agreement->Agmt_Atch)
                                <a
                                    href="{{ route('agreements.download', ['agreement' => $agreement]) }}"
                                    target="_blank"
                                    download
                                    class="inline-flex items-center gap-2 px-3 py-1.5 border border-white/10 rounded-md text-sm text-slate-300 hover:text-white hover:bg-white/10 transition shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="7 10 12 15 17 10" />
                                        <line x1="12" y1="15" x2="12" y2="3" />
                                    </svg>
                                    Previous Document
                                </a>
                                @endif

                                <input
                                    type="file"
                                    name="attachment"
                                    accept="image/jpeg,image/png,application/pdf,.doc,.docx,.xls,.xlsx"
                                    id="attachment"
                                    class="block w-full text-sm text-slate-300 border border-white/10 rounded-md cursor-pointer bg-transparent focus:outline-none file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-white/10 file:text-white file:hover:bg-white/20 file:cursor-pointer" />
                            </div>
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

                <x-additional-documents
                    :file-no="$agreement->Agmt_File_no"
                    :ref-no="$agreement->Agmt_Ref_No"
                    :agreement-signed="$agreement->Agmt_Signed"
                    :signed-document-received-date="$agreement->Signed_Recv_Date"
                    :agreement-received-type="$agreement->Agmt_Recv_Type"
                />

                <!-- Remarks -->
                <x-rich-text-editor name="remarks" :value="old('remarks')" :editable="true" />
                <div class="card p-4 mt-4" style="background-color: #0f1729; border: 1px solid #2a3654; border-radius: 8px;">
                    <h3 class="mb-3" style="color: #94a3c4; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                        Previous Internal Notes
                    </h3>

                    <div style="max-height: 280px; overflow-y: auto;">
                        @forelse($agreement->remarks->sortByDesc('agmt_remk_date') as $remark)
                            <div class="flex flex-col justify-start items-start gap-3 py-2 px-3 mb-2"
                                style="background-color: #1a2338; border: 1px solid #2a3654; border-radius: 6px;">
                                <div class="flex items-center gap-1.5 text-nowrap text-[#6b7fa3] text-xs whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($remark->agmt_remk_date)->format('d-m-Y') }} | {{ $agreement->user?->User_Name }}
                                </div>

                                <div class="flex-grow-1 ql-editor" style="color: #e2e8f0; font-size: 0.9rem; word-break: break-word;">
                                    {!! $remark->agmt_remk !!}
                                </div>

                            </div>
                        @empty
                            <p class="mb-0" style="color: #6b7fa3; font-style: italic;">No previous Internal Notes found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 pt-2">
                    <a href="/dashboard/agreements" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="editSaveBtn">
                        <i class="bi bi-floppy"></i> <span>Update</span>
                    </button>
                </div>
        </form>
    </main>

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

        .input:disabled {
            opacity: .6;
            cursor: not-allowed;
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
            min-height: auto;
            color: #fff;
            font-family: inherit;
            padding: 0px;
            white-space: inherit;
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

    @section('extrajs')
    <script>
        window.partyIndex = {{ $totalCount }};
    </script>
    @endsection

    @section('extrajscdns')
    <script src="{{ asset('assets/agreement-page-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection

    @section('extracsscdns')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    @endsection
</x-base-component>