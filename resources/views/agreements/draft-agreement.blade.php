<x-base-component tailwind="true" jQuery="true" subheading="Draft Agreement" metadata="Draft Agreement — Renewal Tracker" metadescription="description" bIcons="true">
    <x-secondary-header backurl="/dashboard/drafts" :companyInformation="$companyInformation" :userData="$userData" title="Agreements" backurlforSettings="https://partakers.in/dashboard" />

    <main class="mx-auto max-w-5xl px-6 py-8">

        <x-globalresponse />

        <form class="space-y-6" action="{{ route('draft-agreements.store') }}"
            method="POST"
            enctype="multipart/form-data" id="agreementForm">
            @csrf

            <input type="hidden" name="company_id" value="{{ request('company') }}">

            <!-- Classification -->
            <section class="card p-6">
                <h2 class="section-title">Draft Agreement</h2>

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
                            ->sortBy('Agmt_Catg_Name');
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

                        <select id="agreementName" class="input" name="agreement_name">
                            <option value="" class="bg-slate-900">Select agreement category & type first</option>
                        </select>
                        <input type="hidden" id="currentAgreementName"
                            value="{{ old('agreement_name', $agreement->Agmt_Name ?? '') }}">

                        @error('agreement_name')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Draft Version No -->
                    <div class="space-y-1.5">
                        <label class="label">Draft Version No</label>

                        <input
                            type="text"
                            class="input"
                            name="draft_version_no"
                            value="{{ old('draft_version_no') }}"
                            placeholder="e.g. v1.0">

                        @error('draft_version_no')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Approved By -->
                    <div class="space-y-1.5">
                        <label class="label">Approved By</label>

                        <input
                            type="text"
                            class="input"
                            name="approved_by"
                            value="{{ old('approved_by') }}"
                            placeholder="e.g. John Doe">

                        @error('approved_by')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Approved Date -->
                    <div class="space-y-1.5">
                        <label class="label">Approved Date</label>

                        <input
                            type="date"
                            class="input"
                            name="approved_date"
                            value="{{ old('approved_date') }}">

                        @error('approved_date')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            <!-- File Uploads -->
            <section class="card p-6">
                <h2 class="section-title">Attachments</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Upload Draft File -->
                    <div class="space-y-1.5">
                        <label class="label">Upload Draft File</label>

                        <input
                            type="file"
                            class="input file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-slate-700 file:text-slate-100 file:text-sm hover:file:bg-slate-600 cursor-pointer"
                            name="draft_file"
                            id="draftFile">

                        @error('draft_file')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Checklist File -->
                    <div class="space-y-1.5">
                        <label class="label">Upload Checklist File</label>

                        <input
                            type="file"
                            class="input file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-slate-700 file:text-slate-100 file:text-sm hover:file:bg-slate-600 cursor-pointer"
                            name="checklist_file"
                            id="checklistFile">

                        @error('checklist_file')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            <div class="flex flex-wrap justify-end gap-3 pt-2">
                <a href="/dashboard/drafts" class="btn btn-ghost">Cancel</a>
                <button id="submitDraftBtn" type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Submit</button>
            </div>
        </form>
    </main>

    <script>
        var AGREEMENT_NAMES_URL = "{{ route('fetchagreements.name') }}";
        var ORGA_UIN = "{{ $companyInformation->Orga_UIN ?? '' }}";
    </script>

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

    @section('extracss')
    <style>
        * {
            font-family: "Figtree", sans-serif;
            font-optical-sizing: auto;
        }

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