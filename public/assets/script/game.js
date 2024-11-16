$(document).ready(function () {

    function getBaseUrl() {
        // Get the current URL
        var currentUrl = window.location.href;

        // Check if the URL contains the string "https://localhost/projects/Helionline/public/"
        if (currentUrl.includes("https://localhost/projects/Helionline/public/")) {
            // Split the URL into parts
            var urlParts = currentUrl.split("/");

            // Construct the base URL
            var baseUrl = urlParts.slice(0, urlParts.length - 1).join("/");

            return baseUrl;
        } else {
            // Return the current URL if it doesn't match the pattern
            return '';
        }
    }

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let loading = `
            <div class="col text-center">
                <span class="spinner-grow" role="status" aria-hidden="true"></span>
            </div>`;

    $(document.body).on("click", ".load-game", function () {
        $("#loading").fadeIn();
        var id = $(this).data("id");
        $("#crud-result").html(loading);
        $.ajax({
            type: "POST",
            url: getBaseUrl() + "/load-game",
            data: {
                id: id,
            },
            success: function (data) {
                $("#load-game-result").html(data);
             
                $("#loading").fadeOut();
            },
            error: function (data) {
                $("#loading").fadeOut();
            },
        });
    });

});
