$(document).ready(function () {
    // CSRF Token Header Requests
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    // Function which authenticate the user
    $(document).on("submit", "#requestLogin", function () {
        var e = this;

        var submitBtn = $(this).find("[type='submit']");
        submitBtn.prop("disabled", true);
        submitBtn.html(
            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading...`
        );
        $.ajax({
            url: $(this).attr("action"),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            dataType: "json",
            success: function (data) {
                if (data.status == true) {
                    window.location = data.redirect;
                } else {
                    $(".alert").remove();
                    $.each(data.errors, function (key, val) {
                        $("#errors-list").append(
                            `
                            <div class="alert alert-danger border-0 d-flex align-items-center" role="alert">
                                <p class="mb-0 flex-1">` +
                                val +
                                `</p><button class="btn-close"
                                    type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            `
                        );
                    });
                    submitBtn.html("Log In");
                    submitBtn.prop("disabled", false);
                }
            },
        });
        return false;
    });
});
