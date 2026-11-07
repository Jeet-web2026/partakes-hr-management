<x-filament-panels::page>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <main class="mt-8">
        <!-- Toolbar -->
        <div>
            <div class="flex flex-col gap-3">

                {{-- Top row --}}
                <div class="flex items-center justify-between gap-3">

                    {{-- Left side --}}
                    <div class="flex items-center gap-2">

                        {{-- Rows (HIDDEN: only 7 fixed steps) --}}
                        <div class="hidden items-center gap-2">
                            <span class="hidden sm:inline text-sm text-muted-foreground">
                                Rows
                            </span>

                            <select
                                id="perPageSelect"
                                class="h-9 w-[70px] sm:w-[90px] rounded-md border border-white/10 bg-transparent px-2 text-sm text-white focus:outline-none">
                                <option value="50" class="bg-slate-800">50</option>
                                <option value="100" class="bg-slate-800" selected>100</option>
                                <option value="250" class="bg-slate-800">250</option>
                            </select>
                        </div>

                        {{-- Filter (HIDDEN) --}}
                        <div class="hidden relative" data-menu>

                            <button
                                type="button"
                                data-menu-toggle="calMenuForFilter"
                                aria-label="Filter by date"
                                title="Filter by date"
                                class="hidden h-9 w-9 sm:w-auto sm:px-3 items-center justify-center gap-2 rounded-md border border-white/10 hover:bg-white/5 transition">
                                <i class="bi bi-funnel text-base"></i>

                                <span class="hidden sm:inline text-sm">
                                    Filter by date
                                </span>
                            </button>

                            {{-- Filter Dropdown --}}
                            <div
                                id="calMenuForFilter"
                                class="hidden absolute right-0 left-[-50px] mt-2 w-[calc(100vw-2rem)] max-w-80 rounded-md border border-white/10 bg-card shadow-xl z-50">

                                <div class="p-4">

                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-sm font-medium">
                                            Select Period
                                        </label>

                                        <button
                                            id="closeDateRange"
                                            type="button"
                                            class="h-8 w-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    {{-- Date type --}}
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 mb-3 text-sm">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="dateFilterType"
                                                value="agreement_date"
                                                checked
                                                class="accent-blue-600">
                                            Agreement Date
                                        </label>

                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="dateFilterType"
                                                value="expiry_date"
                                                class="accent-blue-600">
                                            Expiry Date
                                        </label>
                                    </div>

                                    <input
                                        id="agreementDateRange"
                                        class="w-full rounded border border-white/10 bg-transparent px-3 py-2 text-sm"
                                        placeholder="Select Date Range">

                                    <div class="flex justify-end gap-2 mt-4">
                                        <button
                                            id="clearDate"
                                            type="button"
                                            class="px-3 py-2 rounded border border-white/10 text-sm hover:bg-white/5">
                                            Clear
                                        </button>

                                        <button
                                            id="applyDate"
                                            type="button"
                                            class="px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
                                            Apply
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Search (HIDDEN) --}}
                        <div class="relative hidden" data-menu>

                            <button
                                id="searchToggle"
                                type="button"
                                data-menu-toggle="searchMenu"
                                aria-label="Search"
                                title="Search"
                                class="h-9 w-9 items-center justify-center rounded-md border border-white/10 hover:bg-white/5 transition">
                                <i class="bi bi-search"></i>
                            </button>

                            {{-- Desktop Search --}}
                            <div class="relative hidden">

                                <svg
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>

                                <input
                                    id="searchInputDesktop"
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Search step name…"
                                    class="h-9 w-[250px] lg:w-[350px] rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary">

                            </div>

                        </div>

                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2">

                        {{-- Add Agreement Name (HIDDEN) --}}
                        <a
                            id="addAgreementNameBtn"
                            href="{{ route('add-agreement-name', ['company' => $companyInformation->first()?->Orga_UIN]) }}"
                            aria-label="Add Agreement Name"
                            title="Add Agreement Name"
                            class="hidden h-9 w-9 sm:w-auto sm:px-4 items-center justify-center gap-2 rounded-md bg-[#3c83f6] text-sm font-medium text-white hover:bg-[#3c83f6]/90 transition">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>

                            <span class="hidden sm:inline">
                                Add
                            </span>

                        </a>

                        {{-- Columns (HIDDEN) --}}
                        <div class="hidden relative" data-menu>

                            <button
                                type="button"
                                data-menu-toggle="colsMenu2"
                                aria-label="Show columns"
                                title="Show columns"
                                class="hidden h-9 w-9 sm:w-auto sm:px-3 items-center justify-center gap-2 rounded-md border border-white/10 hover:bg-white/5 transition">

                                <i class="bi bi-window-plus"></i>

                            </button>

                            {{-- Columns Menu --}}
                            <div
                                id="colsMenu2"
                                class="hidden absolute right-0 mt-2 w-60 max-h-96 overflow-y-auto rounded-md border border-white/10 bg-card shadow-xl z-50 p-1">

                                <div class="px-2 py-1.5 text-xs font-semibold text-muted-foreground">
                                    Show columns
                                </div>

                                <div class="border-t border-white/10 my-1"></div>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="category" class="accent-primary">
                                    Agreement Category
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="type" class="accent-primary">
                                    Agreement Type
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="name" class="accent-primary" checked>
                                    Agreement Name
                                </label>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <x-globalresponse />

        {{-- Mobile Search Dropdown --}}
        <div
            id="searchMenu"
            class="hidden mb-3 rounded-md border border-white/10 bg-card shadow-xl z-50 p-3">

            <div class="relative">

                <svg
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.3-4.3" />
                </svg>

                <input
                    id="searchInput"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search step name..."
                    class="h-10 w-full rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary">

            </div>

        </div>

        <!-- Table (visible on all screen sizes) -->
        <div class="rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3">Step</th>
                        <th class="px-5 py-3">Step Name</th>
                        <th class="px-5 py-3">Stage Name</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody id="tbody">
                    @forelse ($this->steps as $i => $step)
                    @php $names = (array) $step->name_list; @endphp
                    <tr wire:key="step-{{ $step->Intv_Step_UIN }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                        <td class="px-5 py-3 whitespace-nowrap align-top">Step {{ $step->Intv_Step_Numb }}</td>

                        <td class="px-5 py-3">
                            @php $names = (array) $step->Intv_Step_Name; @endphp

                            <div class="flex flex-wrap gap-1.5">
                                @foreach ((array) $step->placeholder_list as $j => $placeholder)
                                @php $custom = filled($names[$j] ?? null); @endphp

                                <span
                                    wire:key="step-{{ $step->Intv_Step_UIN }}-{{ $j }}"
                                    class="inline-flex items-center rounded border px-2.5 py-0.5 text-xs font-medium
                                    {{ $custom
                                        ? 'border-[#3c83f6]/30 bg-[#3c83f6]/15 text-[#3c83f6]'
                                        : 'border-white/10 bg-white/5 text-muted-foreground' }}">
                                    {{ $custom ? $names[$j] : 'e.g. ' . $placeholder }}
                                </span>
                                @endforeach
                            </div>
                        </td>

                        <td class="px-5 py-3 whitespace-nowrap align-top">{{ $step->stage?->Stage_Name }}</td>

                        <td class="px-5 py-3 align-top">
                            @if ($step->Intv_Step_Stau === 100201)
                            <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-medium text-emerald-400">Active</span>
                            @else
                            <span class="inline-flex rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-muted-foreground">Inactive</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-right align-top">
                            @php $stageActive = $step->stage?->Stage_Status === 100201; @endphp

                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    @keydown.escape.window="open = false"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-white/10 transition"
                                    aria-haspopup="true"
                                    :aria-expanded="open">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <div
                                    x-show="open"
                                    x-cloak
                                    @click.outside="open = false"
                                    x-transition.origin.top.right
                                    class="absolute right-0 z-40 mt-1 w-44 rounded-md border border-white/10 bg-card py-1 shadow-xl">

                                    @if ($stageActive)
                                    <button
                                        type="button"
                                        wire:click="openEdit('{{ $step->Intv_Step_UIN }}')"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-white/5">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    @else
                                    <button
                                        type="button"
                                        disabled
                                        title="This stage is inactive, so its steps can't be edited"
                                        class="flex w-full cursor-not-allowed items-center gap-2 px-3 py-2 text-left text-sm opacity-40">
                                        <i class="bi bi-pencil-square"></i> Edit (stage inactive)
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-muted-foreground">No interview steps found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards (HIDDEN: the table above is used on all sizes) -->
        <div id="mobileAgreementList" class="hidden space-y-3"></div>

        <!-- Footer (HIDDEN) -->
        <div class="mt-4 flex items-center justify-between text-xs text-muted-foreground hidden">
            @php($total = $this->steps->count())
            <span id="entriesLabel">Showing {{ $total ? 1 : 0 }} to {{ $total }} of {{ $total }} entries</span>
            <div id="pagination" class="hidden items-center gap-1"></div>
        </div>
    </main>

    {{-- Edit modal --}}
    @if ($editingUin)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="closeEdit">
        <div class="w-full max-w-3xl rounded-xl border border-white/10 bg-card p-5 shadow-xl">

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold">Edit interview steps</h3>
                <button type="button" wire:click="closeEdit"
                    class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-white/10">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">

                {{-- Stage (read-only: the rows belong to this stage) --}}
                <div>
                    <label class="mb-1 block text-sm font-medium">Stage</label>
                    <select
                        wire:model="editStage"
                        disabled
                        class="h-10 w-full cursor-not-allowed rounded-md border border-white/10 bg-transparent px-3 text-sm text-white opacity-70 focus:outline-none">
                        @foreach ($this->stages as $stage)
                        <option value="{{ $stage->Stage_UIN }}" class="bg-slate-800">
                            {{ $stage->Stage_Name }}
                        </option>
                        @endforeach
                    </select>
                    @error('editStage')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Steps (scrollable) --}}
                <div class="h-[300px] overflow-y-auto pr-1">
                    <p class="mb-2 text-sm font-medium">Steps</p>

                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($editStepUins as $i => $stepUin)
                        <div wire:key="step-row-{{ $stepUin }}" class="flex items-start gap-3">

                            {{-- Left: saved name or placeholder hint --}}
                            <div class="min-w-0 flex-1">
                                <textarea
                                    wire:model="editNames.{{ $i }}"
                                    rows="2"
                                    maxlength="100"
                                    placeholder="e.g. {{ $editPlaceholders[$i] ?? '' }}"
                                    class="w-full resize-none rounded-md border border-white/10 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                                @error('editNames.' . $i)
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Right: active / inactive --}}
                            <div class="w-32 shrink-0">
                                <select
                                    wire:model="editStatuses.{{ $i }}"
                                    class="w-full rounded-md border border-white/10 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary [&>option]:bg-gray-900">
                                    @foreach ($statusOptions as $uin => $name)
                                    <option value="{{ $uin }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('editStatuses.' . $i)
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @error('editNames')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeEdit"
                        class="rounded border border-white/10 px-3 py-2 text-sm hover:bg-white/5">
                        Cancel
                    </button>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="rounded bg-[#3c83f6] px-3 py-2 text-sm text-white hover:bg-[#3c83f6]/90 disabled:opacity-60">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        var AGREEMENTS_URL = "{{ route('agreements-name.data') }}";
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/header-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</x-filament-panels::page>