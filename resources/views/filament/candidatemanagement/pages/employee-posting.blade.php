<x-filament-panels::page>
    @php
    $columns = collect(\App\Models\EmplPosting::LOOKUPS)->map(fn ($c) => $c[0])->all()
    + ['Strt_Dt' => 'Start Date', 'End_Dt' => 'End Date'];
    $defaults = ['employee', 'branchOffice', 'designation', 'department', 'project', 'reportingHead'];
    $btn = 'pt-btn inline-flex h-9 items-center justify-center gap-2 px-3 text-sm transition';
    $field = 'pt-input w-full px-3 py-2 text-sm';

    $cell = function ($application, string $key) {
    $posting = $application->posting;

    if ($key === 'employee') {
    return $application->full_name ?: '-';
    }

    if (in_array($key, ['Strt_Dt', 'End_Dt'])) {
    return $posting?->{$key}?->format('d M Y') ?? '-';
    }

    $column = \App\Models\EmplPosting::LOOKUPS[$key][1];

    return $posting?->{$key}?->getAttribute($column) ?: '-';
    };
    @endphp

    <style>
        /* Page elements */
        .dark .pt-input {
            background-color: #0b1220;
            border: 1px solid #242a37;
            border-radius: .5rem;
            color: #fff;
            color-scheme: dark;
        }

        .dark .pt-input:focus {
            outline: none;
            border-color: #3c83f6;
            box-shadow: 0 0 0 1px #3c83f6;
        }

        .dark .pt-btn {
            background-color: #0f172b;
            border: 1px solid #272f41;
            border-radius: .5rem;
            color: #fff;
        }

        .dark .pt-btn:hover:not(:disabled) {
            background-color: #111a2b;
            border-color: #3c83f6;
        }

        .dark .pt-panel {
            background-color: #0f172b;
            border: 1px solid #272f41;
            border-radius: .5rem;
            color: #cbd5e1;
        }

        .dark .pt-card {
            background-color: #111a2b;
            border: 1px solid #272f41;
            color: #fff;
        }

        .dark .pt-title {
            color: #60a5fa;
            border-bottom: 1px solid #272f41;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .dark .pt-head {
            background-color: #111a2b;
            border-bottom: 1px solid #272f41;
            color: #cbd5e1;
        }

        .dark .pt-row {
            border-bottom: 1px solid #272f41;
        }

        .dark .pt-row:hover,
        .dark .pt-option:hover {
            background-color: rgb(255 255 255 / .05);
        }

        .dark .pt-muted {
            color: #cbd5e1;
        }

        /* Edit modal */
        .dark .fi-modal-window {
            background-color: #111a2b;
            box-shadow: 0 0 0 1px #272f41;
        }

        .dark .fi-modal-header {
            border-bottom: 1px solid #272f41;
            padding-bottom: 1.25rem;
        }

        .dark .fi-modal-footer {
            border-top: 1px solid #272f41;
            padding-top: 1.25rem;
        }

        .dark .fi-modal-heading {
            color: #60a5fa;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .dark .fi-fo-field-label-content {
            color: #cbd5e1;
        }

        .dark .fi-fo-field-label-required-mark {
            color: #ef4444;
        }

        .dark .fi-input-wrp {
            background-color: #0b1220;
            border-radius: .5rem;
        }

        .dark .fi-input-wrp:not(.fi-invalid) {
            box-shadow: 0 0 0 1px #242a37;
        }

        .dark .fi-input-wrp:not(.fi-invalid):focus-within {
            box-shadow: 0 0 0 2px #3c83f6;
        }

        .dark .fi-input,
        .dark .fi-select-input-btn {
            color: #fff;
            color-scheme: dark;
        }

        /* 3-dots and select dropdowns */
        .dark .fi-dropdown-panel {
            background-color: #0f172b;
            box-shadow: 0 0 0 1px #272f41, 0 10px 25px rgb(0 0 0 / .4);
        }

        .dark .fi-dropdown-list-item:hover,
        .dark .fi-select-input-option:hover {
            background-color: rgb(255 255 255 / .05);
        }

        .dark .fi-dropdown-list-item-label,
        .dark .fi-dropdown-list-item .fi-icon {
            color: #fff;
        }
    </style>

    <div class="mt-8" x-data="{ cols: @js($defaults) }">
        {{-- Toolbar --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="pt-muted hidden sm:inline text-sm">Rows</span>
                <select wire:model.live.number="perPage" class="pt-input h-9 w-[90px] px-2 text-sm">
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

                    <div x-show="open" x-cloak class="pt-panel absolute left-0 z-50 mt-2 w-72 space-y-3 p-4 shadow-xl">
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
                    class="pt-input h-9 w-[220px] lg:w-[350px] px-3 text-sm">
            </div>

            <div class="flex items-center gap-2">
                {{-- Columns --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="{{ $btn }}" title="Show columns">
                        <i class="bi bi-window-plus"></i>
                    </button>

                    <div x-show="open" x-cloak class="pt-panel absolute right-0 z-50 mt-2 max-h-96 w-60 overflow-y-auto p-1 shadow-xl">
                        <div class="pt-title mb-1 px-2 py-2 text-xs font-semibold">Show columns</div>
                        @foreach ($columns as $key => $label)
                        <label class="pt-option flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm">
                            <input type="checkbox" value="{{ $key }}" x-model="cols" style="accent-color: #3c83f6">
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
        <div class="pt-card hidden overflow-x-auto rounded-xl lg:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="pt-head text-left text-xs font-semibold uppercase tracking-wider">
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
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->applications as $application)
                    <tr wire:key="row-{{ $application->Aply_UIN }}" class="pt-row">
                        @foreach ($columns as $key => $label)
                        <td x-show="cols.includes('{{ $key }}')" x-cloak class="px-5 py-3">
                            {{ $cell($application, $key) }}
                        </td>
                        @endforeach
                        <td class="px-5 py-3 text-right">
                            <x-filament::dropdown placement="bottom-end" teleport>
                                <x-slot name="trigger">
                                    <x-filament::icon-button icon="heroicon-m-ellipsis-vertical" label="Actions" color="gray" />
                                </x-slot>
                                <x-filament::dropdown.list>
                                    <x-filament::dropdown.list.item icon="heroicon-m-pencil-square" x-on:click="close"
                                        wire:click="mountAction('editPosting', { application: {{ (int) $application->Aply_UIN }} })">
                                        Edit
                                    </x-filament::dropdown.list.item>
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="pt-muted px-5 py-8 text-center">No verified applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="space-y-3 lg:hidden">
            @forelse ($this->applications as $application)
            <div wire:key="card-{{ $application->Aply_UIN }}" class="pt-card rounded-xl p-4 text-sm">
                <div class="flex justify-end">
                    <x-filament::dropdown placement="bottom-end" teleport>
                        <x-slot name="trigger">
                            <x-filament::icon-button icon="heroicon-m-ellipsis-vertical" label="Actions" color="gray" />
                        </x-slot>
                        <x-filament::dropdown.list>
                            <x-filament::dropdown.list.item icon="heroicon-m-pencil-square" x-on:click="close"
                                wire:click="mountAction('editPosting', { application: {{ (int) $application->Aply_UIN }} })">
                                Edit
                            </x-filament::dropdown.list.item>
                        </x-filament::dropdown.list>
                    </x-filament::dropdown>
                </div>
                @foreach ($columns as $key => $label)
                <div x-show="cols.includes('{{ $key }}')" x-cloak class="flex justify-between gap-4 py-1">
                    <span class="pt-muted">{{ $label }}</span>
                    <span class="text-right">{{ $cell($application, $key) }}</span>
                </div>
                @endforeach
            </div>
            @empty
            <p class="pt-muted py-8 text-center text-sm">No verified applications found.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="pt-muted mt-4 flex items-center justify-between text-xs">
            <span>
                Showing {{ $this->applications->firstItem() ?? 0 }} to {{ $this->applications->lastItem() ?? 0 }}
                of {{ $this->applications->total() }} entries
            </span>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="previousPage" @disabled($this->applications->onFirstPage())
                    class="{{ $btn }} disabled:opacity-40">Previous</button>
                <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-3 text-white"
                    style="background-color: #3c83f6">
                    {{ $this->applications->currentPage() }} / {{ $this->applications->lastPage() }}
                </span>
                <button type="button" wire:click="nextPage" @disabled(! $this->applications->hasMorePages())
                    class="{{ $btn }} disabled:opacity-40">Next</button>
            </div>
        </div>
    </div>

    {{-- Hidden elements required by the JS files. Do not remove. --}}
    <div wire:ignore style="display: none !important" aria-hidden="true">
        <select id="perPageSelect">
            <option value="50">50</option>
            <option value="100" selected>100</option>
            <option value="250">250</option>
        </select>

        {{-- Date filter --}}
        <div data-menu>
            <button type="button" data-menu-toggle="calMenuForFilter">
                <p class="showdateRange"></p>
            </button>
            <div id="calMenuForFilter" class="hidden">
                <button id="closeDateRange" type="button"></button>
                <select id="statusFilter">
                    <option value="">All Stages</option>
                </select>
                <input id="agreementDateRange">
                <button id="clearDate" type="button"></button>
                <button id="applyDate" type="button"></button>
            </div>
        </div>

        {{-- Status filter --}}
        <div data-menu>
            <button type="button" data-menu-toggle="statusMenuForFilter">
                <span id="statusFilterLabel">Status</span>
            </button>
            <div id="statusMenuForFilter" class="hidden">
                <button id="closeStatusFilter" type="button"></button>
                @foreach (['new', 'shortlisted', 'interview_scheduled', 'selected', 'offer_letter_issued', 'accepted'] as $value)
                <input type="checkbox" class="status-checkbox" value="{{ $value }}">
                @endforeach
                <button id="clearStatusFilter" type="button"></button>
                <button id="applyStatusFilter" type="button"></button>
            </div>
        </div>

        {{-- Search --}}
        <div data-menu>
            <button id="searchToggle" type="button" data-menu-toggle="searchMenu"></button>
            <input id="searchInputDesktop" type="text">
        </div>
        <div id="searchMenu" class="hidden">
            <input id="searchInput" type="text">
        </div>

        {{-- Calendar --}}
        <div data-menu>
            <button type="button" data-menu-toggle="calMenu"></button>
            <div id="calMenu" class="hidden">
                <button id="calPrev" type="button"></button>
                <div id="calTitle"></div>
                <button id="calNext" type="button"></button>
                <div id="calendarDays"></div>
                <button id="calToday" type="button"></button>
                <span id="calCount"></span>
                <div id="calList"></div>
            </div>
        </div>

        {{-- Generate link --}}
        <a id="generateLinkBtn"
            href="javascript:void(0)"
            data-url="{{ url('') }}"
            data-csrf="{{ csrf_token() }}"
            data-share-type="candidate"
            data-share-id="{{ $recordId ?? '' }}"></a>

        <div id="linkModal" class="hidden">
            <div data-close-modal></div>
            <h3 id="linkModalTitle"></h3>
            <button type="button" data-close-modal></button>
            <p id="linkLoading"></p>
            <p id="linkError" class="hidden"></p>
            <div id="linkResult" class="hidden">
                <input id="linkInput" type="text" readonly>
                <button id="linkCopyBtn" type="button"></button>
                <span id="linkExpiry"></span>
            </div>
        </div>

        {{-- Columns menu and table --}}
        @php
        $jsColumns = ['FaNm', 'MiNm', 'SuNm', 'Gend', 'DOB', 'MoTu', 'Cutr_UIN', 'PinCod_UIN', 'Mo_No', 'Own_Mo', 'Mo_Type', 'MO_OS', 'Emai', 'Own_Lptp', 'Lptp_OS', 'Drvi_Lisc', 'Vcle_Type_Lisc', 'Rlca', 'Rlca_Area', 'Job_Intr', 'Job_Type'];
        $jsChecked = ['FaNm', 'SuNm', 'DOB', 'Mo_No', 'Emai', 'Job_Intr', 'Job_Type'];
        $jsSortable = ['FaNm', 'SuNm', 'DOB', 'Job_Intr'];
        @endphp

        <div data-menu>
            <button type="button" data-menu-toggle="colsMenu"></button>
            <div id="colsMenu" class="hidden">
                @foreach ($jsColumns as $col)
                <input type="checkbox" data-col="{{ $col }}" @checked(in_array($col, $jsChecked))>
                @endforeach
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    @foreach ($jsColumns as $col)
                    <th data-col="{{ $col }}" @class(['hidden' => ! in_array($col, $jsChecked)])>
                        @if (in_array($col, $jsSortable))
                        <button type="button" data-sort="{{ $col }}" class="sort-btn">
                            <span class="sort-icon"></span>
                        </button>
                        @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>

        <div id="mobileAgreementList"></div>
        <span id="entriesLabel"></span>
        <div id="pagination"></div>

        {{-- Employee invite modal --}}
        <div id="employee-invite-modal" class="hidden">
            <h3 id="employee-invite-title"></h3>
            <button type="button" data-invite-close></button>
            <p id="employee-invite-error" class="hidden"></p>
            <div id="employee-invite-result" class="hidden">
                <input id="employee-invite-link" type="text" readonly>
                <button type="button" id="employee-invite-copy"></button>
                <p id="employee-invite-expiry"></p>
            </div>
            <button type="button" id="employee-invite-generate"><span></span></button>
        </div>

        <x-common.generate-link-modal />
    </div>

    <script>
        var AGREEMENTS_URL = "{{ route('job-applications.index') }}";
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/base-agreement-page-functionalities.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
</x-filament-panels::page>