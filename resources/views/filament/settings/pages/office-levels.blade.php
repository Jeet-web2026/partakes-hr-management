<x-filament-panels::page>
    <main class="mt-8">

        <!-- Toolbar -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between gap-3">

                {{-- Left side --}}
                <div class="flex items-center gap-2">

                    {{-- Rows (HIDDEN: an organisation has at most 7 levels) --}}
                    <div class="hidden items-center gap-2">
                        <span class="hidden sm:inline text-sm text-muted-foreground">Rows</span>
                        <select
                            wire:model.live="perPage"
                            class="h-9 w-[70px] sm:w-[90px] rounded-md border border-white/10 bg-transparent px-2 text-sm text-white focus:outline-none">
                            @foreach (self::PER_PAGE_OPTIONS as $n)
                            <option value="{{ $n }}" class="bg-slate-800">{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="relative">
                        <svg
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"
                            xmlns="http://www.w3.org/2000/svg"
                            width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>

                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search type name…"
                            class="h-9 w-[200px] sm:w-[250px] lg:w-[350px] rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>

                    {{-- Level filter (all levels, so existing rows can be filtered) --}}
                    <select
                        wire:model.live="levelFilter"
                        class="h-9 rounded-md border border-white/10 bg-transparent px-2 text-sm text-white focus:outline-none">
                        <option value="" class="bg-slate-800">All levels</option>
                        @foreach (\App\Enums\OfficeLevel::cases() as $level)
                        <option value="{{ $level->value }}" class="bg-slate-800">{{ $level->label() }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-1.5 sm:gap-2">
                    @php($allLevelsCreated = $editingUin === null && count($this->availableLevels) === 0)

                    <button
                        type="button"
                        wire:click="openCreate"
                        @disabled($allLevelsCreated)
                        aria-label="Add Office Type"
                        title="{{ $allLevelsCreated ? 'All office types have already been created' : 'Add Office Type' }}"
                        class="inline-flex h-9 w-9 sm:w-auto sm:px-4 items-center justify-center gap-2 rounded-md bg-[#3c83f6] text-sm font-medium text-white transition hover:bg-[#3c83f6]/90 disabled:cursor-not-allowed disabled:opacity-40">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        <span class="hidden sm:inline">Add</span>
                    </button>
                </div>
            </div>
        </div>

        <x-globalresponse />

        <!-- Table -->
        <div class="mt-3 rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3">Level</th>
                        <th class="px-5 py-3">Level Name</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Created By</th>
                        <th class="px-5 py-3">Created On</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->levels as $row)
                    <tr wire:key="level-{{ $row->Hiky_UIN }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                        <td class="px-5 py-3 whitespace-nowrap align-top">Level {{ $row->Hiky_Levl }}</td>

                        <td class="px-5 py-3 align-top">
                            <span class="inline-flex items-center rounded border border-[#3c83f6]/30 bg-[#3c83f6]/15 px-2.5 py-0.5 text-xs font-medium text-[#3c83f6]">
                                {{ $row->Hiky_Levl_Name }}
                            </span>
                        </td>

                        <td class="px-5 py-3 align-top">
                            @if ($row->Stau_UIN === 100201)
                            <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-medium text-emerald-400">Active</span>
                            @else
                            <span class="inline-flex rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-muted-foreground">Inactive</span>
                            @endif
                        </td>

                        <td class="px-5 py-3 whitespace-nowrap align-top text-muted-foreground">
                            {{ $row->creator?->User_Name ?? $row->CrBy }}
                        </td>

                        <td class="px-5 py-3 whitespace-nowrap align-top text-muted-foreground">
                           {{ $row->CrOn?->format('d M Y H:i') }}
                        </td>

                        <td class="px-5 py-3 text-right align-top">
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

                                    <button
                                        type="button"
                                        wire:click="openEdit('{{ $row->Hiky_UIN }}')"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-white/5">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="confirmDelete('{{ $row->Hiky_UIN }}')"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-400 hover:bg-white/5">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-muted-foreground">No office levels found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-4 flex items-center justify-between text-xs text-muted-foreground">
            @php($total = $this->levels->total())
            <span>Showing {{ $total ? $this->levels->firstItem() : 0 }} to {{ $this->levels->lastItem() ?? 0 }} of {{ $total }} entries</span>
            <div>{{ $this->levels->links() }}</div>
        </div>
    </main>

    {{-- Create / Edit modal --}}
    @if ($showForm)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="closeForm">
        <div class="w-full max-w-md rounded-xl border border-white/10 bg-card p-5 shadow-xl">

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold">
                    {{ $editingUin ? 'Edit office type' : 'Add office type' }}
                </h3>
                <button type="button" wire:click="closeForm"
                    class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-white/10">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">

                {{-- Level: only levels not yet created (plus the current one when editing) --}}
                <div>
                    <label class="mb-1 block text-sm font-medium">Parent Office</label>
                    <select
                        wire:model.live="formLevel"
                        class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-primary [&>option]:bg-slate-900" {{ $editingUin ? 'disabled' : '' }}>
                        <option value="">Select Parent Office</option>
                        @foreach ($this->availableLevels as $level)
                        <option value="{{ $level->value }}" class="bg-slate-900">{{ $level->label() }}</option>
                        @endforeach
                    </select>
                    @error('formLevel')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Level name --}}
                <div>
                    <label class="mb-1 block text-sm font-medium">New Office Type Name</label>
                    <input
                        type="text"
                        wire:model="formName"
                        maxlength="100"
                        placeholder="e.g. Country Head Office"
                        class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
                    @error('formName')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="mb-1 block text-sm font-medium">Status</label>
                    <select
                        wire:model="formStatus"
                        class="h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary [&>option]:bg-gray-900">
                        <option value="100201">Active</option>
                        <option value="100202">Inactive</option>
                    </select>
                    @error('formStatus')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeForm"
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

    {{-- Delete confirmation --}}
    @if ($deletingUin)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="cancelDelete">
        <div class="w-full max-w-sm rounded-xl border border-white/10 bg-card p-5 shadow-xl">
            <h3 class="text-base font-semibold">Delete office type?</h3>
            <p class="mt-2 text-sm text-muted-foreground">
                The office type will be removed from this list. It can't be deleted while offices still use it.
            </p>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" wire:click="cancelDelete"
                    class="rounded border border-white/10 px-3 py-2 text-sm hover:bg-white/5">
                    Cancel
                </button>
                <button type="button" wire:click="delete('{{ $deletingUin }}')"
                    wire:loading.attr="disabled"
                    wire:target="delete"
                    class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700 disabled:opacity-60">
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        var AGREEMENTS_URL = "{{ route('agreements-name.data') }}";
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/header-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
</x-filament-panels::page>