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
    // Function which will always initialize datatables with api response data
    var currentDt = "";
    function dispay_datatable(dataTableId, columns, custom) {
        currentDt = $(`#${dataTableId}`).DataTable({
            autoWidth: false,
            order: [0, "ASC"],
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            paging: true,
            language: {
                url: $(`#${dataTableId}`).attr("data-lang"),
            },
            ajax: {
                url: $(`#${dataTableId}`).attr("data-api-url"),
                data: function (data) {
                    data.custom = custom;
                },
            },
            iDisplayLength: "10",
            columns: columns,
            lengthMenu: [10, 25, 50, 100],
        });
    }

    dispay_datatable(
        "usersDt",
        [
            {
                data: "checkbox",
                name: "checkbox",
                orderable: false,
                searchable: false,
            },
            {
                data: "name",
                name: "name",
                className: "text-900 sort pe-1 align-middle white-space-nowrap",
            },
            {
                data: "contact",
                name: "contact",
                className: "text-900 sort pe-1 align-middle white-space-nowrap",
            },
            {
                data: "address",
                name: "address",
                className:
                    "text-900 sort pe-1 align-middle white-space-nowrap ps-5",
            },
            {
                data: "join",
                name: "join",
                className: "text-900 sort pe-1 align-middle white-space-nowrap",
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
        ""
    );
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
    // summit create or update

    function updateOrCreate(formId, modalId) {
        $(`#${formId}`).submit(function (e) {
            e.preventDefault();
            const fd = new FormData(this);

            var submitBtn = $(this).find("[type='submit']");
            var singleId = $(this).find("[name='id']");
            submitBtn.html("Traitement...");
            submitBtn.prop("disabled", true);

            $.ajax({
                url: $(this).attr("action"),
                method: $(this).attr("method"),
                data: fd,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (response) {
                    submitBtn.html("Sauvegarder");
                    submitBtn.prop("disabled", false);
                    if (response.status == true) {
                        sweet_alert_toast("success", response.message);
                        singleId.val("0");
                        $(`#${formId}`).trigger("reset");
                        $(`#${modalId}`).modal("hide");
                        currentDt.ajax.reload();
                    } else if (response.status == false) {
                        sweet_fire(
                            "Ooops, erreur!!!",
                            response.message,
                            "warning"
                        );
                    }
                },
                error: function (xhr, status, error) {
                    submitBtn.html("Sauvegarder");
                    submitBtn.prop("disabled", false);
                    var errors = xhr.responseJSON.errors;
                    var errorString = "";
                    $.each(errors, function (key, value) {
                        errorString += value[0] + "<br>";
                    });
                    $(".errorsList").html(errorString);
                    $("#errorsDiv").css("display", "");
                    $("#errorsDiv").addClass("show");
                },
            });
        });
    }
    updateOrCreate("requestUsers", "add-modal");

    // select multiples
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

    let isClicked = false;

    $(document).on("click", ".img-preview, .camera-span", function () {
        if (!isClicked) {
            isClicked = true;
            $("#upload-file").click();
            isClicked = false;
        }
    });

    $("#upload-file").on("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $(".img-preview").attr("src", e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
});
