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
    let currentPage = 1;
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

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }
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
        category: true,
        type: false,
        name: true,
        version: true,
        approved_by: true,
        approved_date: true,
        upload_draft_file_name: false,
    };
    const today = new Date();
    let calMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    document
        .querySelectorAll('#colsMenu2 input[type="checkbox"]')
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

                    <td data-col="version"
                        class="px-5 py-4 text-muted-foreground">
                        ${r.version ?? "—"}
                    </td>

                    <td data-col="approved_by"
                        class="px-5 py-4 text-muted-foreground">
                        ${r.approvedBy ?? "—"}
                    </td>

                    <td data-col="approved_date"
                        class="px-5 py-4 text-muted-foreground">
                        ${fmtDate(r.approvedDate)}
                    </td>

                   <td data-col="upload_draft_file_name"
                            class="px-5 py-4 flex flex-col gap-2 text-muted-foreground hidden">
                            ${
                                r.uploadDraftFileName
                                    ? `<a href="/storage/${r.uploadDraftFileName}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-500 hover:underline">
                                        <i class="ri-file-line text-base"></i>
                                        <span class="truncate max-w-[140px]">Draft Attachment</span>
                                    </a>`
                                    : "—"
                            }
                            ${
                                r.uploadChecklistFileName
                                    ? `<a href="/storage/${r.uploadChecklistFileName}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-green-600 hover:text-green-500 hover:underline">
                                        <i class="ri-file-line text-base"></i>
                                        <span class="truncate max-w-[140px]">Checklist Attachment</span>
                                    </a>`
                                    : "—"
                            }
                        </td>

                    <!-- Action -->
                    <td class="px-5 py-4">
                        <div class="relative flex justify-center items-center" data-menu>

                            <!-- 3 Dot Button -->
                            <button
                                type="button"
                                data-menu-toggle="agreement-row-${r.id}"
                                class="h-8 w-8 flex items-center justify-center rounded hover:bg-white/10"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="1"/>
                                    <circle cx="12" cy="5" r="1"/>
                                    <circle cx="12" cy="19" r="1"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div
                                id="agreement-row-${r.id}"
                                class="hidden absolute right-0 top-9 w-40 rounded-md border border-white/10 bg-card shadow-xl z-50 p-1"
                            >

                                <!-- Generate Link -->
                                <a
                                    href="#"
                                    data-id="${r.id}"
                                    class="generate-link-btn flex items-center gap-2 px-3 py-2 text-sm rounded hover:bg-white/5"
                                >
                                    <i class="ri-links-line"></i>
                                    Generate Link
                                </a>

                                <!-- Delete -->
                                <a
                                    href="#"
                                    data-id="${r.id}"
                                    class="delete-agreement-btn flex items-center gap-2 px-3 py-2 text-sm rounded hover:bg-white/5 text-red-400"
                                >
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

                return `
                    <div
                        class="rounded-xl border border-white/10 bg-card overflow-hidden"
                        data-mobile-card="${r.id}">

                        <!-- Card Header -->
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="font-semibold text-lg truncate">
                                ${name}
                            </div>

                            <!-- Right Side -->
                            <div class="flex items-center gap-2 shrink-0">

                                <span
                                    class="rounded-md bg-white/5 px-2 py-1
                                    text-[10px] font-medium
                                    text-muted-foreground capitalize">

                                    ${fmtDate(r.approvedDate)}

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

                                        <a href="#"
                                            data-id="${r.id}"
                                            class="generate-link-btn flex items-center justify-center gap-2 px-2 py-1.5 text-sm rounded hover:bg-white/5">
                                            <i class="ri-links-line"></i>
                                            Generate Link
                                        </a>

                                        <a
                                            href="#"
                                            data-id="${r.id}"
                                            class="delete-agreement-btn block px-3 py-2 text-sm rounded hover:bg-white/5 text-red-400">
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
                                        Version
                                    </div>
                                    <div class="text-[10px] text-muted-foreground">
                                        ${r.version ?? "—"}
                                    </div>
                                </div>

                                <div class="flex flex-row items-center gap-3 mt-4">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-cyan-400">
                                        Approved By
                                    </div>
                                    <div class="text-[10px] text-muted-foreground">
                                        ${r.approvedBy ?? "—"}
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Bottom Info -->
                        <div class="border-t border-white/5 px-4 py-2.5 flex items-center justify-between">
                            <div class="text-xs text-muted-foreground">
                                Approved:
                                <span class="text-white/70">${fmtDate(r.approvedDate)}</span>
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

    // ---------- Search (server-side) ----------
    const searchInput = document.getElementById("searchInput");

    function runSearch(e) {
        searchTerm = e.target.value;
        fetchAgreements(1);
    }

    searchInput.addEventListener("blur", runSearch);
    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            runSearch(e);
            searchInput.blur(); // optional: also removes focus after Enter
        }
    });

    const searchInputDesktop = document.getElementById("searchInputDesktop");

    searchInputDesktop.addEventListener("blur", runSearch);
    searchInputDesktop.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            runSearch(e);
            searchInputDesktop.blur(); // optional: also removes focus after Enter
        }
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

    document.getElementById("applyDate").addEventListener("click", () => {
        if (selectedRange.length !== 2) {
            Swal.fire({
                icon: "warning",
                title: "Date Range Required",
                text: "Please select a date range.",
                confirmButtonText: "OK",
                background: "#0f172b",
                confirmButtonColor: "#155dfc",
                color: "#ffffff",
                didOpen: (popup) => {
                    popup.style.border = "1px solid #ffffff14";
                },
            });
            return;
        }

        dateFrom = selectedRange[0].toISOString().split("T")[0];
        dateTo = selectedRange[1].toISOString().split("T")[0];

        updateFilterButtonLabel(); // <-- reflect selection on the button

        fetchAgreements(1, { dateFilterType }); // pass along which date field to filter by
        document.getElementById("calMenuForFilter").classList.add("hidden");
    });

    document.getElementById("closeDateRange").addEventListener("click", () => {
        document.getElementById("calMenuForFilter").classList.add("hidden");
    });

    document.getElementById("clearDate").addEventListener("click", () => {
        document.getElementById("agreementDateRange")._flatpickr.clear();

        selectedRange = [];
        dateFrom = "";
        dateTo = "";
        dateFilterType = "agreement_date";
        document.querySelector(
            'input[name="dateFilterType"][value="agreement_date"]',
        ).checked = true;

        $filterBtn.text(defaultBtnLabel); // <-- reset button label

        fetchAgreements(1);
    });

    document.getElementById("perPageSelect").addEventListener("change", (e) => {
        perPage = parseInt(e.target.value, 10);
        fetchAgreements(1);
    });

    function selectCompany($item) {
        const newCompanyId = $item.data("company-id");
        const companyName = $item.data("company");

        $("#draftAgreementBtn").attr(
            "href",
            "/draft-agreement?company=" + newCompanyId,
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
            title: "Are you sure you want to delete this draft?",
            html: `
                <p>Once deleted, the draft cannot be recovered. Please confirm before proceeding.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Yes, delete",
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
                    <form action="/draft/${agreementId}" method="POST" style="display: none;">
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
                    `/drafts/${currentAgreementId}/generate-view-link`,
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
});
