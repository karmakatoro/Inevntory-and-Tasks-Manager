<div class="modal fade" id="users-modal" tabindex="-1" role="dialog" aria-labelledby="users-modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="users-modalTitle">Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="errorsDiv"
                    class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade mb-xl-0"
                    style="display:none;" role="alert">
                    Errors</strong><br>
                    <div class="errorsList"></div>
                </div>
                <form class="needs-validation was-validated" method="POST" action="{{ route('users.store') }}"
                    id="requestUsers" novalidate="">
                    <input type="hidden" name="id" id="userId">
                    <div class="row">
                        <div class="mb-3 col-lg-6 col-sm-12">
                            <label for="name" class="form-label">Names</label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="John Doe" required="">
                            <div class="invalid-feedback">
                                Names are required
                            </div>
                        </div>

                        <div class="mb-3 col-lg-6 col-sm-12">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="expemple@email.xyz" aria-describedby="inputGroupPrepend"
                                    required="">
                                <div class="invalid-feedback">
                                    Please enter a valid email.
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6 col-sm-12">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" id="phone"
                                placeholder="+000 000 000 000" required="">
                            <div class="invalid-feedback">
                                Please provide a phone number.
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <label for="gender" class="form-label">Select the gender</label>
                            <select class="form-select" name="gender" id="gender">
                                <option value="m" selected>Male</option>
                                <option value="f">Female</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="admin">Admin</option>
                                <option value="user" selected>User</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <label for="accred" class="form-label">Level</label>
                            <select class="form-select" id="accred" name="accred">
                                <option value="1">One</option>
                                <option value="2" selected>Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="statusUser" name="status">
                                <option value="on">Activated</option>
                                <option value="off" selected>Desactivated</option>
                            </select>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="btnSave" class="btn btn-primary">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script>
    $(document).ready(function() {
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
                timer: 6000,
            });
        }
        // sweet alert fire
        function sweet_fire(title, message, state) {
            Swal.fire(title, message, state);
        }
        $(document).on("click", "#btnSave", function(e) {
            e.preventDefault();
            var form = $("#requestUsers")

            var submitBtn = $("#btnSave");
            var singleId = $("#userId");
            submitBtn.html(
                `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading...`
            );
            submitBtn.prop("disabled", true);

            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                success: function(response) {

                    if (response.status == true) {
                        sweet_alert_toast("success", response.message);
                        singleId.val("0");
                        $(form).trigger("reset");
                        $("#users-modal").modal("hide");
                        //currentDt.ajax.reload();
                    } else if (response.status == false) {
                        sweet_alert_toast("warning", response.message);
                    }
                    submitBtn.html("Save changes");
                    submitBtn.prop("disabled", false);
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON.errors;
                    var errorString = "";
                    $.each(errors, function(key, value) {
                        errorString += value[0] + "<br>";
                    });
                    $(".errorsList").html(errorString);
                    $("#errorsDiv").css("display", "");
                    $("#errorsDiv").addClass("show");
                    submitBtn.html("Save changes");
                    submitBtn.prop("disabled", false);
                },
            });

        });
    })
</script>
