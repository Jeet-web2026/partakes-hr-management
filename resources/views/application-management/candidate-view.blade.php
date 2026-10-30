@php
    $yn = fn ($v) => $v ? 'Yes' : 'No';
    $a  = $application;

    // Same look as the application form
    $card    = 'rounded-xl border border-gray-800 bg-gray-900';
    $cardHd  = 'border-b border-gray-800 px-6 py-4 text-sm font-semibold uppercase tracking-wide text-blue-400';
    $label   = 'block text-sm font-medium text-gray-200';
    $value   = 'mt-2 flex min-h-[3rem] w-full items-center rounded-lg border border-gray-800 bg-gray-950 px-4 py-3 text-sm text-gray-100';
    $input   = 'mt-2 w-full rounded-lg border border-gray-800 bg-gray-950 px-4 py-3 text-sm text-gray-100 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500';
    $error   = 'mt-1 text-xs text-red-400';

    $personal = [
        'Name'          => trim("{$a->First_Name} {$a->Middle_Name} {$a->Last_Name}"),
        'Gender'        => $a->Gender,
        'Mother Tongue' => $a->Mother_Tongue,
        'Date of Birth' => optional($a->DOB)->format('d-M-Y'),
        'Email'         => $a->Email,
        'Mobile Number' => $a->Mobile,
        'Country'       => $a->country->Name ?? null,
        'Pincode'       => $a->Pincode,
        'State'         => $a->State,
        'District'      => $a->District,
    ];

    $job = [
        'Interested in Job' => $a->Job_Intr,
        'Job Type'          => $a->Job_Type,
        'Relocate'          => $yn($a->Relocate) . ($a->Relocate ? ' | ' . $a->Relocate_Scope : ''),
    ];

    $assets = [
        'Mobile'                => $yn($a->Has_Mobile) . ($a->Has_Mobile ? ' | ' . $a->Mobile_Type : ''),
        'Laptop'                => $yn($a->Has_Laptop) . ($a->Has_Laptop ? ' | ' . $a->Laptop_OS : ''),
        'Valid Driving Licence' => $yn($a->Has_Licence) . ($a->Has_Licence ? ' | ' . $a->Licence_Type : ''),
    ];
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    <style>
        * {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>

<body class="min-h-full bg-[#0b1220] font-sans text-slate-200 antialiased">
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">

<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6" id="candidate-view">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-white">{{ $personal['Name'] }}</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $a->Job_Intr }}</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-800 bg-green-900/30 px-4 py-2 text-sm text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-800">
        <nav class="-mb-px flex gap-6" role="tablist">
            @foreach (['detail' => 'Detail', 'resume' => 'Resume', 'query' => 'Query'] as $key => $title)
                <button type="button" role="tab" data-tab="{{ $key }}"
                    class="tab-btn border-b-2 border-transparent px-1 py-3 text-sm font-medium text-gray-400 hover:text-gray-200">
                    {{ $title }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- DETAIL --}}
    <section data-panel="detail" class="tab-panel space-y-6 py-6">
        @foreach (['Personal Details' => $personal, 'Job Preference' => $job, 'Assets & Licence' => $assets] as $title => $rows)
            <div class="{{ $card }}">
                <h2 class="{{ $cardHd }}">{{ $title }}</h2>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-5 p-6 sm:grid-cols-2">
                    @foreach ($rows as $k => $v)
                        <div>
                            <dt class="{{ $label }}">{{ $k }}</dt>
                            <dd class="{{ $value }}">{{ $v ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endforeach
    </section>

    {{-- RESUME --}}
    <section data-panel="resume" class="tab-panel hidden py-6">
        <div class="{{ $card }} overflow-hidden">
            <h2 class="{{ $cardHd }}">Resume</h2>
            @if ($a->Resume_Path)
                <iframe src="{{ route('applications.resume', [$orguin, $a->Aply_UIN]) }}"
                    class="h-[80vh] w-full bg-gray-950" title="Resume"></iframe>
            @else
                <p class="p-8 text-center text-sm text-gray-400">No resume attached.</p>
            @endif
        </div>
    </section>

    {{-- QUERY --}}
    <section data-panel="query" class="tab-panel hidden space-y-6 py-6">
        <form method="POST" action="{{ route('applications.query.update', [$orguin, $a->Aply_UIN]) }}"
            class="{{ $card }}">
            @csrf
            <h2 class="{{ $cardHd }}">Query</h2>

            <div class="space-y-5 p-6">
                <div>
                    <label for="question" class="{{ $label }}">Question</label>
                    <textarea id="question" name="question" rows="3" class="{{ $input }}">{{ old('question') }}</textarea>
                    @error('question') <p class="{{ $error }}">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <div>
                        <label for="stage" class="{{ $label }}">Stage <span class="text-red-500">*</span></label>
                        <select id="stage" name="stage" required class="{{ $input }}">
                            @foreach ($stages as $s)
                                <option value="{{ $s->Stage_UIN }}" @selected(old('stage', $a->Stage_UIN) == $s->Stage_UIN)>{{ $s->Name }}</option>
                            @endforeach
                        </select>
                        @error('stage') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="step" class="{{ $label }}">Steps <span class="text-red-500">*</span></label>
                        <select id="step" name="step" required class="{{ $input }}">
                            @foreach ($steps as $s)
                                <option value="{{ $s->Step_UIN }}" @selected(old('step', $a->Step_UIN) == $s->Step_UIN)>{{ $s->Name }}</option>
                            @endforeach
                        </select>
                        @error('step') <p class="{{ $error }}">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <span class="{{ $label }}">Flags</span>
                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-3">
                        @foreach ($flags as $f)
                            <label class="inline-flex items-center gap-2 text-sm text-gray-200">
                                <input type="checkbox" name="flags[]" value="{{ $f->Flag_UIN }}"
                                    class="h-4 w-4 rounded border-gray-700 bg-gray-950 text-blue-500 focus:ring-blue-500"
                                    >
                                {{ $f->Name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                        Update
                    </button>
                </div>
            </div>
        </form>

        {{-- Query History --}}
        <div class="{{ $card }}">
            <h2 class="{{ $cardHd }}">Query History</h2>
            <ul class="divide-y divide-gray-800 px-6">
                @forelse ($history as $h)
                    <li class="flex flex-col gap-1 py-4 sm:flex-row sm:justify-between">
                        <span class="text-sm text-gray-100">
                            <span class="font-medium text-gray-400">Date :</span>
                            {{ \Carbon\Carbon::parse($h->Intr_Date)->format('H.i d-M-Y l') }}
                        </span>
                        <span class="text-sm text-gray-100">
                            <span class="font-medium text-gray-400">Interview By :</span>
                            {{ $h->interviewer->Name ?? '—' }}
                        </span>
                    </li>
                @empty
                    <li class="py-4 text-sm text-gray-400">No history yet.</li>
                @endforelse
            </ul>
        </div>
    </section>
</div>
    </main>

<script>
    (function () {
        const root = document.getElementById('candidate-view');
        const buttons = root.querySelectorAll('.tab-btn');
        const panels = root.querySelectorAll('.tab-panel');
        const active = ['border-blue-500', 'text-blue-400'];
        const idle = ['border-transparent', 'text-gray-400'];

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
</body>