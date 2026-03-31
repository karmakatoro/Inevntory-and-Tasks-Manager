<div class="modal fade" id="movement-stock-modal" tabindex="-1" role="dialog" aria-labelledby="users-modalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="users-modalTitle">Stock Movement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="errorsDiv"
                    class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade mb-xl-0"
                    style="display:none;" role="alert">
                    Errors</strong><br>
                    <div class="errorsList"></div>
                </div>
                <form class="needs-validation was-validated" method="POST" action="{{ route('products-stock.store') }}"
                    id="requestStockMovement" novalidate="">
                    <input type="hidden" name="id" id="movementId" value="0">

                    <div class="row">
                        <div class="mb-3 col-lg-12 col-sm-12">
                            <label for="accred" class="form-label">Product</label>
                            <select data-product-price="{{ route('product-price') }}"
                                class="form-control select2-product" id="product_id" name="product_id"
                                data-toggle="select2" data-placeholder="Choose ...">
                                @foreach ($products as $product)
                                    <option data-image="{{ asset($product->photo) }}" value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12 col-sm-12">
                            <label for="quantity" class="form-label">Unit Price</label>
                            <input type="text" class="form-control" id="unitPrice" required="" disabled>
                        </div>
                        <div class="mb-3 col-lg-12 col-sm-12">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" id="quantity" required>
                            <div class="invalid-feedback">
                                Please provide a quantity.
                            </div>
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
        $('.select2-product').select2({
            dropdownParent: $('#movement-stock-modal'),
            width: '100%',
            templateResult: function(option) {
                if (!option.id) {
                    return option.text;
                }
                var imageUrl = $(option.element).data('image');
                var $option = $(
                    '<div><img src="' + imageUrl +
                    '" class="img-avatar" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;" />' +
                    option.text + '</div>'
                );
                return $option;
            },
            templateSelection: function(option) {
                if (!option.id) {
                    return option.text;
                }
                var imageUrl = $(option.element).data('image');
                var $selectedOption = $(
                    '<div><img src="' + imageUrl +
                    '" class="img-avatar" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;" />' +
                    option.text + '</div>'
                );
                return $selectedOption;
            }
        });
        var select = $('.select2-product');
        var productIdPrice = select.val();
        var productUrl = select.attr("data-product-price");
        $.ajax({
            url: productUrl,
            method: 'get',
            data: {
                id: productIdPrice
            },
            success: function(response) {
                if (response.status == true) {
                    $("#unitPrice").val(response.price);
                }
            },
        });

        $(document).on("change", ".select2-product", function(e) {
            e.preventDefault();
            let valueOptionSelected = $(this).val();
            let url = $(this).attr("data-product-price");
            $.ajax({
                url: url,
                method: 'get',
                data: {
                    id: valueOptionSelected
                },
                success: function(response) {
                    if (response.status == true) {
                        $("#unitPrice").val(response.price);
                    }
                },
            });
        });

        $(document).on("click", "#btnSave", function(e) {
            e.preventDefault();
            var form = $("#requestStockMovement");

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
                        Swal.fire({
                            title: "Done!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#1abc9c",
                        });
                        singleId.val("0");
                        $(form).trigger("reset");
                        $("#movement-stock-modal").modal("hide");
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
