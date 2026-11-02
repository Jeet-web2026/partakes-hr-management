$(document).ready(function () {
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
                ...overrides,
            },
            success: function (response) {
                AGREEMENTS = response.data;
                currentPage = response.current_page;

                renderTable(AGREEMENTS);
                renderPagination(response);

                $("#entriesLabel").text(
                    `Showing ${response.from ?? 0} to ${response.to ?? 0} of ${response.total} entries`,
                );

                Swal.close();
            },
        });
    }
    // fetchAgreements();

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
        // fetchAgreements(1);
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

    let sort = null;

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

                    <!-- Agreement Category (always visible, first col, matches thead) -->
                    <td class="px-5 py-4">
                        <div class="min-w-0">
                            <div class="truncate font-medium">
                                ${r.categoryLabel ?? "—"}
                            </div>
                        </div>
                    </td>

                    <td data-col="type"
                        class="px-5 py-4 text-muted-foreground hidden">
                        ${r.type ?? "—"}
                    </td>
                    
                    <td data-col="name"
                        class="px-5 py-4 text-muted-foreground">
                        ${r.name ?? "—"}
                    </td>

                    <td data-col="draftCount"
                        class="px-5 py-4 text-muted-foreground">
                        ${r.draftCount ?? "—"}
                    </td>
                    <!-- Action -->
                    <td class="px-5 py-4 text-right">
                        <a href="#"
                        data-id="${r.id}"
                        ${r.draftCount > 0 ? 'aria-disabled="true"' : ""}
                        class="delete-agreement-btn flex items-center justify-center gap-2 px-2 py-1.5 text-sm rounded text-red-400 ${
                            r.draftCount > 0
                                ? "opacity-40 cursor-not-allowed pointer-events-none"
                                : "hover:bg-white/5"
                        }">
                        <i class="ri-delete-bin-line text-base"></i>
                    </a>
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

                return `
                    <div
                        class="rounded-xl border border-white/10 bg-card overflow-hidden"
                        data-mobile-card="${r.id}">

                        <!-- Card Header -->
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="font-semibold text-lg truncate capitalize">
                                ${name}
                            </div>

                            <!-- Right Side -->
                            <div class="flex items-center gap-2 shrink-0">


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

                                        
                                            <a href="#"
                                            data-id="${r.id}"
                                            ${r.draftCount > 0 ? 'aria-disabled="true"' : ""}
                                            class="delete-agreement-btn gap-2 px-2 py-2 text-sm rounded text-red-400 ${
                                                r.draftCount > 0
                                                    ? "opacity-40 cursor-not-allowed pointer-events-none"
                                                    : "hover:bg-white/5"
                                            }">
                                            <i class="ri-delete-bin-line text-base"></i>
                                            Delete
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Card Body -->
                        <div class="grid grid-cols-2 gap-4 px-4 pb-4 pt-2">

                            <div>
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-cyan-400 mb-2">
                                    Category
                                </div>
                                <div class="text-xs text-muted-foreground truncate">
                                    ${r.categoryLabel ?? "—"}
                                </div>
                            </div>

                            <div>
                                <div class="flex flex-row items-center gap-3">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-cyan-400">
                                        Type
                                    </div>
                                    <div class="text-[10px] text-muted-foreground">
                                        ${r.type ?? "—"}
                                    </div>
                                </div>

                                <div class="flex flex-row items-center gap-3 mt-4">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-cyan-400">
                                        Total Drafts
                                    </div>
                                    <div class="text-[10px] text-muted-foreground">
                                        ${r.draftCount ?? "—"}
                                    </div>
                                </div>
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

        // applyColumnVisibility();
        // renderSortIcons();
    }
});
