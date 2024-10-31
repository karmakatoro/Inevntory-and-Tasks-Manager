<div class="modal fade" id="product-category-modal" tabindex="-1" role="dialog" aria-labelledby="users-modalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="users-modalTitle">Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="errorsDiv"
                    class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade mb-xl-0"
                    style="display:none;" role="alert">
                    Errors</strong><br>
                    <div class="errorsList"></div>
                </div>
                <form class="needs-validation was-validated" method="POST"
                    action="{{ route('product-categories.store') }}" id="requestProductCategories" novalidate="">
                    <input type="hidden" name="id" id="productCategoryId" value="0">
                    <div class="row">
                        <div class="mb-3 col-lg-12 col-sm-12">
                            <label for="name" class="form-label">Designation</label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Enter Product Category" required="">
                            <div class="invalid-feedback">
                                Designation is required
                            </div>
                        </div>
                        <div class="col-lg-12 col-sm-12">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="statusProductCategory" name="status">
                                <option value="on" selected>Activated</option>
                                <option value="off">Desactivated</option>
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
        $(document).on("click", "#btnSave", function(e) {
            e.preventDefault();
            var form = $("#requestProductCategories");

            var submitBtn = $("#btnSave");
            var singleId = $("#productCategoryId");
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
                        Swal.fire({
                            title: "Done!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#1abc9c",
                        });
                        singleId.val("0");
                        $(form).trigger("reset");
                        $("#product-category-modal").modal("hide");
                        currentDt.ajax.reload();
                    } else if (response.status == false) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            confirmButtonColor: "#3bafda",
                        });
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
