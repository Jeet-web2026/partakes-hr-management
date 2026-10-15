$(document).ready(function () {
    function fetchAgreementNames(orgaUin, categoryId, typeId) {
        const $select = $("#agreementName");

        if (!categoryId || !typeId) {
            $select.html(
                '<option value="">Select agreement category & type first</option>',
            );
            return;
        }

        $select.html('<option value="">Loading…</option>');

        $.ajax({
            url: AGREEMENT_NAMES_URL,
            type: "GET",
            dataType: "json",
            data: {
                orga_uin: orgaUin,
                category: categoryId,
                type: typeId,
            },
            success: function (names) {
                const currentValue = $("#currentAgreementName").val();

                let html = names.length
                    ? '<option value="" class="bg-slate-900">Select agreement name</option>'
                    : '<option value="" class="bg-slate-900">No agreement name added</option>';

                names.forEach(function (n) {
                    const selected =
                        currentValue === n.Agmt_Name ? "selected" : "";
                    html += `<option value="${n.Agmt_Name}" class="bg-slate-900" ${selected}>${n.Agmt_Name}</option>`;
                });

                $select.html(html);
            },
            error: function (xhr) {
                $select.html(
                    '<option value="">Failed to load agreement names</option>',
                );
            },
        });
    }

    // Re-fetch whenever category or type changes
    $(document).on("change", "#agreementCategory, #agreementType", function () {
        const category = $("#agreementCategory").val();
        const type = $("#agreementType").val();

        fetchAgreementNames(ORGA_UIN, category, type);
    });

    // On initial page load (edit mode), if category+type are already pre-filled, fetch immediately
    $(document).ready(function () {
        const category = $("#agreementCategory").val();
        const type =
            $("#currentAgreementType").val() || $("#agreementType").val();

        if (category && type) {
            fetchAgreementNames(ORGA_UIN, category, type);
        }
    });

    function loadAgreementTypes(categoryId, selectedType = null) {
        $("#agreementType").html(
            '<option value="" class="bg-slate-900">Loading...</option>',
        );

        if (!categoryId) {
            $("#agreementType").html(
                '<option value="" class="bg-slate-900">Select Agreement Type</option>',
            );
            return;
        }

        $.ajax({
            url: "/agreement-types/" + categoryId,
            type: "GET",
            dataType: "json",

            success: function (response) {
                let options =
                    '<option value="" class="bg-slate-900">Select Agreement Type</option>';

                $.each(response, function (index, type) {
                    let selected =
                        selectedType == type.Agmt_Type_UIN ? "selected" : "";

                    options += `
                <option value="${type.Agmt_Type_UIN}" ${selected} class="bg-slate-900">
                    ${type.Agmt_Type_Name}
                </option>
            `;
                });

                $("#agreementType").html(options);
            },

            error: function () {
                $("#agreementType").html(
                    '<option value="" class="bg-slate-900">Select Agreement Type</option>',
                );
            },
        });
    }

    $(document).on("click", "#submitBtn", function (e) {
        e.preventDefault();

        Swal.fire({
            html: `
                <p>This agreement is being renewed. Once saved, the Internal Notes cannot be modified or deleted. Please review all details before proceeding.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Renew",
            cancelButtonText: "Cancel",
            background: "#0f172b",
            confirmButtonColor: "#155dfc",
            color: "#ffffff",
            didOpen: (popup) => {
                popup.style.border = "1px solid #ffffff14";
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest("form").submit();
            }
        });
    });

    $(document).on("click", "#addBtn", function (e) {
        e.preventDefault();

        Swal.fire({
            html: `
                <p>You are adding a new agreement. Once saved, the Internal Notes cannot be modified or deleted. Please review all details before proceeding.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Add",
            cancelButtonText: "Cancel",
            background: "#0f172b",
            confirmButtonColor: "#155dfc",
            color: "#ffffff",
            didOpen: (popup) => {
                popup.style.border = "1px solid #ffffff14";
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest("form").submit();
            }
        });
    });

    $(document).on("click", "#editSaveBtn", function (e) {
        e.preventDefault();

        Swal.fire({
            html: `
                <p>Confirm Agreement Submission Once saved, Internal Notes cannot be modified or deleted. Please ensure that all information is correct before saving.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Update",
            cancelButtonText: "Cancel",
            background: "#0f172b",
            confirmButtonColor: "#155dfc",
            color: "#ffffff",
            didOpen: (popup) => {
                popup.style.border = "1px solid #ffffff14";
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest("form").submit();
            }
        });
    });

    $(document).on("click", "#submitDraftBtn", function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Are you sure you want to save this agreement as a draft?",
            html: `
                <p>This draft cannot be edited after saving. Please review all details carefully before proceeding.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Save Draft",
            cancelButtonText: "Cancel",
            background: "#0f172b",
            confirmButtonColor: "#155dfc",
            color: "#ffffff",
            didOpen: (popup) => {
                popup.style.border = "1px solid #ffffff14";
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest("form").submit();
            }
        });
    });

    $(document).on("click", "#submitAgreementCreateBtn", function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Are you sure you want to create this agreement name?",
            html: `
                <p>Please verify that all information is complete and accurate before proceeding.</p>
            `,
            showCancelButton: true,
            confirmButtonText: "Save Name",
            cancelButtonText: "Cancel",
            background: "#0f172b",
            confirmButtonColor: "#155dfc",
            color: "#ffffff",
            didOpen: (popup) => {
                popup.style.border = "1px solid #ffffff14";
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).closest("form").submit();
            }
        });
    });

    // On category change, load its types (no pre-selection — user is actively switching)
    $("#agreementCategory").on("change", function () {
        loadAgreementTypes($(this).val());
    });

    // On page load, fetch types for the agreement's already-saved category,
    // and pre-select its already-saved type
    $(document).ready(function () {
        const savedType = $("#currentAgreementType").val();
        loadAgreementTypes($("#agreementCategory").val(), savedType);
    });

    $("#attachment").on("change", function () {
        const file = this.files[0];

        if (!file) {
            $("#selectedFileName").addClass("hidden").text("");
            return;
        }

        const size = (file.size / 1024 / 1024).toFixed(2);

        $("#selectedFileName").removeClass("hidden").html(`
            <strong>${file.name}</strong><br>
            <span class="text-slate-400">${size} MB</span>
        `);
    });

    window.addParty = function addParty() {
        const container = document.getElementById("parties");
        const row = document.createElement("div");
        row.className =
            "party-row grid grid-cols-[90px_1fr_1fr_80px] gap-3 items-center";
        row.dataset.index = window.partyIndex;

        row.innerHTML = `
            <div class="text-sm font-medium text-slate-400">
                ${partyIndex + 1}th Party
                <input type="hidden" name="parties[${partyIndex}][role]" value="${partyIndex + 1}th Party">
            </div>
            <div><input class="input" name="parties[${partyIndex}][org_name]" placeholder="Organization"></div>
            <div><input class="input" name="parties[${partyIndex}][signatory]" placeholder="Authorised Signatory"></div>
            <div class="flex items-center justify-end gap-3">
                <input type="checkbox" class="checkbox vs-toggle" name="parties[${partyIndex}][is_primary]" value="1">
                <button type="button" class="btn btn-outline-danger" onclick="removeParty(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;

        container.appendChild(row);
        partyIndex++;
    };

    window.removeParty = function removeParty(btn) {
        btn.closest(".party-row").remove();
        window.partyIndex = parseInt(window.partyIndex - 1);
    };

    const partiesEl = document.getElementById("parties");
    if (!partiesEl) return;

    partiesEl.addEventListener("change", function (e) {
        if (!e.target.classList.contains("vs-toggle")) return;
        toggleDivider(e.target);
    });

    // Run on load for any pre-checked toggles (edit page scenario)
    document.querySelectorAll(".vs-toggle:checked").forEach(toggleDivider);

    function toggleDivider(checkbox) {
        const row = checkbox.closest(".party-row");

        if (checkbox.checked) {
            if (
                row.nextElementSibling &&
                row.nextElementSibling.classList.contains("vs-divider")
            ) {
                return;
            }

            const divider = document.createElement("div");
            divider.className = "vs-divider flex items-center gap-3 py-1";
            divider.innerHTML = `
            <div class="flex-1 border-t border-white/10"></div>
            <span class="text-xs uppercase tracking-wider text-slate-500">vs</span>
            <div class="flex-1 border-t border-white/10"></div>
        `;

            row.insertAdjacentElement("afterend", divider);
        } else {
            if (
                row.nextElementSibling &&
                row.nextElementSibling.classList.contains("vs-divider")
            ) {
                row.nextElementSibling.remove();
            }
        }
    }

    const editorEl = document.getElementById("remarksEditor");
    const isEditable = editorEl.dataset.editable === "true";

    const quill = new Quill("#remarksEditor", {
        theme: "snow",
        placeholder: "Add notes about this agreement...",
        readOnly: !isEditable,

        modules: {
            toolbar: isEditable
                ? [
                      [
                          { font: [] },
                          { size: ["small", false, "large", "huge"] },
                      ],
                      [{ header: [1, 2, 3, 4, 5, 6, false] }],
                      ["bold", "italic", "underline", "strike"],
                      [{ color: [] }, { background: [] }],
                      [{ script: "sub" }, { script: "super" }],
                      [
                          { list: "ordered" },
                          { list: "bullet" },
                          { list: "check" },
                      ],
                      [{ indent: "-1" }, { indent: "+1" }],
                      [{ align: [] }],
                      ["blockquote", "code-block"],
                      ["link"],
                      ["clean"],
                  ]
                : false,

            history: {
                delay: 1000,
                maxStack: 100,
                userOnly: true,
            },
        },
    });

    $("form").on("submit", function () {
        $("#remarks").val(quill.root.innerHTML);
    });

    // Only sync hidden input + wire up submit-time value when editable
    if (isEditable) {
        quill.on("text-change", function () {
            document.getElementById("remarks").value = quill.root.innerHTML;
        });
    }

    window.removeParty = function (btn) {
        const wrap = document.getElementById("parties");
        const rows = wrap.querySelectorAll(".party-row");

        if (rows.length <= 1) {
            Swal.fire({
                icon: "warning",
                title: "At least one party is required",
                text: "An agreement must have at least one party.",
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

        btn.closest(".party-row").remove();
    };

    const allowedExtensions = [
        "pdf",
        "doc",
        "docx",
        "xls",
        "xlsx",
        "jpg",
        "jpeg",
        "png",
    ];
    const maxSizeBytes = 1 * 1024 * 1024;

    $("#document").on("change", function (e) {
        const file = this.files[0];

        if (!file) {
            return;
        }

        const fileName = file.name;
        const fileExt = fileName.split(".").pop().toLowerCase();

        // 1. Check extension
        if (!allowedExtensions.includes(fileExt)) {
            Swal.fire({
                icon: "error",
                title: "Invalid File Type",
                text: "Only PDF, Word, Excel, JPG, JPEG, and PNG files are allowed.",
                confirmButtonColor: "#d33",
                background: "#0f172b",
                color: "#ffffff",
                didOpen: (popup) => {
                    popup.style.border = "1px solid #ffffff14";
                },
            });
            $(this).val(""); // reset input
            return;
        }

        // 2. Check size
        if (file.size > maxSizeBytes) {
            Swal.fire({
                icon: "error",
                title: "File Too Large",
                text:
                    "File size must not exceed 1 MB. Your file is " +
                    (file.size / (1024 * 1024)).toFixed(2) +
                    " MB.",
                confirmButtonColor: "#d33",
                background: "#0f172b",
                color: "#ffffff",
                didOpen: (popup) => {
                    popup.style.border = "1px solid #ffffff14";
                },
            });
            $(this).val("");
            return;
        }

        // 3. Basic corruption check for images (tries to load it)
        if (["jpg", "jpeg", "png"].includes(fileExt)) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = new Image();
                img.onload = function () {
                    // valid image, do nothing — passes
                };
                img.onerror = function () {
                    Swal.fire({
                        icon: "error",
                        title: "Corrupt File",
                        text: "The uploaded image appears to be corrupt or unreadable.",
                        confirmButtonColor: "#d33",
                        background: "#0f172b",
                        color: "#ffffff",
                        didOpen: (popup) => {
                            popup.style.border = "1px solid #ffffff14";
                        },
                    });
                    $("#document").val("");
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }

        // 4. Basic corruption check for PDFs (checks file header)
        if (fileExt === "pdf") {
            const reader = new FileReader();
            reader.onload = function (event) {
                const arr = new Uint8Array(event.target.result).subarray(0, 5);
                let header = "";
                for (let i = 0; i < arr.length; i++) {
                    header += String.fromCharCode(arr[i]);
                }
                if (header !== "%PDF-") {
                    Swal.fire({
                        icon: "error",
                        title: "Corrupt File",
                        text: "The uploaded PDF appears to be corrupt or invalid.",
                        confirmButtonColor: "#d33",
                        background: "#0f172b",
                        color: "#ffffff",
                        didOpen: (popup) => {
                            popup.style.border = "1px solid #ffffff14";
                        },
                    });
                    $("#document").val("");
                }
            };
            reader.readAsArrayBuffer(file.slice(0, 5));
        }

        // Optional: show success toast if all checks pass
        // Swal.fire({ icon: 'success', title: 'File looks good!', timer: 1200, showConfirmButton: false });
    });

    // Optional: block form submit if no valid file selected but required
    const MAX_FILE_SIZE = 1 * 1024 * 1024; // 1 MB in bytes

    function validateFileInput(input) {
        if (!input || input.files.length === 0) return true;

        const file = input.files[0];

        if (file.size >= MAX_FILE_SIZE) {
            Swal.fire({
                icon: "warning",
                title: "File Too Large",
                text: "File size must be less than 1 MB.",
                background: "#0f172b",
                confirmButtonColor: "#155dfc",
                color: "#ffffff",
                didOpen: (popup) => {
                    popup.style.border = "1px solid #ffffff14";
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    file.value = "";
                }
            });
            return false;
        }

        return true;
    }

    $("#attachment").on("change", function () {
        validateFileInput(this);
    });

    $("#upload-input").on("change", function () {
        validateFileInput(this);
    });

    const historyPanel = $("#history-panel");
    const historyBackdrop = $("#history-panel-backdrop");
    const historyContent = $("#history-panel-content");

    function openHistoryPanel() {
        historyBackdrop.removeClass("hidden");
        historyPanel.removeClass("hidden");
        requestAnimationFrame(() => {
            historyPanel.removeClass("translate-x-full");
        });
    }

    function closeHistoryPanel() {
        historyPanel.addClass("translate-x-full");
        setTimeout(() => {
            historyPanel.addClass("hidden");
            historyBackdrop.addClass("hidden");
        }, 300);
    }
    const id = $("#agreementHistoryBtn").data("id");

    historyContent.html(`<div class="text-white/40 text-xs">Loading...</div>`);
    $(document).on("click", "#agreementHistoryBtn", function () {
        openHistoryPanel();
    });

    $.get(`/agreements/${id}/history`, function (res) {
        renderHistory(res.data);
    }).fail(function () {
        historyContent.html(
            `<div class="text-red-400 text-xs">This agreement has no history.</div>`,
        );
    });

    $("#history-panel-close, #history-panel-backdrop").on(
        "click",
        closeHistoryPanel,
    );

    function renderHistory(items) {
        if (!items || items === undefined) {
            historyContent.html(
                `<div class="text-white/40 text-xs">No history found.</div>`,
            );
            return;
        }

        const html = items
            .map((item, index) => {
                const isLast = index === items.length - 1;
                const isRenewed = item.status?.toLowerCase() === "renewed";

                const badgeClass = isRenewed
                    ? "bg-red-500/15 text-red-400"
                    : "bg-emerald-500/15 text-emerald-400";

                const badgeLabel = isRenewed ? "Renewed" : "Current";

                return `
                    <div class="rounded-lg border border-white/10  p-3 ${!isLast ? "mb-2" : ""} ${!isRenewed ? "bg-[#407645]" : "bg-white/[0.03]"}">
                        <div class="flex items-start justify-between">
                            <div class="text-sm font-semibold text-white capitalize">${item.name ?? "-"}</div>
                             <div class="text-[11px] text-white/40">File No: ${item.file_no ?? "-"}</div>
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <div class="text-[11px] text-white/50 whitespace-nowrap ml-2">${item.created_date ?? "-"} - ${item.expiry_date ?? "-"}</div>
                            <span class="rounded px-2 py-0.5 text-[10px] font-medium ${badgeClass}">${badgeLabel}</span>
                        </div>
                    </div>
                `;
            })
            .join("");

        historyContent.html(html);
    }

    document
        .getElementById("expiry_date")
        .addEventListener("blur", function () {
            const selectedDate = this.value;

            if (!selectedDate) return;

            const expiryDate = new Date(selectedDate);

            if (isNaN(expiryDate.getTime())) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            expiryDate.setHours(0, 0, 0, 0);

            if (expiryDate < today) {
                Swal.fire({
                    title: "Past Expiry Date Selected",
                    text: 'You have selected a past Expiry Date. If saved, the agreement status will be set to "Expired".',
                    confirmButtonText: "OK",
                    background: "#0f172b",
                    confirmButtonColor: "#155dfc",
                    color: "#ffffff",
                    didOpen: (popup) => {
                        popup.style.border = "1px solid #ffffff14";
                    },
                });
            }
        });
});
