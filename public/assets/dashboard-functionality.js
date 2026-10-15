$(document).ready(function () {
    function fetchDashboardStat(organisationUin, year, month) {
        $.ajax({
            url: DASHBOARD_STATS_URL,
            type: "POST",
            dataType: "json",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                selected_orga_uin: organisationUin,
                year: year,
                month: month,
            },
            success: function (response) {
                updateTileCounts(response);
            },
            error: function (xhr) {
                console.error(
                    "Failed to fetch dashboard stats:",
                    xhr.responseText,
                );
            },
        });
    }

    function updateTileCounts(stats) {
        $(".tile-count").each(function () {
            const $el = $(this);
            const key = $el.data("key");

            if (!(key in stats)) return; // key not present in this response, leave as-is

            const from = parseInt($el.data("current"), 10) || 0;
            const to = parseInt(stats[key], 10) || 0;

            $({ count: from }).animate(
                { count: to },
                {
                    duration: 700,
                    easing: "swing",
                    step: function () {
                        $el.text(Math.floor(this.count));
                    },
                    complete: function () {
                        $el.text(to);
                        $el.data("current", to); // remember new value as the baseline for next update
                    },
                },
            );
        });
    }

    let companyId = null;
    let dateFilterType = "agreement_date";
    const $initial = $(".company-item")
        .filter(function () {
            return !$(this).find(".check-icon").hasClass("hidden");
        })
        .first();

    if ($initial.length) {
        companyId = $initial.data("company-id");
    }
    let AGREEMENTS = [];
    let perPage = 5;

    // ---------- Search & Date filter state ----------
    let searchTerm = "";
    let dateFrom = "";
    let dateTo = "";
    let statuses = [];

    function fetchAgreements(page = 1, overrides = {}) {
        $.ajax({
            url: AGREEMENTS_URL,
            type: "GET",
            dataType: "json",
            data: {
                page: page,
                per_page: perPage,
                search: searchTerm,
                date_from: dateFrom,
                date_to: dateTo,
                date_filter_type: dateFilterType,
                company_id: companyId,
                statuses,
                ...overrides,
            },
            success: function (response) {
                AGREEMENTS = response.data;
                currentPage = response.current_page;

                renderTable(AGREEMENTS);
                renderPagination(response);
                renderCalendar();
                renderCalendarEvents();

                $("#entriesLabel").text(
                    `Showing ${response.from ?? 0} to ${response.to ?? 0} of ${response.total} entries`,
                );

                Swal.close();
            },
        });
    }
    fetchAgreements();

    function renderPagination(data) {
        let html = "";

        html += `
            <button
                class="page-btn h-8 px-3 rounded-md border border-white/10 hover:bg-white/5 disabled:opacity-50"
                data-page="${data.current_page - 1}"
                ${!data.prev_page_url ? "disabled" : ""}
            >
                Prev
            </button>
        `;

        for (let i = 1; i <= data.last_page; i++) {
            html += `
                <button
                    class="page-btn h-8 w-8 rounded-md ${
                        i === data.current_page
                            ? "bg-primary text-primary-foreground"
                            : "border border-white/10 hover:bg-white/5"
                    }"
                    data-page="${i}">
                    ${i}
                </button>
            `;
        }

        html += `
            <button
                class="page-btn h-8 px-3 rounded-md border border-white/10 hover:bg-white/5 disabled:opacity-50"
                data-page="${data.current_page + 1}"
                ${!data.next_page_url ? "disabled" : ""}
            >
                Next
            </button>
        `;

        $("#pagination").html(html);
    }

    $(document).on("click", ".page-btn", function () {
        if ($(this).is(":disabled")) {
            return;
        }

        const page = $(this).data("page");

        fetchAgreements(page);
    });

    const $calendarDays = $("#calendarDays");
    const $calTitle = $("#calTitle");

    let current = new Date();

    function renderCalendar() {
        const year = current.getFullYear();
        const month = current.getMonth();

        fetchDashboardStat(companyId, year, month + 1);

        $calTitle.text(
            current.toLocaleString("default", {
                month: "long",
                year: "numeric",
            }),
        );

        $calendarDays.empty();

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const eventMap = {};

        AGREEMENTS.forEach(function (agreement) {
            if (agreement.date) {
                if (!eventMap[agreement.date]) {
                    eventMap[agreement.date] = {};
                }
                eventMap[agreement.date].created = true;
            }

            if (agreement.expiry) {
                if (!eventMap[agreement.expiry]) {
                    eventMap[agreement.expiry] = {};
                }
                eventMap[agreement.expiry].renewal = true;
            }
        });

        for (let i = 0; i < firstDay; i++) {
            $calendarDays.append("<div></div>");
        }

        const today = new Date();

        for (let day = 1; day <= daysInMonth; day++) {
            const isToday =
                today.getDate() === day &&
                today.getMonth() === month &&
                today.getFullYear() === year;

            const dateString = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

            const event = eventMap[dateString] || {};

            const hasCreated = event.created;
            const hasRenewal = event.renewal;

            $calendarDays.append(`
        <div class="flex flex-col items-center">

            <button
                class="
                    w-8
                    h-8
                    rounded-full
                    flex
                    items-center
                    justify-center
                    transition
                    ${isToday ? "bg-slate-700 text-white" : "hover:bg-white/10"}
                "
                data-day="${day}">

                ${day}

            </button>

            <div class="flex gap-1 mt-1 h-2">

                ${
                    hasCreated
                        ? '<span class="w-2 h-2 rounded-full bg-green-500"></span>'
                        : ""
                }

                ${
                    hasRenewal
                        ? '<span class="w-2 h-2 rounded-full bg-red-500"></span>'
                        : ""
                }

            </div>

        </div>
    `);
        }

        // Disable "Next" once we've reached the current real-world month
        const isCurrentMonth =
            year === today.getFullYear() && month === today.getMonth();

        $("#calNext")
            .prop("disabled", isCurrentMonth)
            .toggleClass(
                "opacity-40 cursor-not-allowed pointer-events-none",
                isCurrentMonth,
            );
    }

    $("#calPrev").on("click", function () {
        current.setMonth(current.getMonth() - 1);
        renderCalendar();
    });

    $("#calNext").on("click", function () {
        const today = new Date();
        const isCurrentMonth =
            current.getFullYear() === today.getFullYear() &&
            current.getMonth() === today.getMonth();

        if (isCurrentMonth) return; // safety net — button should already be disabled

        current.setMonth(current.getMonth() + 1);
        renderCalendar();
    });

    $("#calToday").on("click", function () {
        current = new Date();
        renderCalendar();
    });

    // ---------- Helpers ----------
    const fmtDate = (iso) => {
        if (!iso || iso === "-" || iso === "0000-00-00") {
            return "-";
        }

        const date = new Date(iso);

        if (isNaN(date.getTime())) {
            return "-";
        }

        return date.toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    };

    const expiryBadge = (d, parentAgreement) => {
        if (d === "Renewed") {
            return `<span class="rounded bg-blue-500/20 text-blue-300 px-1.5 py-0.5 text-[10px]">Renewed</span>`;
        }
        if (
            d === null ||
            d === undefined ||
            d === "" ||
            d === "-" ||
            isNaN(Number(d))
        ) {
            return `<span>-</span>`;
        }

        d = Number(d);

        if (d < 0) {
            return `<span class="rounded bg-destructive text-white px-1.5 py-0.5 text-[10px]">${d}</span>`;
        }

        if (d === 0) {
            return `<span class="rounded bg-green-500/20 text-green-400 px-1.5 py-0.5 text-[10px]">${d}</span>`;
        }

        if (d <= 90) {
            return `<span class="rounded bg-amber-500/20 text-amber-300 px-1.5 py-0.5 text-[10px]">${d}</span>`;
        }

        if (d <= 30) {
            return `<span class="rounded bg-destructive/80 text-white px-1.5 py-0.5 text-[10px]">${d}</span>`;
        }

        return `<span class="rounded bg-white/10 px-1.5 py-0.5 text-[10px]">${d}</span>`;
    };

    const sortValue = (r, key) =>
        ({
            firstParty: r.parties[0]?.org ?? "",
            secondParty: r.parties[1]?.org ?? "",
            date: r.date,
            expiry: r.expiry,
        })[key];

    // ---------- State (columns / sort / calendar month) ----------
    let sort = null; // {key, dir}
    const visible = {
        fileNo: false,
        category: false,
        type: false,
        firstParty: true,
        secondParty: true,
        date: true,
        expiry: true,
        expiry_by: true,
        signingDate: false,
        notaryDate: false,
        version: true,
        drafted: false,
        jurisdiction: false,
        remarks: false,
    };
    const today = new Date();
    let calMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    document
        .querySelectorAll('#colsMenu input[type="checkbox"]')
        .forEach((cb) => {
            // Sync checkbox UI state to match the `visible` object on load
            cb.checked = visible[cb.dataset.col] ?? false;

            cb.addEventListener("change", () => {
                visible[cb.dataset.col] = cb.checked;
                applyColumnVisibility();
            });
        });

    // ---------- Render table ----------
    // Server already applies search filtering, so we only sort client-side here.
    function renderTable(data = AGREEMENTS) {
        let rows = data;

        // Sorting
        if (sort) {
            rows = [...rows].sort((a, b) => {
                const av = sortValue(a, sort.key);
                const bv = sortValue(b, sort.key);

                if (av === bv) return 0;

                return (av < bv ? -1 : 1) * (sort.dir === "asc" ? 1 : -1);
            });
        }

        // =========================
        // DESKTOP TABLE
        // =========================

        const tbody = document.getElementById("tbody");

        if (!tbody) return;

        tbody.innerHTML = rows
            .map(
                (r) => `
            <tr class="border-b border-white/10 hover:bg-white/5 last:border-b-0">

                <!-- Agreement -->
                <td class="px-5 py-4">
                    <div class="min-w-0">
                        <a href="/view-agreement/${r.id}">
                            <div class="truncate font-medium">
                                ${r.name}
                            </div>
                        </a>
                    </div>
                </td>

                <td data-col="fileNo"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${r.fileNo ?? "—"}
                </td>

                <td data-col="category"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${r.categoryLabel ?? "—"}
                </td>

                <td data-col="type"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${r.type ?? "—"}
                </td>

                <td data-col="firstParty"
                    class="px-5 py-4 text-muted-foreground">
                    ${r.parties?.[0]?.org ?? "—"}
                </td>

                <td data-col="secondParty"
                    class="px-5 py-4 text-muted-foreground">
                    ${r.parties?.[1]?.org ?? "—"}
                </td>

                <td data-col="date"
                    class="px-5 py-4 text-muted-foreground">
                    ${fmtDate(r.date)}
                </td>

                <td data-col="expiry"
                    class="px-5 py-4 text-muted-foreground">
                    ${fmtDate(r.expiry)}
                </td>

                <td data-col="expiry_by"
                    class="px-5 py-4 text-muted-foreground">
                    ${expiryBadge(r.daysLeft, r.parentAgreement)}
                </td>

                <td data-col="signingDate"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${fmtDate(r.signingDate)}
                </td>

                <td data-col="notaryDate"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${fmtDate(r.notaryDate)}
                </td>

                <td data-col="version"
                    class="px-5 py-4 text-muted-foreground">
                    ${r.version ?? "—"}
                </td>

                <td data-col="drafted"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${r.drafted ?? "—"}
                </td>

                <td data-col="jurisdiction"
                    class="px-5 py-4 text-muted-foreground hidden">
                    ${r.jurisdiction ?? "—"}
                </td>

                <td data-col="remarks"
                    class="px-5 py-4 text-muted-foreground hidden">
                    <span class="line-clamp-2">
                        ${r.remarks ?? "—"}
                    </span>
                </td>

                <!-- Action -->
                <td class="px-5 py-4 text-right">
                    <div class="relative inline-block" data-menu>

                        <button
                            data-menu-toggle="row-${r.id}"
                            class="h-9 w-9 inline-flex items-center justify-center rounded hover:bg-white/10">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <circle cx="12" cy="12" r="1"/>
                                <circle cx="12" cy="5" r="1"/>
                                <circle cx="12" cy="19" r="1"/>

                            </svg>
                        </button>

                        <div
                            id="row-${r.id}"
                            class="hidden absolute right-0 mt-1 w-36 rounded-md border border-white/10 bg-card shadow-xl z-20 p-1">

                            <a href="/edit-agreement/${r.id}"
                                class="flex items-center gap-2 px-2 py-1.5 text-sm rounded hover:bg-white/5">
                                <i class="ri-edit-box-line"></i>
                                Edit
                            </a>

                            <a href="/renewal-agreement/${r.id}"
                                class="flex items-center gap-2 px-2 py-1.5 text-sm rounded hover:bg-white/5">
                                <i class="ri-file-add-line"></i>
                                Renew
                            </a>

                            <a href="#"
                                data-id="${r.id}"
                                class="generate-link-btn flex items-center gap-2 px-2 py-1.5 text-sm rounded hover:bg-white/5">
                                <i class="ri-links-line"></i>
                                Generate Link
                            </a>

                            <a href="#"
                                data-id="${r.id}"
                                class="delete-agreement-btn flex items-center gap-2 px-2 py-1.5 text-sm rounded hover:bg-white/5 text-red-400">
                                <i class="ri-delete-bin-5-line"></i>
                                Delete
                            </a>

                        </div>
                    </div>
                </td>

            </tr>
        `,
            )
            .join("");

        // =========================
        // MOBILE CARDS
        // =========================

        const mobileList = document.getElementById("mobileAgreementList");

        mobileList.innerHTML = rows
            .map((r) => {
                const name = r.name ?? "Unknown";

                // Generate initials
                const initials = name
                    .split(" ")
                    .map((word) => word.charAt(0))
                    .join("")
                    .substring(0, 2)
                    .toUpperCase();

                const phone = r.phone ?? r.mobile ?? r.phoneNumber ?? null;

                const email = r.email ?? r.emailAddress ?? null;

                const firstParty = r.parties?.[0]?.org ?? "—";

                const secondParty = r.parties?.[1]?.org ?? "—";

                return `
                <div
                    class="rounded-xl border border-white/10 bg-card overflow-hidden"
                    data-mobile-card="${r.id}">

                    <!-- Card Header -->
                    <div class="flex items-center justify-between px-4 py-3">

                        <a
                            href="/view-agreement/${r.id}"
                            class="flex items-center gap-3 min-w-0">

                            <!-- Name -->
                            <div class="min-w-0">

                                <div
                                    class="font-semibold text-lg truncate">

                                    ${name}

                                </div>

                            </div>

                        </a>


                        <!-- Right Side -->
                        <div class="flex items-center gap-2 shrink-0">

                            <!-- Date -->
                            <span
                                class="rounded-md bg-white/5 px-2 py-1
                                text-[10px] font-medium
                                text-muted-foreground capitalize">

                                ${fmtDate(r.date)}

                            </span>


                            <!-- Menu -->
                            <div class="relative" data-menu>

                                <button
                                    data-menu-toggle="mobile-row-${r.id}"
                                    class="h-8 w-8 flex items-center justify-center
                                    rounded hover:bg-white/10">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2">

                                        <circle cx="12" cy="12" r="1"/>
                                        <circle cx="12" cy="5" r="1"/>
                                        <circle cx="12" cy="19" r="1"/>

                                    </svg>

                                </button>


                                <div
                                    id="mobile-row-${r.id}"
                                    class="hidden absolute right-0 top-9
                                    w-36 rounded-md border border-white/10
                                    bg-card shadow-xl z-50 p-1">


                                    <a
                                        href="/edit-agreement/${r.id}"
                                        class="block px-3 py-2 text-sm rounded hover:bg-white/5">
                                        <i class="ri-edit-box-line"></i>
                                        Edit

                                    </a>

                                    <a
                                        href="/renewal-agreement/${r.id}"
                                        class="block px-3 py-2 text-sm rounded hover:bg-white/5">
                                        <i class="ri-file-add-line"></i>
                                        Renew

                                    </a>

                                    <a href="#"
                                        data-id="${r.id}"
                                        class="generate-link-btn flex items-center gap-2 px-2 py-1.5 text-sm rounded hover:bg-white/5">
                                        <i class="ri-links-line"></i>
                                        Generate Link
                                    </a>

                                    <a
                                        href="#"
                                        data-id="${r.id}"
                                        class="delete-agreement-btn block px-3 py-2 text-sm rounded hover:bg-white/5 text-red-400">
                                        <i class="ri-delete-bin-5-line"></i>
                                        Delete

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Card Body -->
                    <div class="grid grid-cols-2 gap-4 px-4 pb-4 pt-2">

                        <!-- Personal Info -->
                        


                        <!-- Organization -->
                        <div>

                            <div
                                class="text-[10px] font-semibold uppercase
                                tracking-wider text-cyan-400 mb-2">

                                Parties

                            </div>

                            <div
                                class="text-xs text-muted-foreground truncate">

                                ${firstParty}

                            </div>

                            ${
                                secondParty !== "—"
                                    ? `
                                    <div
                                        class="text-xs text-muted-foreground
                                        truncate mt-1">

                                        ${secondParty}

                                    </div>
                                    `
                                    : ""
                            }

                        </div>
                        <div>

                            <div class="flex flex-row items-center gap-3">
                            
                            <div
                                class="text-[10px] font-semibold uppercase
                                tracking-wider text-cyan-400">

                                File no.

                            </div>

                            <div class="text-[10px] text-muted-foreground">
                                ${r.fileNo ?? ""}
                            </div>
                            
                            </div>
                            <div class="flex flex-row items-center gap-3 mt-4">
                            
                            <div
                                class="text-[10px] font-semibold uppercase
                                tracking-wider text-cyan-400">

                               Verson

                            </div>

                            <div class="text-[10px] text-muted-foreground">
                                ${r.version ?? ""}
                            </div>
                            
                            </div>
                        </div>

                    </div>


                    <!-- Bottom Info -->
                    <div
                        class="border-t border-white/5
                        px-4 py-2.5 flex items-center
                        justify-between">

                        <div class="text-xs text-muted-foreground">

                            Expiry:
                            <span class="text-white/70">
                                ${fmtDate(r.expiry)}
                            </span>

                        </div>

                        <div>
                            ${expiryBadge(r.daysLeft, r.parentAgreement)}
                        </div>

                    </div>

                </div>
            `;
            })
            .join("");

        // =========================
        // EXISTING FUNCTIONS
        // =========================

        document.getElementById("entriesLabel").textContent =
            `Showing 1 to ${rows.length} of ${rows.length} entries`;

        applyColumnVisibility();
        renderSortIcons();
    }

    // ---------- Column visibility ----------
    function applyColumnVisibility() {
        Object.entries(visible).forEach(([key, on]) => {
            document.querySelectorAll(`[data-col="${key}"]`).forEach((el) => {
                el.classList.toggle("hidden", !on);
            });
        });
    }

    // ---------- Sorting ----------
    const iconChevrons = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>`;
    const iconUp = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>`;
    const iconDown = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>`;

    function renderSortIcons() {
        document.querySelectorAll(".sort-btn").forEach((btn) => {
            const key = btn.dataset.sort;
            const icon = btn.querySelector(".sort-icon");
            if (sort && sort.key === key)
                icon.innerHTML = sort.dir === "asc" ? iconUp : iconDown;
            else icon.innerHTML = iconChevrons;
        });
    }
    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".sort-btn");
        if (!btn) return;
        const key = btn.dataset.sort;
        if (!sort || sort.key !== key)
            sort = {
                key,
                dir: "asc",
            };
        else if (sort.dir === "asc")
            sort = {
                key,
                dir: "desc",
            };
        else sort = null;
        renderTable();
    });

    // ---------- Calendar events list ----------
    function renderCalendarEvents() {
        document.getElementById("calTitle").textContent =
            calMonth.toLocaleDateString("en-GB", {
                month: "long",
                year: "numeric",
            });
        const y = calMonth.getFullYear(),
            m = calMonth.getMonth();
        const inMonth = (iso) => {
            const d = new Date(iso);
            return d.getFullYear() === y && d.getMonth() === m;
        };
        const events = [];
        AGREEMENTS.forEach((a) => {
            if (inMonth(a.date))
                events.push({
                    date: a.date,
                    type: "new",
                    agreement: a,
                });
            if (inMonth(a.expiry))
                events.push({
                    date: a.expiry,
                    type: "renewal",
                    agreement: a,
                });
        });
        events.sort((a, b) => (a.date < b.date ? -1 : 1));

        const list = document.getElementById("calList");
        if (!events.length) {
            list.innerHTML = `<div class="py-3 text-center text-xs text-muted-foreground">No agreements or renewals this month.</div>`;
        } else {
            list.innerHTML = events
                .map(
                    (e) => `
                <div class="flex items-start justify-between gap-3 rounded-md px-3 py-2 hover:bg-white/5">
                <div class="min-w-0">
                    <div class="truncate text-sm font-medium">${e.agreement.name}</div>
                    <div class="truncate text-xs text-muted-foreground">${e.agreement.type}</div>
                </div>
                <div class="shrink-0 text-right">
                    <div class="text-xs font-medium">${fmtDate(e.date)}</div>
                    <span class="mt-1 inline-block rounded ${e.type === "renewal" ? "bg-destructive/80 text-white" : "bg-green-700"} px-1.5 py-0.5 text-[10px]">
                    ${e.type === "renewal" ? "Renewal" : "New"}
                    </span>
                </div>
                </div>
            `,
                )
                .join("");
        }
        document.getElementById("calCount").textContent =
            `${events.length} events`;
    }
    document.getElementById("calPrev").addEventListener("click", () => {
        calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() - 1, 1);
        renderCalendarEvents();
    });
    document.getElementById("calNext").addEventListener("click", () => {
        calMonth = new Date(calMonth.getFullYear(), calMonth.getMonth() + 1, 1);
        renderCalendarEvents();
    });
    document.getElementById("calToday").addEventListener("click", () => {
        calMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        renderCalendarEvents();
    });

    // ---------- Menu toggling ----------
    document.addEventListener("click", (e) => {
        const toggle = e.target.closest("[data-menu-toggle]");
        if (toggle) {
            const id = toggle.dataset.menuToggle;
            const wasHidden = document
                .getElementById(id)
                .classList.contains("hidden");
            document
                .querySelectorAll("[data-menu] > div[id]")
                .forEach((m) => m.classList.add("hidden"));
            if (wasHidden)
                document.getElementById(id).classList.remove("hidden");
            e.stopPropagation();
            return;
        }
        if (!e.target.closest("[data-menu]")) {
            document
                .querySelectorAll("[data-menu] > div[id]")
                .forEach((m) => m.classList.add("hidden"));
        }
    });

    // ---------- Init ----------
    renderTable();
    renderCalendarEvents();

    // ---------- Date range filter (server-side) ----------
    let selectedRange = []; // default selection

    const $filterBtn = $('[data-menu-toggle="calMenuForFilter"]');
    const defaultBtnLabel = $filterBtn.text().trim(); // "Filter by date"

    const fp = flatpickr("#agreementDateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        onClose(selectedDates) {
            selectedRange = selectedDates;
        },
    });

    // track which radio is selected
    document
        .querySelectorAll('input[name="dateFilterType"]')
        .forEach((radio) => {
            radio.addEventListener("change", (e) => {
                dateFilterType = e.target.value;
            });
        });

    ["calPrev", "calNext"].forEach((id) => {
        document.getElementById(id).addEventListener("click", () => {
            if (document.activeElement === fp.input) {
                fp.input.blur();
            }
        });
    });

    // Helper to format the button label from the selected range
    function updateFilterButtonLabel() {
        if (selectedRange.length !== 2) {
            $filterBtn.text(defaultBtnLabel);
            return;
        }

        const typeLabel =
            dateFilterType === "expiry_date" ? "Expiry Date" : "Agreement Date";

        const fromStr = fmtDate(selectedRange[0].toISOString().split("T")[0]);
        const toStr = fmtDate(selectedRange[1].toISOString().split("T")[0]);

        $filterBtn.html(`${typeLabel}: ${fromStr} - ${toStr}`);
    }

    function selectCompany($item) {
        const newCompanyId = $item.data("company-id");
        const companyName = $item.data("company");

        $("#addAgreementBtn").attr(
            "href",
            "/add-agreement?company=" + newCompanyId,
        );

        $("#companyName").text(companyName);

        $(".check-icon").addClass("hidden");
        $item.find(".check-icon").removeClass("hidden");

        $("#companyMenu").addClass("hidden");

        companyId = newCompanyId;
        fetchAgreements(1);
    }

    $(document).on("click", ".company-item", function () {
        selectCompany($(this));
    });

    $(document).ready(function () {
        // Pick whichever item should be selected on load
        const $initial = $(".company-item .check-icon:not(.hidden)").closest(
            ".company-item",
        );

        if ($initial.length) {
            selectCompany($initial);
        } else {
            selectCompany($(".company-item").first());
        }
    });

    $(document).on("click", ".delete-agreement-btn", function (e) {
        e.preventDefault();

        const agreementId = $(this).data("id");

        Swal.fire({
            icon: "warning",
            title: "Are you sure you want to delete this agreement?",
            html: `
                <p>Please note that the agreement can only be deleted if the following conditions are met:</p>
                <p>It has no linked or subsequent agreements.
                    No shared link has been created within the last 24 hours.</p>
                    <p>If either condition applies, the agreement cannot be deleted at this time.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
            background: "#0f172b",
            color: "#ffffff",
            didOpen: (popup) => {
                popup.style.border = "1px solid #ffffff14";
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = $('meta[name="csrf-token"]').attr("content");

                const form = $(`
                    <form action="/agreements/${agreementId}" method="POST" style="display: none;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                    </form>
                `);

                $("body").append(form);
                form.submit();
            }
        });
    });

    function initGenerateLinkModal() {
        let currentAgreementId = null;

        const modal = document.getElementById("generate-link-modal");
        const linkInput = document.getElementById("generated-link-input");
        const linkWrapper = document.getElementById("generated-link-wrapper");
        const generateBtn = document.getElementById("generate-link-btn-action");

        document.addEventListener("click", (e) => {
            const trigger = e.target.closest(".generate-link-btn");
            if (trigger) {
                e.preventDefault();
                currentAgreementId = trigger.dataset.id;
                linkWrapper.classList.add("hidden");
                linkInput.value = "";
                modal.classList.remove("hidden");
            }
        });

        document
            .getElementById("close-generate-link-modal")
            ?.addEventListener("click", () => {
                modal.classList.add("hidden");
            });

        generateBtn?.addEventListener("click", async () => {
            if (!currentAgreementId) return;
            generateBtn.disabled = true;
            generateBtn.innerHTML = `<i class="ri-loader-4-line animate-spin"></i> Generating...`;

            try {
                const res = await fetch(
                    `/agreements/${currentAgreementId}/generate-view-link`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                    },
                );
                if (!res.ok) throw new Error("Request failed");
                const data = await res.json();
                linkInput.value = data.url;
                linkWrapper.classList.remove("hidden");
            } catch (err) {
                alert("Failed to generate link. Please try again.");
            } finally {
                generateBtn.disabled = false;
                generateBtn.innerHTML = `<i class="ri-magic-line"></i> Generate Link`;
            }
        });

        document
            .getElementById("copy-generated-link")
            ?.addEventListener("click", () => {
                linkInput.select();
                navigator.clipboard.writeText(linkInput.value);
            });
    }

    initGenerateLinkModal();

    $(document).on("click", ".tiles-btn", function () {
        const key = $(this).find(".tiles-key").data("current");
        $("#count-view").text(key);
    });
});
