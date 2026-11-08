<x-filament-panels::page>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    @php $designations = $this->designations; @endphp

    <main class="mt-8">
        {{-- Toolbar --}}
        <div class="mb-6 flex items-center justify-between gap-3">

            <div class="flex w-full items-center gap-3 sm:w-auto">

                {{-- Rows per page --}}
                <div class="flex shrink-0 items-center gap-2">
                    <label for="perPage" class="hidden text-xs text-muted-foreground sm:block">Rows</label>
                    <select
                        id="perPage"
                        wire:model.live="perPage"
                        class="h-9 rounded-md border border-white/10 bg-transparent px-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-primary">
                        @foreach (\App\Filament\Settings\Pages\Designations::PER_PAGE_OPTIONS as $option)
                        <option value="{{ $option }}" class="bg-slate-800">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>


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
            </div>

            {{-- Add --}}
            <div class="flex items-center gap-2">
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
                <x-feedback-button page="10000015" />
            </div>
        </div>

        {{-- Flash / session message (same component as the questions page) --}}
        <x-globalresponse />

        {{-- Table --}}
        <div class="rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3">Designation</th>
                        <th class="px-5 py-3">Designation Type</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody id="tbody">
                    @forelse ($designations as $designation)
                    <tr wire:key="dsig-{{ $designation->getKey() }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                        <td class="px-5 py-3 whitespace-nowrap font-medium">{{ $designation->Dsig_Name }}</td>
                        <td class="px-5 py-3 whitespace-nowrap font-medium capitalize">{{ $designation->Dsig_Type }}</td>
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
                        <td class="px-5 py-3 text-right">
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
                                    class="absolute right-0 z-40 mt-1 w-36 rounded-md border border-white/10 bg-card py-1 shadow-xl">
                                    <button
                                        type="button"
                                        wire:click="openEdit('{{ $designation->getKey() }}')"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-white/5">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <button
                                        type="button"
                                        @click="
                                            open = false;
                                            Swal.fire({
                                                title: 'Delete this designation?',
                                                text: 'This action cannot be undone.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Yes, delete',
                                                cancelButtonText: 'Cancel',
                                                confirmButtonColor: '#ef4444',
                                                cancelButtonColor: '#475569',
                                                background: '#0f172a',
                                                color: '#e2e8f0',
                                                reverseButtons: true,
                                                focusCancel: true,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete('{{ $designation->getKey() }}');
                                                }
                                            });
                                        "
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-400 hover:bg-red-500/10">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-muted-foreground">No designations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: count + pagination --}}
        @if ($designations->total() > 0)
        <div class="mt-4 flex flex-col items-center justify-between gap-3 text-xs sm:flex-row">
            <span id="entriesLabel" class="text-muted-foreground">
                Showing {{ $designations->firstItem() }} to {{ $designations->lastItem() }} of {{ $designations->total() }} entries
            </span>

            @if ($designations->hasPages())
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="previousPage"
                    @disabled($designations->onFirstPage())
                    class="inline-flex h-8 items-center gap-1 rounded-md border border-white/10 px-3 text-xs transition hover:bg-white/5 disabled:cursor-not-allowed disabled:opacity-40">
                    <i class="bi bi-chevron-left"></i> Prev
                </button>

                <span class="text-muted-foreground">
                    Page {{ $designations->currentPage() }} of {{ $designations->lastPage() }}
                </span>

                <button
                    type="button"
                    wire:click="nextPage"
                    @disabled(! $designations->hasMorePages())
                    class="inline-flex h-8 items-center gap-1 rounded-md border border-white/10 px-3 text-xs transition hover:bg-white/5 disabled:cursor-not-allowed disabled:opacity-40">
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            @endif
        </div>
        @endif
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
                <fieldset>
                    <legend class="mb-1 block text-sm font-medium">
                        Designation will be created as per <span class="text-red-400">*</span>
                    </legend>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($this->typeOptions as $value => $label)
                        <label wire:key="dsig-type-{{ $value }}"
                            class="flex cursor-pointer items-center gap-2 rounded-md border border-white/10 px-3 py-2 text-sm transition
                                hover:bg-white/5
                                has-[:checked]:border-[#3c83f6] has-[:checked]:bg-[#3c83f6]/10
                                has-[:focus-visible]:ring-1 has-[:focus-visible]:ring-primary">
                            <input type="radio" wire:model="formType" value="{{ $value }}"
                                class="h-4 w-4 accent-[#3c83f6]">
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>

                    @error('formType')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </fieldset>
                <div>
                    <label class="mb-1 block text-sm font-medium">Designation <span class="text-red-400">*</span></label>
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
                    <label class="mb-1 block text-sm font-medium">Designation Description</label>
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

    <script>
        var AGREEMENTS_URL = "{{ route('agreements-name.data') }}";
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/header-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endassets
</x-filament-panels::page>