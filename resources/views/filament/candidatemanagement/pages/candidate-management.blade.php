<x-filament-panels::page>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #calMenu {
            width: 350px !important;
        }

        @media (max-width: 600px) {
            #calMenu {
                right: -40px;
                width: 300px !important;
            }
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

                        {{-- Rows --}}
                        <div class="flex items-center gap-2">
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

                        {{-- Filter --}}
                        <div class="relative" data-menu>

                            <button
                                type="button"
                                data-menu-toggle="calMenuForFilter"
                                aria-label="Filter by date"
                                title="Filter by date"
                                class="inline-flex h-9 w-9 sm:w-auto sm:px-3 items-center justify-center rounded-md border border-white/10 hover:bg-white/5 transition">
                                <i class="bi bi-funnel text-base"></i>
                                <p class="showdateRange"></p>
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
                                    <div class="mb-3 text-sm">

                                        {{-- Status dropdown --}}
                                        <div class="flex flex-col gap-1 w-full">
                                            <select
                                                id="statusFilter"
                                                wire:model.live="statusFilter"
                                                class="block w-full rounded-md border border-gray-800 bg-slate-900 px-3 py-2 text-sm
                                                focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-900 cursor-pointer">
                                                <option value="">All Statuses</option>
                                                @foreach ($this->getStatuses() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Date (shown once a status is selected) --}}
                                        @if ($statusFilter)
                                        <div class="flex flex-col gap-1 w-full sm:w-56">
                                            <label for="statusDate" class="font-medium text-gray-700">
                                                {{ $statuses[$statusFilter] }} Date
                                            </label>
                                            <input
                                                id="statusDate"
                                                type="date"
                                                wire:model.live="statusDate"
                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                                                focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600">
                                        </div>
                                        @endif

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

                        {{-- Filter By Status --}}
                        <div class="relative" data-menu>

                            <button
                                type="button"
                                data-menu-toggle="statusMenuForFilter"
                                aria-label="Filter by status"
                                title="Filter by status"
                                class="inline-flex h-9 w-9 sm:w-auto sm:px-3 items-center justify-center gap-2 rounded-md border border-white/10 hover:bg-white/5 transition">
                                <i class="bi bi-filter text-base"></i>
                                <span id="statusFilterLabel" class="hidden sm:inline text-sm">Status</span>
                            </button>

                            {{-- Status Filter Dropdown --}}
                            <div
                                id="statusMenuForFilter"
                                class="hidden absolute right-0 left-[-50px] mt-2 w-70 rounded-md border border-white/10 bg-card shadow-xl z-50">

                                <div class="p-4">

                                    <div class="flex items-center justify-between mb-3">
                                        <label class="text-sm font-medium">
                                            Filter By Status
                                        </label>

                                        <button
                                            id="closeStatusFilter"
                                            type="button"
                                            class="h-8 w-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <div class="flex flex-col gap-2 text-sm mb-3">

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="status-checkbox accent-blue-600" value="new">
                                            New Candidate
                                        </label>

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="status-checkbox accent-blue-600" value="short_listed">
                                            Candidate Short Listed
                                        </label>

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="status-checkbox accent-blue-600" value="interview_scheduled">
                                            Schedule Interview
                                        </label>

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="status-checkbox accent-blue-600" value="selected">
                                            Candidate Selected
                                        </label>

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="status-checkbox accent-blue-600" value="offer_issued">
                                            Offer Letter Issued
                                        </label>

                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" class="status-checkbox accent-blue-600" value="offer_accepted">
                                            Offer Accepted
                                        </label>

                                    </div>

                                    <div class="flex justify-end gap-2 mt-4">

                                        <button
                                            id="clearStatusFilter"
                                            type="button"
                                            class="px-3 py-2 rounded border border-white/10 text-sm hover:bg-white/5">
                                            Clear
                                        </button>

                                        <button
                                            id="applyStatusFilter"
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
                                    placeholder="Search registrations…"
                                    class="h-9 w-[250px] lg:w-[350px] rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary">

                            </div>

                        </div>

                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2">

                        {{-- Calendar --}}
                        <div class="relative" data-menu>

                            <button
                                type="button"
                                data-menu-toggle="calMenu"
                                aria-label="Calendar"
                                title="Calendar"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 hover:bg-white/5 transition">
                                <i class="bi bi-calendar3"></i>
                            </button>


                            {{-- Calendar Dropdown --}}
                            <div
                                id="calMenu"
                                class="hidden absolute right-0 mt-2 max-w-96 rounded-md border border-white/10 bg-card shadow-xl z-50">

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


                        {{-- Generate Link --}}
                        <a
                            id="generateLinkBtn"
                            href="javascript::void()"
                            data-url="{{ url('') }}"
                            data-csrf="{{ csrf_token() }}"
                            data-share-type="candidate"
                            data-share-id="{{ $recordId ?? '' }}"
                            aria-label="Generate link"
                            title="Generate link"
                            class="inline-flex h-9 w-9 sm:w-auto sm:px-4 items-center justify-center gap-2 rounded-md bg-[#3c83f6] text-sm font-medium text-[#3c83f6]-foreground hover:bg-[#3c83f6]/90 transition">

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
                                Generate
                            </span>

                        </a>

                        <div id="linkModal" wire:ignore
                            class="hidden fixed inset-0 z-[100] items-center justify-center p-4"
                            role="dialog" aria-modal="true" aria-labelledby="linkModalTitle">

                            <div data-close-modal class="absolute inset-0 bg-black/60"></div>

                            <div class="relative w-full max-w-md rounded-lg border border-white/10 bg-card shadow-xl">

                                <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                                    <h3 id="linkModalTitle" class="text-base font-semibold">Share link</h3>
                                    <button type="button" data-close-modal aria-label="Close"
                                        class="text-muted-foreground hover:text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="px-5 py-4 space-y-3 text-sm">

                                    <p id="linkLoading" class="text-muted-foreground">Generating link...</p>

                                    <p id="linkError" class="hidden text-red-400"></p>

                                    <div id="linkResult" class="hidden space-y-3">
                                        <div class="flex items-center gap-2">
                                            <input id="linkInput" type="text" readonly
                                                class="flex-1 min-w-0 rounded-md border border-white/10 bg-transparent px-3 py-2 text-sm">
                                            <button id="linkCopyBtn" type="button"
                                                class="rounded-md bg-[#3c83f6] px-3 py-2 text-sm font-medium hover:bg-[#3c83f6]/90 transition">
                                                Copy
                                            </button>
                                        </div>
                                        <p class="text-xs text-muted-foreground">
                                            Expires: <span id="linkExpiry"></span> (valid for 24 hours)
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-end px-5 py-3 border-t border-white/10">
                                    <button type="button" data-close-modal
                                        class="rounded-md border border-white/10 px-4 py-2 text-sm hover:bg-white/5">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>


                        {{-- Columns --}}
                        <div class="relative" data-menu>

                            <button
                                type="button"
                                data-menu-toggle="colsMenu"
                                aria-label="Show columns"
                                title="Show columns"
                                class="inline-flex h-9 w-9 sm:w-auto sm:px-3 items-center justify-center gap-2 rounded-md border border-white/10 hover:bg-white/5 transition">

                                <i class="bi bi-window-plus"></i>

                            </button>


                            {{-- Columns Menu --}}
                            <div
                                id="colsMenu"
                                class="hidden absolute right-0 mt-2 w-60 max-h-96 overflow-y-auto rounded-md border border-white/10 bg-card shadow-xl z-50 p-1">

                                <div class="px-2 py-1.5 text-xs font-semibold text-muted-foreground">
                                    Show columns
                                </div>

                                <div class="border-t border-white/10 my-1"></div>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="FaNm" class="accent-primary" checked>
                                    First Name
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="MiNm" class="accent-primary">
                                    Middle Name
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="SuNm" class="accent-primary" checked>
                                    Surname
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Gend" class="accent-primary">
                                    Gender
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="DOB" class="accent-primary" checked>
                                    Date of Birth
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="MoTu" class="accent-primary">
                                    Mother Tongue
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Cutr_UIN" class="accent-primary">
                                    Country Name
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="PinCod_UIN" class="accent-primary">
                                    Pincode of Present Location
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Mo_No" class="accent-primary" checked>
                                    Mobile Number
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Own_Mo" class="accent-primary">
                                    You Have Own Mobile
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Mo_Type" class="accent-primary">
                                    Type of Mobile
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="MO_OS" class="accent-primary">
                                    Operating System
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Emai" class="accent-primary" checked>
                                    Email Address
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Own_Lptp" class="accent-primary">
                                    You Have Own Laptop for Personal Use in Running Condition
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Lptp_OS" class="accent-primary">
                                    Laptop OS
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Drvi_Lisc" class="accent-primary">
                                    You Have Valid Driving Licence
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Vcle_Type_Lisc" class="accent-primary">
                                    Vehicle Type Licence
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Rlca" class="accent-primary">
                                    Are You Ready to Relocate for Your Job
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Rlca_Area" class="accent-primary">
                                    Relocation Area
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Job_Intr" class="accent-primary" checked>
                                    Interested for Job
                                </label>

                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-white/5 text-sm cursor-pointer">
                                    <input type="checkbox" data-col="Job_Type" class="accent-primary" checked>
                                    Job Type
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
                    placeholder="Search registrations..."
                    class="h-10 w-full rounded-md border border-white/10 bg-transparent pl-9 pr-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary">

            </div>

        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block rounded-xl border border-white/10 bg-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10 bg-white/[0.04] text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        <th class="px-3 py-3">Attached Bio Data</th>

                        <th data-col="FaNm" class="px-5 py-3">
                            <button data-sort="FaNm"
                                class="sort-btn inline-flex items-center gap-1 uppercase tracking-wider hover:text-white">
                                First Name
                                <span class="sort-icon"></span>
                            </button>
                        </th>

                        <th data-col="MiNm" class="px-5 py-3 hidden">
                            Middle Name
                        </th>

                        <th data-col="SuNm" class="px-5 py-3">
                            <button data-sort="SuNm"
                                class="sort-btn inline-flex items-center gap-1 uppercase tracking-wider hover:text-white">
                                Surname
                                <span class="sort-icon"></span>
                            </button>
                        </th>

                        <th data-col="Gend" class="px-5 py-3 hidden">
                            Gender
                        </th>

                        <th data-col="DOB" class="px-5 py-3">
                            <button data-sort="DOB"
                                class="sort-btn inline-flex items-center gap-1 uppercase tracking-wider hover:text-white">
                                Date of Birth
                                <span class="sort-icon"></span>
                            </button>
                        </th>

                        <th data-col="MoTu" class="px-5 py-3 hidden">
                            Mother Tongue
                        </th>

                        <th data-col="Cutr_UIN" class="px-5 py-3 hidden">
                            Country Name
                        </th>

                        <th data-col="PinCod_UIN" class="px-5 py-3 hidden">
                            Pincode of Present Location
                        </th>

                        <th data-col="Mo_No" class="px-5 py-3">
                            Mobile Number
                        </th>

                        <th data-col="Own_Mo" class="px-5 py-3 hidden">
                            You Have Own Mobile
                        </th>

                        <th data-col="Mo_Type" class="px-5 py-3 hidden">
                            Type of Mobile
                        </th>

                        <th data-col="MO_OS" class="px-5 py-3 hidden">
                            Operating System
                        </th>

                        <th data-col="Emai" class="px-5 py-3">
                            Email Address
                        </th>

                        <th data-col="Own_Lptp" class="px-5 py-3 hidden">
                            You Have Own Laptop for Personal Use in Running Condition
                        </th>

                        <th data-col="Lptp_OS" class="px-5 py-3 hidden">
                            Laptop OS
                        </th>

                        <th data-col="Drvi_Lisc" class="px-5 py-3 hidden">
                            You Have Valid Driving Licence
                        </th>

                        <th data-col="Vcle_Type_Lisc" class="px-5 py-3 hidden">
                            Vehicle Type Licence
                        </th>

                        <th data-col="Rlca" class="px-5 py-3 hidden">
                            Are You Ready to Relocate for Your Job
                        </th>

                        <th data-col="Rlca_Area" class="px-5 py-3 hidden">
                            Relocation Area
                        </th>

                        <th data-col="Job_Intr" class="px-5 py-3">
                            <button data-sort="Job_Intr"
                                class="sort-btn inline-flex items-center gap-1 uppercase tracking-wider hover:text-white">
                                Interested for Job
                                <span class="sort-icon"></span>
                            </button>
                        </th>

                        <th data-col="Job_Type" class="px-5 py-3">
                            Job Type
                        </th>

                        <th class="px-5 py-3 text-right">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody id="tbody"></tbody>
            </table>
        </div>


        <!-- Mobile Cards -->
        <div id="mobileAgreementList" class="lg:hidden space-y-3"></div>

        <!-- Pagination (static) -->
        <div class="mt-4 flex items-center justify-between text-xs text-muted-foreground">
            <span id="entriesLabel"></span>
            <div id="pagination" class="flex items-center gap-1"></div>
        </div>

        <x-common.generate-link-modal />
    </main>

    <script>
        var AGREEMENTS_URL = "";
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js" integrity="sha512-8LENNbXmzI/Gbj+OwXmqR6V4QaUAw0/porPzy1+dQoJqC0JPHedWoe0DDOTL2uHA5XXJyIsPtiMHH86pVlay6A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/base-agreement-page-functionalities.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
</x-filament-panels::page>