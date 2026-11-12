@php
$inp = 'h-10 w-full rounded-md border border-white/10 bg-transparent px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary';
$sel = $inp . ' [&>option]:bg-slate-900';
$lbl = 'mb-1 block text-sm font-medium';
$err = 'mt-1 text-xs text-red-400';
@endphp

<x-filament-panels::page>
    <main class="mt-8">

        <!-- Toolbar -->
        <div class="flex flex-col gap-3 mb-3">
            <div class="flex items-center justify-between gap-3">

                {{-- Left side --}}
                <div class="flex items-center gap-2">

                    {{-- Search --}}
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none"
                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search office name or code…"
                            class="h-9 w-[200px] sm:w-[250px] lg:w-[350px] rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary">
                    </div>

                    {{-- Office type filter --}}
                    <select wire:model.live="typeFilter"
                        class="h-9 rounded-md border border-white/10 bg-transparent px-2 text-sm text-white focus:outline-none">
                        <option value="" class="bg-slate-800">All types</option>
                        @foreach ($this->officeTypes as $type)
                        <option value="{{ $type->Hiky_UIN }}" class="bg-slate-800">{{ $type->Hiky_Levl_Name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <button type="button" wire:click="openCreate" aria-label="Add Branch Office" title="Add Branch Office"
                        class="inline-flex h-9 w-9 sm:w-auto sm:px-4 items-center justify-center gap-2 rounded-md bg-[#3c83f6] text-sm font-medium text-white transition hover:bg-[#3c83f6]/90">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        <span class="hidden sm:inline">Add</span>
                    </button>
                    {{-- Replace with this page's Page_UIN from Mstr_Pages_Name --}}
                    <x-feedback-button page="10000019" />
                </div>
            </div>
        </div>

        <x-globalresponse />

        <!-- Table -->
        <div class="rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-5 py-3">Office</th>
                        <th class="px-5 py-3">Office Type</th>
                        <th class="px-5 py-3">Parent Office</th>
                        <th class="px-5 py-3">Contact</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Created By</th>
                        <th class="px-5 py-3">Created On</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($this->offices as $row)
                    <tr wire:key="office-{{ $row->Ofic_UIN }}" class="border-b border-white/5 last:border-0 hover:bg-white/[0.03]">

                        <td class="px-5 py-3 align-top">
                            <div class="font-medium">{{ $row->Ofic_Name }}</div>
                            <div class="text-xs text-muted-foreground">Code: {{ $row->Ofic_Code }}</div>
                        </td>

                        <td class="px-5 py-3 align-top">
                            <span class="inline-flex items-center rounded border border-[#3c83f6]/30 bg-[#3c83f6]/15 px-2.5 py-0.5 text-xs font-medium text-[#3c83f6]">
                                {{ $row->hierKey?->Hiky_Levl_Name ?? '-' }}
                            </span>
                        </td>

                        <td class="px-5 py-3 align-top text-muted-foreground">
                            {{ $row->parent?->Ofic_Name ?? '—' }}
                        </td>

                        <td class="px-5 py-3 align-top text-muted-foreground">
                            <div>{{ $row->Ofic_Emai ?: '—' }}</div>
                            @if ($row->Ofic_LdLi)
                            <div class="text-xs">{{ $row->Ofic_LdLi }}</div>
                            @endif
                        </td>

                        <td class="px-5 py-3 align-top">
                            @if ((int) $row->Stau_UIN === 100201)
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
                                <button type="button" @click="open = !open" @keydown.escape.window="open = false"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-white/10 transition"
                                    aria-haspopup="true" :aria-expanded="open">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <div x-show="open" x-cloak @click.outside="open = false" x-transition.origin.top.right
                                    class="absolute right-0 z-40 mt-1 w-44 rounded-md border border-white/10 bg-card py-1 shadow-xl">
                                    <button type="button" wire:click="openEdit('{{ $row->Ofic_UIN }}')" @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-white/5">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" wire:click="confirmDelete('{{ $row->Ofic_UIN }}')" @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-400 hover:bg-white/5">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-muted-foreground">No branch offices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-4 flex items-center justify-between text-xs text-muted-foreground">
            @php($total = $this->offices->total())
            <span>Showing {{ $total ? $this->offices->firstItem() : 0 }} to {{ $this->offices->lastItem() ?? 0 }} of {{ $total }} entries</span>
            <div>{{ $this->offices->links() }}</div>
        </div>
    </main>

    {{-- Create / Edit modal --}}
    @if ($showForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="closeForm">
        <div class="w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-xl border border-white/10 bg-card p-5 shadow-xl">

            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold">{{ $editingUin ? 'Edit branch office' : 'Add branch office' }}</h3>
                <button type="button" wire:click="closeForm"
                    class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-white/10">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form wire:submit="save" class="space-y-6">

                {{-- Office --}}
                <div>
                    <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Office</h4>
                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>
                            <label class="{{ $lbl }}">Office Type</label>
                            <select wire:model.live="form.Hiky_UIN" class="{{ $sel }}">
                                <option value="">Select Office Type</option>
                                @foreach ($this->officeTypes as $type)
                                <option value="{{ $type->Hiky_UIN }}">{{ $type->Hiky_Levl_Name }}</option>
                                @endforeach
                            </select>
                            @error('form.Hiky_UIN') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $lbl }}">Parent Office</label>
                            <select wire:model="form.Parn_Ofic_UIN" class="{{ $sel }}">
                                <option value="0">— None (top level) —</option>
                                @foreach ($this->parentOffices as $p)
                                <option value="{{ $p->Ofic_UIN }}">{{ $p->Ofic_Name }} ({{ $p->Ofic_Code }})</option>
                                @endforeach
                            </select>
                            @error('form.Parn_Ofic_UIN') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>

                        @foreach ([
                        ['Ofic_Code', 'Office Code', 'text'],
                        ['Ofic_Name', 'Office Name', 'text'],
                        ['Ofic_Strt_Dt', 'Start Date', 'date'],
                        ['Ofic_Clos_Dt', 'Close Date', 'date'],
                        ] as [$key, $label, $type])
                        <div>
                            <label class="{{ $lbl }}">{{ $label }}</label>
                            <input type="{{ $type }}" wire:model="form.{{ $key }}" class="{{ $inp }}">
                            @error('form.' . $key) <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Address</h4>
                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>
                            <label class="{{ $lbl }}">Country</label>
                            <select wire:model.live="form.Cutr_UIN" class="{{ $sel }}">
                                <option value="">Select Country</option>
                                @foreach ($this->countries as $uin => $name)
                                <option value="{{ $uin }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('form.Cutr_UIN') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $lbl }}">State</label>
                            <select wire:model.live="form.Stat_UIN" class="{{ $sel }}" @disabled(! $form['Cutr_UIN'])>
                                <option value="">Select State</option>
                                @foreach ($this->states as $uin => $name)
                                <option value="{{ $uin }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('form.Stat_UIN') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="{{ $lbl }}">District</label>
                            <select wire:model="form.Dist_UIN" class="{{ $sel }}" @disabled(! $form['Stat_UIN'])>
                                <option value="">Select District</option>
                                @foreach ($this->districts as $uin => $name)
                                <option value="{{ $uin }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('form.Dist_UIN') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>

                        @foreach ([
                        ['PinCod_UIN', 'Pincode'],
                        ['Loca_UIN', 'Locality Name'],
                        ['Stet_UIN', 'Street Name'],
                        ['Bild_No_Name', 'Building No & Name'],
                        ] as [$key, $label])
                        <div>
                            <label class="{{ $lbl }}">{{ $label }}</label>
                            <input type="text" wire:model="form.{{ $key }}" class="{{ $inp }}">
                            @error('form.' . $key) <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Contact & location --}}
                <div>
                    <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Contact &amp; Location</h4>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ([
                        ['Ofic_LdLi', 'Landline No', 'tel'],
                        ['Ofic_Emai', 'Email Address', 'email'],
                        ['Lon', 'Longitude', 'number'],
                        ['Lat', 'Latitude', 'number'],
                        ] as [$key, $label, $type])
                        <div>
                            <label class="{{ $lbl }}">{{ $label }}</label>
                            <input type="{{ $type }}" step="any" wire:model="form.{{ $key }}" class="{{ $inp }}">
                            @error('form.' . $key) <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        @endforeach

                        <div>
                            <label class="{{ $lbl }}">Status</label>
                            <select wire:model="form.Stau_UIN" class="{{ $sel }}">
                                <option value="100201">Active</option>
                                <option value="100202">Inactive</option>
                            </select>
                            @error('form.Stau_UIN') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeForm"
                        class="rounded border border-white/10 px-3 py-2 text-sm hover:bg-white/5">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="rounded bg-[#3c83f6] px-3 py-2 text-sm text-white hover:bg-[#3c83f6]/90 disabled:opacity-60">Save</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete confirmation --}}
    @if ($deletingUin)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        wire:keydown.escape.window="cancelDelete">
        <div class="w-full max-w-sm rounded-xl border border-white/10 bg-card p-5 shadow-xl">
            <h3 class="text-base font-semibold">Delete branch office?</h3>
            <p class="mt-2 text-sm text-muted-foreground">
                The office will be removed from this list. It can't be deleted while it still has child offices.
            </p>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" wire:click="cancelDelete"
                    class="rounded border border-white/10 px-3 py-2 text-sm hover:bg-white/5">Cancel</button>
                <button type="button" wire:click="delete('{{ $deletingUin }}')" wire:loading.attr="disabled"
                    wire:target="delete"
                    class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700 disabled:opacity-60">Delete</button>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/header-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
</x-filament-panels::page>