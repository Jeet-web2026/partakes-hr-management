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
        {{-- Toolbar --}}
        <div class="mb-6 flex items-center justify-between gap-3">

            {{-- Search --}}
            <div class="relative w-full sm:w-auto">
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
                    placeholder="Search designations…"
                    class="h-9 w-full sm:w-[250px] lg:w-[350px] rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            {{-- Add --}}
            <button
                type="button"
                wire:click="openCreate"
                aria-label="Add Designation"
                title="Add Designation"
                class="inline-flex h-9 w-9 sm:w-auto sm:px-4 items-center justify-center gap-2 rounded-md bg-[#3c83f6] text-sm font-medium text-white hover:bg-[#3c83f6]/90 transition">
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
                <span class="hidden sm:inline">Add</span>
            </button>
        </div>

        {{-- Flash / session message (same component as the questions page) --}}
        <x-globalresponse />

        {{-- Table --}}
        <div class="rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3 w-16">#</th>
                        <th class="px-5 py-3">Designation</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody id="tbody">
                    @forelse ($this->designations as $designation)
                    <tr wire:key="dsig-{{ $designation->getKey() }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                        <td class="px-5 py-3 whitespace-nowrap text-muted-foreground">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3 whitespace-nowrap font-medium">{{ $designation->Dsig_Name }}</td>
                        <td class="px-5 py-3">
                            <div class="line-clamp-2 max-w-2xl text-muted-foreground">{{ $designation->Dsig_Desp ?: '—' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @if ($designation->Dsig_Stau === \App\Services\ApplicationManagement\MastDsigService::STATUS_ACTIVE)
                            <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-medium text-emerald-400">{{ $designation->status?->Stau_Name ?? 'Active' }}</span>
                            @else
                            <span class="inline-flex rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-muted-foreground">{{ $designation->status?->Stau_Name ?? 'Inactive' }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    wire:click="openEdit('{{ $designation->getKey() }}')"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-md border border-white/10 px-3 text-xs hover:bg-white/5 transition">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button
                                    type="button"
                                    wire:click="confirmDelete('{{ $designation->getKey() }}')"
                                    class="inline-flex h-8 items-center gap-1.5 rounded-md border border-red-500/30 px-3 text-xs text-red-400 hover:bg-red-500/10 transition">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-muted-foreground">No designations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="mt-4 text-xs text-muted-foreground">
            @php($total = $this->designations->count())
            <span id="entriesLabel">Showing {{ $total ? 1 : 0 }} to {{ $total }} of {{ $total }} entries</span>
        </div>
    </main>

    {{-- Add / Edit modal --}}
    @if ($showForm)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="closeForm">
        <div class="w-full max-w-lg rounded-xl border border-white/10 bg-card p-5 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold">{{ $editingUin ? 'Edit designation' : 'Add designation' }}</h3>
                <button type="button" wire:click="closeForm"
                    class="h-8 w-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Name <span class="text-red-400">*</span></label>
                    <input
                        type="text"
                        wire:model="formName"
                        maxlength="150"
                        class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    @error('formName')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Description</label>
                    <textarea
                        wire:model="formDesc"
                        rows="3"
                        maxlength="255"
                        class="w-full rounded-md border border-white/10 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                    @error('formDesc')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Status <span class="text-red-400">*</span></label>
                    <select
                        wire:model="formStatus"
                        class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm text-white focus:outline-none">
                        @foreach ($this->statusOptions as $value => $label)
                        <option value="{{ $value }}" class="bg-slate-800">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('formStatus')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeForm"
                        class="px-3 py-2 rounded border border-white/10 text-sm hover:bg-white/5">
                        Cancel
                    </button>
                    <button type="submit"
                        wire:loading.attr="disabled" wire:target="save"
                        class="px-3 py-2 rounded bg-[#3c83f6] text-white text-sm hover:bg-[#3c83f6]/90">
                        {{ $editingUin ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete confirmation --}}
    @if ($deletingUin)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="cancelDelete">
        <div class="w-full max-w-sm rounded-xl border border-white/10 bg-card p-5 shadow-xl">
            <h3 class="text-base font-semibold">Delete designation?</h3>
            <p class="mt-2 text-sm text-muted-foreground">This action cannot be undone.</p>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" wire:click="cancelDelete"
                    class="px-3 py-2 rounded border border-white/10 text-sm hover:bg-white/5">
                    Cancel
                </button>
                <button type="button" wire:click="delete"
                    class="px-3 py-2 rounded bg-red-600 text-white text-sm hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</x-filament-panels::page>