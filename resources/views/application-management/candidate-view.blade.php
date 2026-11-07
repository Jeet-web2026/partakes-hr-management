@php
$yn = fn ($v) => $v ? 'Yes' : 'No';
$a = $application;

$personal = [
'Name' => trim("{$a->FaNm} {$a->MiNm} {$a->SuNm}"),
'Gender' => $a->Gend,
'Mother Tongue' => $a->MoTu,
'Date of Birth' => optional($a->DOB)->format('d-M-Y'),
'Email' => $a->Emai,
'Mobile Number' => $a->Mo_No,
'Country' => $a->country->Name ?? null,
'Pincode' => $a->PinCod_UIN,
'State' => $a->State,
'District' => $a->Distr,
];

$job = [
'Interested in Job' => $a->interestedpositionDetails?->Dsig_Name,
'Job Type' => $a->Job_Type,
'Relocate' => $yn($a->Rlca) . ($a->Rlca ? ' | ' . $a->Rlca_Area : ''),
];

$assets = [
'Mobile' => $yn($a->Own_Mo) . ($a->Own_Mo ? ' | ' . $a->Mo_Type : ''),
'Laptop' => $yn($a->Own_Lptp) . ($a->Own_Lptp ? ' | ' . $a->Lptp_OS : ''),
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
        <div data-panel="resume" class="tab-panel hidden pb-6 mt-5">
            <section class="card p-6">
                <h2 class="section-title">Resume</h2>
                @if ($bioDataUrl)
                <iframe src="{{ $bioDataUrl }}"
                    class="h-[100vh] w-full rounded-lg border border-white/10 bg-slate-950" title="Resume"></iframe>
                @else
                <p class="py-8 text-center text-sm text-slate-400">No resume attached.</p>
                @endif
            </section>
        </div>

        {{-- QUERY --}}
        <div data-panel="query" class="tab-panel hidden space-y-6 pb-6 mt-5">
            <form method="POST" action="{{ route('applications.query.update', [$orguin, $a->Aply_UIN]) }}" class="space-y-6">
                @csrf

                <section class="card p-6">
                    <h2 class="section-title">Query & Status update</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="stage" class="label">Stage <span class="text-red-500">*</span></label>
                                <select id="stage" name="stage" required class="input" data-steps-url="{{ route('applications.stages.steps', [
                                        'stage'        => '__STAGE__',
                                        'organisation' => $companyInformation->Orga_UIN,
                                    ]) }}">
                                    @foreach ($stages as $s)
                                    @continue($s->Stage_Name === 'Candidate Selected')

                                    <option value="{{ $s->Stage_UIN }}" class="bg-slate-900"
                                        @selected(old('stage', $a->Stage_UIN) == $s->Stage_UIN)>
                                        Step {{ $s->Stage_Sort }} | {{ $s->Stage_Name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('stage') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="step" class="label">Steps <span class="text-red-500">*</span></label>
                                <select id="step" name="step" required class="input">
                                    @foreach ($steps as $s)
                                    <option value="{{ $s->Intv_Step_UIN }}" class="bg-slate-900"
                                        @selected(old('step', $a->Intv_Step_UIN) == $s->Intv_Step_UIN)>{{ $s->Intv_Step_Name }}</option>
                                    @endforeach
                                </select>
                                @error('step') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="label">Flags</label>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($flags as $f)
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded border border-white/10 bg-white/5 px-3.5 py-1.5 text-sm font-medium text-slate-300 transition
                                            hover:bg-white/10
                                            has-[:checked]:border-blue-500 has-[:checked]:bg-blue-600 has-[:checked]:text-white
                                            has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-blue-500/50">
                                    <input type="checkbox" name="flags[]" value="{{ $f->hr_flag_UIN }}" class="checkbox"
                                        @checked(in_array($f->hr_flag_UIN, old('flags', $selectedFlags ?? [])))>
                                    <span>{{ $f->hr_flag_name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-white/10">

                        <div class="space-y-1.5">
                            <label for="question" class="label">Question <span class="text-red-500">*</span></label>
                            <select id="question" name="question" required class="input">
                                <option value="">Select question</option>
                                @foreach ($questions as $q)
                                <option value="{{ $q->intv_qstn_UIN }}" class="bg-slate-900"
                                    @selected(old('question')==$q->intv_qstn_UIN)>{{ $q->Question_Text }}</option>
                                @endforeach
                            </select>
                            @error('question') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="answer" class="label">Answer <span class="text-red-500">*</span></label>
                            <textarea id="answer" name="answer" rows="4" required class="input"
                                placeholder="Write the candidate's answer">{{ old('answer') }}</textarea>
                            @error('answer') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="marks" class="label">Marks <span class="text-red-500">*</span></label>
                            <input type="number" id="marks" name="marks" required class="input"
                                placeholder="Enter the marks"
                                value="{{ old('marks') }}">
                            @error('marks') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Update</button>
                </div>
            </form>

            {{-- Query History --}}
            <section class="card p-6">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="section-title !mb-0">Query History</h2>
                    @if ($history->count())
                    <span class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-slate-300">
                        {{ $history->count() }} {{ \Illuminate\Support\Str::plural('entry', $history->count()) }}
                    </span>
                    @endif
                </div>

                @forelse ($history as $h)
                @if ($loop->first)
                <ol class="relative ml-3 space-y-6 border-l border-white/10">
                    @endif

                    <li class="relative pl-6">
                        {{-- timeline dot --}}
                        <span class="absolute -left-[7px] top-1.5 h-3 w-3 rounded-full border-2 border-slate-900 bg-blue-500"></span>

                        <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                            {{-- header: date + marks --}}
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <i class="bi bi-clock"></i>
                                    <span class="font-medium text-slate-200">{{ $h->CrOn?->format('d M Y') ?? '—' }}</span>
                                    <span>{{ $h->CrOn?->format('h:i A') }}</span>
                                    @if ($h->CrOn)
                                    <span class="hidden text-slate-500 sm:inline">· {{ $h->CrOn->diffForHumans() }}</span>
                                    @endif
                                </div>

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/15 px-3 py-1 text-xs font-semibold text-blue-300 ring-1 ring-inset ring-blue-500/30">
                                    <i class="bi bi-star-fill text-[10px]"></i>
                                    {{ $h->Qury_Marks }} marks
                                </span>
                            </div>

                            {{-- stage / step badges --}}
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-white/10 px-2.5 py-1 text-xs font-medium text-slate-200 capitalize">
                                    <i class="bi bi-diagram-3 text-slate-400"></i> {{ $h->stage_name }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-white/10 px-2.5 py-1 text-xs font-medium text-slate-200 capitalize">
                                    <i class="bi bi-list-check text-slate-400"></i> {{ $h->step_name }}
                                </span>
                            </div>

                            {{-- question --}}
                            <div class="mt-4">
                                <p class="label">Question</p>
                                <p class="mt-1 text-sm font-medium text-slate-100">{{ $h->question_text }}</p>
                            </div>

                            {{-- answer --}}
                            <div class="mt-3 rounded-md border border-white/5 bg-slate-950/40 p-3">
                                <p class="label">Answer</p>
                                <p class="mt-1 whitespace-pre-line break-words text-sm text-slate-300">{{ $h->Qury_Answer }}</p>
                            </div>
                        </div>
                    </li>

                    @if ($loop->last)
                </ol>
                @endif
                @empty
                <div class="flex flex-col items-center gap-2 rounded-lg border border-dashed border-white/10 py-10 text-center">
                    <i class="bi bi-clock-history text-2xl text-slate-500"></i>
                    <p class="text-sm text-slate-400">No history yet.</p>
                </div>
                @endforelse
            </section>
        </div>
    </main>

    @section('extracsscdns')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    <script src="{{ asset('assets/header-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    @endsection

    @section('extrajs')
    <script>
        (function() {
            const root = document.getElementById('candidate-view');
            const buttons = root.querySelectorAll('.tab-btn');
            const panels = root.querySelectorAll('.tab-panel');
            const active = ['bg-blue-600', 'text-white'];
            const idle = ['text-slate-400', 'hover:bg-white/5', 'hover:text-slate-200'];

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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stageSelect = document.getElementById('stage');
            const stepSelect = document.getElementById('step');
            const urlTemplate = stageSelect.dataset.stepsUrl;

            async function loadSteps(selectedStep = '') {
                const stageId = stageSelect.value;

                stepSelect.innerHTML = '';
                stepSelect.disabled = true;

                if (!stageId) return;

                try {
                    const url = urlTemplate.replace('__STAGE__', encodeURIComponent(stageId));
                    const res = await fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!res.ok) {
                        throw new Error(`HTTP ${res.status} for ${url}`);
                    }

                    const steps = await res.json();

                    if (Array.isArray(steps)) {
                        steps.forEach(s => {
                            if (!s.Intv_Step_Name) return;
                            const opt = new Option(
                                s.Intv_Step_Name,
                                s.Intv_Step_UIN,
                                false,
                                String(s.Intv_Step_UIN) === String(selectedStep)
                            );
                            opt.className = 'bg-slate-900';
                            stepSelect.add(opt);
                        });
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    stepSelect.disabled = stepSelect.options.length === 0;
                }
            }

            // on stage change: no preselected step
            stageSelect.addEventListener('change', () => loadSteps());

            // on page load: keep the saved step
            loadSteps(stepSelect.dataset.selected || '');
        });
    </script>
    @endsection

    @section('extrajscdns')
    <script src="{{ asset('assets/agreement-page-functionality.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection

    @section('extracss')
    <style>
        body {
            background: #0b1220;
            color: #e2e8f0;
        }

        .card {
            background: #111a2e;
            border: 1px solid rgba(255, 255, 255, .08);
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
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .input,
        select.input,
        textarea.input {
            width: 100%;
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .1);
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

        /* read-only value box (same look as .input, not focusable) */
        .input.value {
            display: flex;
            align-items: center;
            min-height: 2.5rem;
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

        .checkbox {
            width: 1rem;
            height: 1rem;
            accent-color: #3b82f6;
        }

        .tab-btn:not(.bg-blue-600):hover {
            background: rgba(255, 255, 255, .05);
            color: #e2e8f0;
        }

        label:has(input.checkbox:checked) {
            background: #2563eb;
            border-color: #3b82f6;
            color: #fff;
        }
    </style>
    @endsection
</x-base-component>