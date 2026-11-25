<x-filament-panels::page>
    @php
    $columns = collect(\App\Models\EmplPosting::LOOKUPS)->map(fn ($c) => $c[0])->all()
    + ['Strt_Dt' => 'Start Date', 'End_Dt' => 'End Date'];
    $defaults = ['employee', 'branchOffice', 'designation', 'department', 'project', 'reportingHead', 'Strt_Dt', 'End_Dt', 'status'];
    $btn = 'inline-flex h-9 items-center justify-center gap-2 rounded-md border border-white/10 px-3 text-sm hover:bg-white/5 transition';
    $field = 'w-full rounded-md border border-white/10 bg-slate-900 px-3 py-2 text-sm text-white';
    @endphp

    <div class="mt-8" x-data="{ cols: @js($defaults) }">
        {{-- Toolbar --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="hidden sm:inline text-sm">Rows</span>
                <select wire:model.live.number="perPage"
                    class="h-9 w-[90px] rounded-md border border-white/10 bg-slate-900 px-2 text-sm text-white">
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                </select>

                {{-- Filter --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="{{ $btn }}" title="Filter">
                        <i class="bi bi-funnel"></i>
                        <span class="hidden sm:inline">Filter</span>
                    </button>

                    <div x-show="open" x-cloak
                        class="absolute left-0 z-50 mt-2 w-72 space-y-3 rounded-md border border-white/10 bg-card p-4 shadow-xl">
                        <select wire:model.live="statusFilter" class="{{ $field }}">
                            <option value="">All Status</option>
                            @foreach ($this->statuses as $uin => $name)
                            <option value="{{ $uin }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        <label class="block text-xs">Start date from
                            <input type="date" wire:model.live="fromDate" class="{{ $field }} mt-1">
                        </label>

                        <label class="block text-xs">Start date to
                            <input type="date" wire:model.live="toDate" class="{{ $field }} mt-1">
                        </label>

                        <div class="flex justify-end">
                            <button type="button" wire:click="clearFilters" class="{{ $btn }}">Clear</button>
                        </div>
                    </div>
                </div>

                {{-- Search --}}
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search postings…"
                    class="h-9 w-[220px] lg:w-[350px] rounded-md border border-white/10 bg-transparent px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <div class="flex items-center gap-2">
                {{-- Columns --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="{{ $btn }}" title="Show columns">
                        <i class="bi bi-window-plus"></i>
                    </button>

                    <div x-show="open" x-cloak
                        class="absolute right-0 z-50 mt-2 max-h-96 w-60 overflow-y-auto rounded-md border border-white/10 bg-card p-1 shadow-xl">
                        <div class="px-2 py-1.5 text-xs font-semibold">Show columns</div>
                        @foreach ($columns as $key => $label)
                        <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm hover:bg-white/5">
                            <input type="checkbox" value="{{ $key }}" x-model="cols" class="accent-primary">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Add this page's UIN, then uncomment --}}
                {{-- <x-feedback-button page="YOUR_PAGE_UIN" /> --}}
            </div>
        </div>

        <x-globalresponse />

        {{-- Desktop Table --}}
        <div class="hidden overflow-x-auto rounded-xl border border-white/10 bg-card lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider">
                        @foreach ($columns as $key => $label)
                        <th x-show="cols.includes('{{ $key }}')" x-cloak class="px-5 py-3">
                            @if (in_array($key, ['Strt_Dt', 'End_Dt']))
                            <button type="button" wire:click="sortBy('{{ $key }}')" class="uppercase tracking-wider">
                                {{ $label }}
                                @if ($sortField === $key) {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                            </button>
                            @else
                            {{ $label }}
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->postings as $posting)
                    <tr wire:key="row-{{ $posting->Post_UIN }}" class="border-b border-white/5 hover:bg-white/5">
                        @foreach ($columns as $key => $label)
                        <td x-show="cols.includes('{{ $key }}')" x-cloak class="px-5 py-3">
                            {{ in_array($key, ['Strt_Dt', 'End_Dt']) ? ($posting->{$key}?->format('d M Y') ?? '—') : $posting->displayName($key) }}
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-5 py-8 text-center">No postings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="space-y-3 lg:hidden">
            @forelse ($this->postings as $posting)
            <div wire:key="card-{{ $posting->Post_UIN }}" class="rounded-xl border border-white/10 bg-card p-4 text-sm">
                @foreach ($columns as $key => $label)
                <div x-show="cols.includes('{{ $key }}')" x-cloak class="flex justify-between gap-4 py-1">
                    <span class="opacity-60">{{ $label }}</span>
                    <span class="text-right">
                        {{ in_array($key, ['Strt_Dt', 'End_Dt']) ? ($posting->{$key}?->format('d M Y') ?? '—') : $posting->displayName($key) }}
                    </span>
                </div>
                @endforeach
            </div>
            @empty
            <p class="py-8 text-center text-sm">No postings found.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-4 flex items-center justify-between text-xs">
            <span>
                Showing {{ $this->postings->firstItem() ?? 0 }}–{{ $this->postings->lastItem() ?? 0 }}
                of {{ $this->postings->total() }}
            </span>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="previousPage" @disabled($this->postings->onFirstPage())
                    class="{{ $btn }} disabled:opacity-40">Previous</button>
                <span>{{ $this->postings->currentPage() }} / {{ $this->postings->lastPage() }}</span>
                <button type="button" wire:click="nextPage" @disabled(! $this->postings->hasMorePages())
                    class="{{ $btn }} disabled:opacity-40">Next</button>
            </div>
        </div>
    </div>
    <script>
        var AGREEMENTS_URL = "{{ route('job-applications.index') }}";
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/base-agreement-page-functionalities.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
</x-filament-panels::page>