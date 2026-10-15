<x-base-component tailwind="true" jQuery="true" subheading="Draft Agreement" metadata="Draft Agreement — Renewal Tracker" metadescription="description" bIcons="true">

    <header class="border-b border-white/10 bg-white/[0.02]">
        <div class="mx-auto max-w-5xl flex items-center justify-between px-6 py-4">
            <span class="inline-flex items-center gap-2 text-sm text-slate-400">
                <i class="bi bi-link-45deg"></i> Shared Draft
            </span>
            <span class="text-xs uppercase tracking-wider text-slate-400">View Draft</span>
        </div>
    </header>

    <div class="max-w-5xl mx-auto mt-8 px-5">
        <x-link-expiration :expiredAt="request()->route('timeStamp')" />
    </div>

    <main class="mx-auto max-w-5xl px-6 py-8">

        <div class="mb-6">
            <h1 class="text-3xl font-semibold tracking-tight">Draft Agreement</h1>
            <p class="mt-1 text-sm text-slate-400">Viewing shared draft.</p>
        </div>

        <form class="space-y-6">

            <!-- Classification -->
            <section class="card p-6">
                <h2 class="section-title">Agreement Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Agreement Category -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Category</label>

                        <select class="input" name="agreement_category" disabled>
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
                                @selected($draft->Agmt_Catg_UIN == $child->Agmt_Catg_UIN)>
                                {{ $child->Agmt_Catg_Name }} &lt; {{ $child->parentName }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Agreement Type -->
                    <div class="space-y-1.5">
                        <label class="label">Agreement Type</label>

                        <input
                            type="text"
                            class="input"
                            value="{{ $draft->type->Agmt_Type_Name ?? '' }}"
                            disabled>
                    </div>

                    <!-- Agreement Name -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="label">Agreement Name</label>

                        <input
                            type="text"
                            class="input"
                            value="{{ $draft->Agmt_Name }}"
                            disabled>
                    </div>

                    <!-- Draft Version No -->
                    <div class="space-y-1.5">
                        <label class="label">Draft Version No</label>

                        <input
                            type="text"
                            class="input"
                            value="{{ $draft->Agmt_Vson }}"
                            disabled>
                    </div>

                    <!-- Approved By -->
                    <div class="space-y-1.5">
                        <label class="label">Approved By</label>

                        <input
                            type="text"
                            class="input"
                            value="{{ $draft->Agmt_Aprv_By }}"
                            disabled>
                    </div>

                    <!-- Approved Date -->
                    <div class="space-y-1.5">
                        <label class="label">Approved Date</label>

                        <input
                            type="date"
                            class="input"
                            value="{{ optional($draft->Agmt_Aprv_Date)->format('Y-m-d') }}"
                            disabled>
                    </div>

                </div>
            </section>

            <!-- File Downloads -->
            <section class="card p-6">
                <h2 class="section-title">Attachments</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="space-y-1.5">
                        <label class="label">Draft File</label>
                        @if($draft->Agmt_Drft_File_Name)
                            <a
                                href="{{ route('drafts.download', ['draft' => $draft->id, 'columnName' => 'Agmt_Drft_File_Name']) }}"
                                target="_blank"
                                download
                                class="inline-flex items-center gap-2 px-3 py-1.5 border border-white/10 rounded-md text-sm text-slate-300 hover:text-white hover:bg-white/10 transition w-full">
                                <i class="bi bi-download"></i> Download Draft File
                            </a>
                            @else
                            <p class="text-sm text-slate-500 italic">No draft file uploaded.</p>
                            @endif
                    </div>

                    <div class="space-y-1.5">
                        <label class="label">Checklist File</label>
                        @if($draft->Agmt_Chklst_File_Name)
                            <a
                                href="{{ route('drafts.download', ['draft' => $draft->id, 'columnName' => 'Agmt_Chklst_File_Name']) }}"
                                target="_blank"
                                download
                                class="inline-flex items-center gap-2 px-3 py-1.5 border border-white/10 rounded-md text-sm text-slate-300 hover:text-white hover:bg-white/10 transition w-full">
                                <i class="bi bi-download"></i> Download Checklist File
                            </a>
                            @else
                            <p class="text-sm text-slate-500 italic">No checklist file uploaded.</p>
                            @endif
                    </div>

                </div>
            </section>

        </form>
    </main>

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

        .input,
        select.input {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: .5rem;
            padding: .5rem .75rem;
            color: #e2e8f0;
            font-size: .875rem;
        }

        .input:disabled,
        .input:read-only {
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
    </style>
    @endsection

</x-base-component>