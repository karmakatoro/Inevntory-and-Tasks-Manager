$(document).ready(function () {
    // CSRF Token Header Requests
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    // sweete alert compoment 1 response
    function sweet_alert_toast(icon, message) {
        Swal.fire({
            position: "top-right",
            icon: icon,
            text: message,
            toast: true,
            showConfirmButton: false,
            timerProgressBar: true,
            timer: 3000,
        });
    }
    // sweet alert fire
    function sweet_fire(title, message, state) {
        Swal.fire(title, message, state);
    }
    // Function de delete one row
    function delete_row(btn) {
        let id = btn.attr("data-id");
        let url = btn.attr("data-action");
        Swal.fire({
            title: "Supression",
            text: "Voulez-vous vraiment supprimer cet enregistrement?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Oui, supprimer!",
            cancelButtonText: "Annuler",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: "delete",
                    data: {
                        id: id,
                    },
                    success: function (response) {
                        if (response.status == true) {
                            sweet_alert_toast("success", response.message);
                            currentDt.ajax.reload();
                        } else {
                            Swal.fire("Erreur", response.message, "warning");
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if (jqXHR.status === 403) {
                            sweet_fire("Ooops...", "Accès refusé", "warning");
                        } else {
                            sweet_fire("Ooops...", "Accès refusé", "warning");
                        }
                    },
                });
            }
        });
    }
    // call delete function
    $(document).on("click", ".btn-delete", function (e) {
        e.preventDefault();
        delete_row($(this));
    });

    // Select Multiple Records
    $(document).on("click", "#checkbox-bulk-customers-select", function () {
        $(".check-row").prop("checked", $(this).prop("checked"));
        console.log("ok");
    });

    // delete multiples or upate multiples
    $(document).on("click", "#btnAppliquer", function () {
        var checkedCount = $(".check-row:checked").length;

        if (checkedCount < 1) {
            sweet_fire(
                "Ooops...",
                "Vous devez selectionner au minimum 2 enregistrements",
                "warning"
            );
        } else {
            // select bulk select element
            var isDeleteOrToogle = $("#bulkSelect").val();
            if (isDeleteOrToogle == "none") {
                sweet_fire(
                    "Ooops...",
                    "Vous devez selectionner une action",
                    "warning"
                );
            } else {
                var all_id = [];
                $('input:checkbox[name="single-row"]:checked').each(
                    function () {
                        all_id.push($(this).val());
                    }
                );
                // check if is delete or desactivate
                var url = $("#bulkSelect")
                    .find("option:selected")
                    .data("action");
                var method = "";
                var data = "";
                if (isDeleteOrToogle == 1 || isDeleteOrToogle == 2) {
                    method = "post";
                    if (isDeleteOrToogle == 1) {
                        data = {
                            all_id: all_id,
                            onoff: "on",
                        };
                    } else {
                        method = "post";
                        data = {
                            all_id: all_id,
                            onoff: "off",
                        };
                    }
                } else if (isDeleteOrToogle == 3) {
                    method = "delete";
                    data = {
                        all_id: all_id,
                    };
                }
                Swal.fire({
                    title: "Actions groupées",
                    text: "Voulez-vous vraiment continuer cette action?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Oui, continuer!",
                    cancelButtonText: "Annuler",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: method,
                            data: data,
                            success: function (response) {
                                if (response.status == true) {
                                    sweet_alert_toast(
                                        "success",
                                        response.message
                                    );
                                    currentDt.ajax.reload();
                                } else if (response.status == false) {
                                    sweet_alert_toast(
                                        "Ooops...",
                                        response.message,
                                        "warning"
                                    );
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                if (jqXHR.status === 403) {
                                    sweet_alert_toast(
                                        "Ooops...",
                                        "Accès refusé",
                                        "warning"
                                    );
                                } else {
                                    sweet_alert_toast(
                                        "Ooops...",
                                        "Une erreur est survenue",
                                        "warning"
                                    );
                                }
                            },
                        });
                    }
                });
            }
        }
    });
});
