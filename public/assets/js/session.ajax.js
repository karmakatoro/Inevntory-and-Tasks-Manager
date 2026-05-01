$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Utilise $(document) au lieu de $(d) car 'd' n'est pas défini
    $(document).on("submit", "#session-open-form", function(e) {
        e.preventDefault(); // Empêche le rechargement de la page

        var form = $(this); // Récupère le formulaire
        var submitBtn = form.find("[type='submit']");
        var alertBox = $('#session-alert'); // Assure-toi que l'ID correspond

        // UI : Spinner et désactivation
        submitBtn.prop("disabled", true);
        submitBtn.find('.spinner-border').removeClass('d-none');
        alertBox.addClass('d-none').removeClass('alert-danger alert-success');

        $.ajax({
            url: $(this).attr("action"),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            dataType: "json",
            success: function (data) {
                if (data.status == true) {
                    window.location.href = data.redirect;
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
                    submitBtn.html("Session  cree");
                    submitBtn.prop("disabled", false);
                }
            },
        });
        return false;
    });
});