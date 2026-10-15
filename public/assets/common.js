$(document).ready(function () {
    $(document).on("click", ".x", function () {
        $(this).closest(".message-body").remove();
    });

    $(document).on("click", ".menu-toggle-btn", function (e) {
        e.stopPropagation();
        $(this).closest("[data-menu-wrapper]").find(".menu-dropdown").toggle();
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest("[data-menu-wrapper]").length) {
            $(".menu-dropdown").hide();
        }
    });
});
