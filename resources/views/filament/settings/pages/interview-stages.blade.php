<x-filament-panels::page>
    <main class="mt-8">
        {{-- Flash messages --}}
        <x-globalresponse />

        <!-- Toolbar -->
        <div class="mb-6">
            <div class="flex items-center justify-between gap-3">
                <div class="relative w-full">
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
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search interview stages…"
                        class="h-9 w-full rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3 hidden">Stage</th>
                        <th class="px-5 py-3">Stage Name</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->stages as $stage)
                    <tr wire:key="stage-{{ $stage->getKey() }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">
                        <td class="px-5 py-3 whitespace-nowrap hidden">Stage {{ $loop->iteration }}</td>
                        <td class="px-5 py-3">{{ $stage->Stage_Name }}</td>
                        <td class="px-5 py-3">
                            @if ($stage->Stage_Status)
                            <span class="inline-flex rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-medium text-emerald-400">Active</span>
                            @else
                            <span class="inline-flex rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium text-muted-foreground">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button
                                type="button"
                                wire:click="openEdit('{{ $stage->getKey() }}')"
                                class="inline-flex h-8 items-center gap-1.5 rounded-md border border-white/10 px-3 text-xs hover:bg-white/5 transition">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-muted-foreground">No interview stages found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    {{-- Edit modal --}}
    @if ($editingId)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="closeEdit">
        <div class="w-full max-w-md rounded-xl border border-white/10 bg-card p-5 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold">Edit interview stage</h3>
                <button type="button" wire:click="closeEdit"
                    class="h-8 w-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Stage Name</label>
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