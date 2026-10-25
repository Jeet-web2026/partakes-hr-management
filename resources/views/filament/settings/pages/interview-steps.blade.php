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
        <div class="mb-6">
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
                                <option value="100" class="bg-slate-800">250</option>
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


                        {{-- Search --}}
                        <div class="relative" data-menu>

                            <button
                                id="searchToggle"
                                type="button"
                                data-menu-toggle="searchMenu"
                                aria-label="Search"
                                title="Search"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 hover:bg-white/5 transition sm:hidden">
                                <i class="bi bi-search"></i>
                            </button>

                            {{-- Desktop Search --}}
                            <div class="relative hidden sm:block">

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
                            class="hidden h-9 w-9 sm:w-auto sm:px-4 items-center justify-center gap-2 rounded-md bg-[#3c83f6] text-sm font-medium text-[#3c83f6]-foreground hover:bg-[#3c83f6]/90 transition">

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
        <!-- Table -->
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

        <!-- Table (now visible on all screen sizes) -->
        <div class="rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3">Step</th>
                        <th class="px-5 py-3">Step Name</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody id="tbody">
                    @forelse ($this->steps as $step)
                        <tr wire:key="step-{{ $step->Intv_Step_UIN }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                            <td class="px-5 py-3 whitespace-nowrap">Step {{ $step->Intv_Step_Numb }}</td>
                            <td class="px-5 py-3">{{ $step->Intv_Step_Name }}</td>
                            <td class="px-5 py-3">
                                @if ($step->Intv_Step_Stau === \App\Enums\InterviewStepStatus::Active)
                                    <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-medium text-emerald-400">Active</span>
                                @else
                                    <span class="inline-flex rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-muted-foreground">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button
                                    type="button"
                                    wire:click="openEdit('{{ $step->Intv_Step_UIN }}')"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-md border border-white/10 px-3 text-xs hover:bg-white/5 transition">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-muted-foreground">No interview steps found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards (HIDDEN: the table above is used on all sizes) -->
        <div id="mobileAgreementList" class="hidden space-y-3"></div>

        <!-- Footer -->
        <div class="mt-4 flex items-center justify-between text-xs text-muted-foreground">
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
            <div class="w-full max-w-md rounded-xl border border-white/10 bg-card p-5 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold">Edit interview step</h3>
                    <button type="button" wire:click="closeEdit"
                            class="h-8 w-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Step Name</label>
                        <input
                            type="text"
                            wire:model="editName"
                            maxlength="100"
                            class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                        @error('editName')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium">Status</label>
                        <select
                            wire:model="editStatus"
                            class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm text-white focus:outline-none">
                            <option value="active" class="bg-slate-800">Active</option>
                            <option value="inactive" class="bg-slate-800">Inactive</option>
                        </select>
                        @error('editStatus')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeEdit"
                                class="px-3 py-2 rounded border border-white/10 text-sm hover:bg-white/5">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-3 py-2 rounded bg-[#3c83f6] text-white text-sm hover:bg-[#3c83f6]/90">
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
    {{-- <script src="{{ asset('assets/agreement-management-page-funccinalities.js') }}"></script> --}}
    <script src="{{ asset('assets/common.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</x-filament-panels::page>