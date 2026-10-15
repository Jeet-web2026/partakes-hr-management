@props([
'companyInformation' => '',
'userData' => '',
'backurl' => '',
'title' => '',
'backurlforSettings' => ''
])
<header class="border-b border-white/10 bg-white/[0.03]">
    <div class="mx-auto flex flex-wrap gap-3 max-w-5xl items-center justify-between px-6 py-4 relative">
        <div class="flex flex-wrap gap-2 items-start">
            <div class="relative flex flex-row items-center" data-menu>
                <button type="button" data-menu-toggle="companyMenu"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-white/10 transition">
                    <!-- building icon -->
                    <i
                        class="cursor-pointer lg:block text-2xl ri-menu-line"></i>
                </button>
                <div id="companyMenu" class="hidden absolute lg:right-0  lg:right-auto mt-2 w-56 rounded-md border border-white/10 bg-card shadow-xl z-30 py-1">
                    <div class="px-3 py-1.5 text-xs font-semibold text-muted-foreground">Switch company</div>
                    <div class="border-t border-white/10 my-1"></div>
                    <!-- Companies -->
                    <button
                        data-company="{{ $companyInformation->Orga_Name }}"
                        data-company-id="{{ $companyInformation->Orga_UIN }}"
                        class="company-item flex w-full items-center justify-between px-3 py-1.5 text-sm hover:bg-white/5">
                        <span>{{ $companyInformation->Orga_Name }}</span>

                        <svg
                            class="check-icon"
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <div class="text-lg font-semibold" id="companyName">{{ $companyInformation->Orga_Name }}</div>
                <p class="text-xs text-muted-foreground">{{ $userData['data'][0]['User_Name'] ?? '' }}</p>
            </div>
        </div>

        <div class="flex flex-col items-end">
            <div class="text-xl font-semibold lg:absolute lg:left-[45%]">{{ $title }}</div>
            <!-- Company switcher -->
            <div class="flex flex-col items-end gap-2 bg-card order-first lg:order-none">
                <div class="text-xl font-semibold lg:absolute lg:left-[45%] hidden">Agreements</div>

                <!-- Company switcher -->
                <div class="relative flex flex-row items-center" data-menu-wrapper>
                    <button
                        type="button"
                        class="menu-toggle-btn inline-flex items-center justify-center h-9 w-9 rounded-md hover:bg-white/10 transition"
                        aria-label="Menu">
                        <i class="ri-more-2-fill text-lg"></i>
                    </button>

                    <div
                        class="menu-dropdown hidden absolute right-0 top-[50px] mt-8 lg:mt-2 w-40 rounded-md border border-white/10 bg-slate-900 shadow-xl z-50 py-1">

                        <a href="{{ request()->routeIs('filament.settings.pages.settings') ? '/dashboard' : $backurl }}"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10 transition">
                        <i class="ri-arrow-left-long-line"></i>
                        Back
                        </a>


                        <a href="{{ $backurlforSettings }}"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10 transition">
                        <i class="ri-home-9-line"></i>
                        Home
                        </a>


                        <A href="{{ \Filament\Facades\Filament::getPanel('settings')->getUrl() }}"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10 transition">
                        <i class="bi bi-gear"></i>
                        Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>