<x-filament-panels::page>
    <main
        x-data="{
            selected: null,
            details: @js($this->getTileDetails()),
            tiles: @js($this->getTiles()),
            get selectedTile() {
                return this.tiles.find(t => t.key === this.selected) ?? null;
            },
            get selectedDetail() {
                return this.selected ? this.details[this.selected] : null;
            },
        }"
        class="space-y-6">

        {{-- Layout: calendar fixed on the left, tiles grid on the right --}}
        <div class="flex lg:flex-row flex-col mt-6 gap-6 items-start">

            {{-- Calendar — always visible, left side --}}
            <div class="lg:w-100 w-full">
                <div
                    id="calMenu"
                    class="rounded-md border border-white/10 bg-card shadow-xl">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">

                        <button
                            id="calPrev"
                            class="h-8 w-8 flex items-center justify-center rounded hover:bg-white/10">
                            &#10094;
                        </button>

                        <div
                            id="calTitle"
                            class="text-sm font-semibold"></div>

                        <button
                            id="calNext"
                            class="h-8 w-8 flex items-center justify-center rounded hover:bg-white/10">
                            &#10095;
                        </button>

                    </div>

                    {{-- Week Days --}}
                    <div class="grid grid-cols-7 text-center text-xs font-medium border-b border-white/10 py-2">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>

                    {{-- Calendar --}}
                    <div
                        id="calendarDays"
                        class="grid grid-cols-7 gap-1 p-2"></div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between border-y border-white/10 px-4 py-2">

                        <button
                            id="calToday"
                            class="h-8 px-3 text-sm rounded hover:bg-white/10">
                            Agreements
                        </button>

                        <span
                            id="calCount"
                            class="text-xs text-muted-foreground"></span>

                    </div>

                    <div
                        id="calList"
                        class="max-h-80 overflow-y-auto p-2"></div>

                </div>
            </div>

            {{-- Tiles — the first 3 tiles (that used to sit in the left column) are moved to the end --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:flex-1 w-full">
                @foreach ($this->getTiles() as $tile)
                <button type="button" @click="selected = (selected === '{{ $tile['key'] }}') ? null : '{{ $tile['key'] }}'" class="text-left tiles-btn">
                    <x-filament::section>
                        <div class="flex items-center gap-3">
                            <span @class([ 'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg' ,
                                match ($tile['color']) { 'success'=> 'bg-success-50 text-success-600 dark:bg-success-500/10',
                                'danger' => 'bg-danger-50 text-danger-600 dark:bg-danger-500/10',
                                'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10',
                                'info' => 'bg-info-50 text-info-600 dark:bg-info-500/10',
                                'gray' => 'bg-gray-100 text-gray-600 dark:bg-white/5',
                                default => 'bg-primary-50 text-primary-600 dark:bg-primary-500/10',
                                },
                                ])>
                                @svg($tile['icon'], 'h-5 w-5')
                            </span>

                            <span class="min-w-0">
                                <span
                                    class="tile-count block text-2xl font-bold text-gray-950 dark:text-white tiles-key"
                                    data-key="{{ $tile['key'] }}"
                                    data-current="{{ $tile['count'] }}">
                                    {{ $tile['count'] }}
                                </span>
                                <span class="block truncate text-sm text-gray-500 dark:text-gray-400">
                                    {{ $tile['label'] }}
                                </span>
                            </span>
                        </div>
                    </x-filament::section>
                </button>
                @endforeach
            </div>

        </div>

        {{-- Short review panel, shown once a tile is clicked --}}
        <div x-show="selected" x-cloak x-transition>
            <x-filament::section>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400" x-text="selectedTile?.label"></h4>
                        <p class="mt-1 text-2xl font-bold text-gray-950 dark:text-white" id="count-view"></p>
                        <p class="mt-2 max-w-2xl text-sm text-gray-600 dark:text-gray-300" x-text="selectedDetail"></p>
                    </div>
                    <x-filament::icon-button
                        icon="heroicon-o-x-mark"
                        label="Close"
                        color="gray"
                        @click="selected = null" />
                </div>

                <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                    Full agreement listing for this tile will render here once wired to real data.
                </p>
            </x-filament::section>
        </div>
    </main>

    <script>
        var AGREEMENTS_URL = "{{ route('agreements.data') }}";
        var DASHBOARD_STATS_URL = "{{ route('dashboard-data') }}";
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js" integrity="sha512-8LENNbXmzI/Gbj+OwXmqR6V4QaUAw0/porPzy1+dQoJqC0JPHedWoe0DDOTL2uHA5XXJyIsPtiMHH86pVlay6A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/dashboard-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</x-filament-panels::page>