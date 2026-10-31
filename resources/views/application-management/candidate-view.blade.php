@php
    $yn = fn ($v) => $v ? 'Yes' : 'No';
    $a  = $application;

    $personal = [
        'Name'          => trim("{$a->FaNm} {$a->MiNm} {$a->SuNm}"),
        'Gender'        => $a->Gend,
        'Mother Tongue' => $a->MoTu,
        'Date of Birth' => optional($a->DOB)->format('d-M-Y'),
        'Email'         => $a->Emai,
        'Mobile Number' => $a->Mo_No,
        'Country'       => $a->country->Name ?? null,
        'Pincode'       => $a->PinCod_UIN,
        'State'         => $a->State,
        'District'      => $a->District,
    ];

    $job = [
        'Interested in Job' => $a->interestedpositionDetails?->Dsig_Name,
        'Job Type'          => $a->Job_Type,
        'Relocate'          => $yn($a->Rlca) . ($a->Rlca ? ' | ' . $a->Rlca_Area : ''),
    ];

    $assets = [
        'Mobile'                => $yn($a->Own_Mo) . ($a->Own_Mo ? ' | ' . $a->Mo_Type : ''),
        'Laptop'                => $yn($a->Own_Lptp) . ($a->Own_Lptp ? ' | ' . $a->Lptp_OS : ''),
        'Valid Driving Licence' => $yn($a->Drvi_Lisc) . ($a->Drvi_Lisc ? ' | ' . $a->Vcle_Type_Lisc : ''),
    ];
@endphp

<x-base-component tailwind="true" jQuery="true" subheading="View Candidate" metadata="Candidate — {{ $personal['Name'] }}" metadescription="Candidate detail" bIcons="true">
    <x-secondary-header backurl="/dashboard/applications" :companyInformation="$companyInformation" :userData="$userData" title="View Candidate" backurlforSettings="https://partakers.in/dashboard" />

    <main class="mx-auto max-w-5xl px-6 py-8" id="candidate-view">

        <x-globalresponse />

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-white">{{ $personal['Name'] }}</h1>
            <p class="mt-1 text-sm text-slate-400">{{ $a->interestedpositionDetails?->Dsig_Name }}</p>
        </div>

        {{-- Tabs --}}
        <div class="grid grid-cols-3 gap-1 rounded-lg border border-white/10 bg-white/5 p-1" role="tablist">
            @foreach (['detail' => 'Detail', 'resume' => 'Resume', 'query' => 'Query'] as $key => $title)
                <button type="button" role="tab" data-tab="{{ $key }}"
                    class="tab-btn w-full rounded-md px-4 py-2.5 text-center text-sm font-medium text-slate-400 transition">
                    {{ $title }}
                </button>
            @endforeach
        </div>


        {{-- DETAIL --}}
        <div data-panel="detail" class="tab-panel space-y-6 pb-6 mt-5">
            @foreach (['Personal Details' => $personal, 'Job Preference' => $job, 'Assets & Licence' => $assets] as $title => $rows)
                <section class="card p-6">
                    <h2 class="section-title">{{ $title }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($rows as $k => $v)
                            <div class="space-y-1.5">
                                <label class="label">{{ $k }}</label>
                                <div class="input value">{{ $v ?: '—' }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        {{-- RESUME --}}
        <div data-panel="resume" class="tab-panel hidden py-6">
            <section class="card p-6">
                <h2 class="section-title">Resume</h2>
                @if ($a->Resume_Path)
                    <iframe src="{{ route('applications.resume', [$orguin, $a->Aply_UIN]) }}"
                        class="h-[80vh] w-full rounded-lg border border-white/10 bg-slate-950" title="Resume"></iframe>
                @else
                    <p class="py-8 text-center text-sm text-slate-400">No resume attached.</p>
                @endif
            </section>
        </div>

        {{-- QUERY --}}
        <div data-panel="query" class="tab-panel hidden space-y-6 py-6">
            <form method="POST" action="{{ route('applications.query.update', [$orguin, $a->Aply_UIN]) }}" class="space-y-6">
                @csrf

                <section class="card p-6">
                    <h2 class="section-title">Query</h2>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="question" class="label">Question</label>
                            <textarea id="question" name="question" rows="3" class="input">{{ old('question') }}</textarea>
                            @error('question') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="stage" class="label">Stage <span class="text-red-500">*</span></label>
                                <select id="stage" name="stage" required class="input">
                                    @foreach ($stages as $s)
                                        <option value="{{ $s->Stage_UIN }}" class="bg-slate-900"
                                            @selected(old('stage', $a->Stage_UIN) == $s->Stage_UIN)>{{ $s->Name }}</option>
                                    @endforeach
                                </select>
                                @error('stage') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="step" class="label">Steps <span class="text-red-500">*</span></label>
                                <select id="step" name="step" required class="input">
                                    @foreach ($steps as $s)
                                        <option value="{{ $s->Step_UIN }}" class="bg-slate-900"
                                            @selected(old('step', $a->Step_UIN) == $s->Step_UIN)>{{ $s->Name }}</option>
                                    @endforeach
                                </select>
                                @error('step') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="label">Flags</label>
                            <div class="mt-2 flex flex-wrap gap-x-6 gap-y-3">
                                @foreach ($flags as $f)
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-200">
                                        <input type="checkbox" name="flags[]" value="{{ $f->Flag_UIN }}" class="checkbox"
                                            @checked(in_array($f->Flag_UIN, old('flags', $selectedFlags ?? [])))>
                                        {{ $f->Name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Update</button>
                </div>
            </form>

            {{-- Query History --}}
            <section class="card p-6">
                <h2 class="section-title">Query History</h2>
                <ul class="divide-y divide-white/10">
                    @forelse ($history as $h)
                        <li class="flex flex-col gap-1 py-3 sm:flex-row sm:justify-between">
                            <span class="text-sm text-slate-100">
                                <span class="label">Date :</span>
                                {{ \Carbon\Carbon::parse($h->Intr_Date)->format('H.i d-M-Y l') }}
                            </span>
                            <span class="text-sm text-slate-100">
                                <span class="label">Interview By :</span>
                                {{ $h->interviewer->Name ?? '—' }}
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-slate-400">No history yet.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </main>

    @section('extracsscdns')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    @endsection

    @section('extrajs')
    <script>
        (function () {
            const root = document.getElementById('candidate-view');
            const buttons = root.querySelectorAll('.tab-btn');
            const panels = root.querySelectorAll('.tab-panel');
            const active = ['bg-blue-600', 'text-white'];
            const idle   = ['text-slate-400', 'hover:bg-white/5', 'hover:text-slate-200'];

            function show(name) {
                if (!root.querySelector('[data-panel="' + name + '"]')) name = 'detail';
                panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== name));
                buttons.forEach(b => {
                    const on = b.dataset.tab === name;
                    active.forEach(c => b.classList.toggle(c, on));
                    idle.forEach(c => b.classList.toggle(c, !on));
                });
                history.replaceState(null, '', '#' + name);
            }

            buttons.forEach(b => b.addEventListener('click', () => show(b.dataset.tab)));
            show(location.hash.replace('#', '') || @json($errors->any() ? 'query' : 'detail'));
        })();
    </script>
    @endsection

    @section('extrajscdns')
    <script src="{{ asset('assets/agreement-page-functionality.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection

    @section('extracss')
    <style>
        body { background: #0b1220; color: #e2e8f0; }

        .card { background: #111a2e; border: 1px solid rgba(255,255,255,.08); border-radius: 12px; }

        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .5rem .875rem;
               border-radius: .5rem; font-size: .875rem; font-weight: 500; transition: .15s; }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; }

        .input, select.input, textarea.input {
            width: 100%; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1);
            border-radius: .5rem; padding: .5rem .75rem; color: #e2e8f0; font-size: .875rem;
        }
        .input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }

        /* read-only value box (same look as .input, not focusable) */
        .input.value { display: flex; align-items: center; min-height: 2.5rem; }

        .label { font-size: .75rem; color: #94a3b8; }

        .section-title { font-size: .75rem; font-weight: 600; text-transform: uppercase;
                         letter-spacing: .08em; color: #94a3b8; margin-bottom: 1rem; }

        select.input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right .75rem center; padding-right: 2rem;
        }

        .checkbox { width: 1rem; height: 1rem; accent-color: #3b82f6; }

        .tab-btn:not(.bg-blue-600):hover { background: rgba(255,255,255,.05); color: #e2e8f0; }
    </style>
    @endsection
</x-base-component>