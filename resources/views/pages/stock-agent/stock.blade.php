<div class="row">
    <div class="col-12">
        <div class="row mb-2">


        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">

                            <div class="col-sm-12">
                                <div class="text-sm-end">
                                    <button data-url="{{ route('dm-prcat') }}" type="button"
                                        class="btn btn-danger mb-2 me-1 delete-all">
                                        <i class="mdi mdi-trash-can-outline"></i></button>
                                    <a href="javascript:void(0);" class="btn btn-primary mb-2"><i
                                            class="mdi mdi-printer me-1"></i> Print</a>
                                    <a href="javascript:void(0);" class="btn btn-success mb-2"><i
                                            class="mdi mdi-database-export me-1"></i> Export</a>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered dt-responsive nowrap w-100" id="products-dt"
                                data-api-url="{{ route('show-stock-agent',['agent'=> Auth::user()->id]) }}">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20px;">
                                            <div class="form-check font-16 mb-0">
                                                <input id="checkAllRows" class="form-check-input" type="checkbox"
                                                    id="customerlist">
                                                <label class="form-check-label" for="customerlist">&nbsp;</label>
                                            </div>
                                        </th>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th style="width: 75px;">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
        </div>

        <!-- end row -->
        <script>
         
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });
                currentDt = $("#products-dt").DataTable({
                    autoWidth: false,
                    order: [0, "ASC"],
                    processing: true,
                    serverSide: true,
                    searchDelay: 1000,
                    paging: true,
                    ajax: {
                        url: $("#products-dt").attr("data-api-url"),
                    },
                    iDisplayLength: "10",
                    columns: [{
                            data: "checkbox",
                            name: "checkbox",
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: "product",
                            name: "product",
                            className: "text-900 sort pe-1 align-middle white-space-wrap",
                        },

                        {
                            data: "price",
                            name: "price",
                            className: "text-900 sort pe-1 align-middle white-space-nowrap",
                        },
                        {
                            data: "quantity",
                            name: "quantity",
                            className: "text-900 sort pe-1 align-middle white-space-nowrap",
                        },

                        {
                            data: "action",
                            name: "action",
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    lengthMenu: [10, 25, 50, 100],
                });


                $(document).on('click', '.delete-btn', function(e) {
                    e.preventDefault();
                    let url = $(this).attr('data-url');
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#1abc9c",
                        cancelButtonColor: "#f1556c",
                        confirmButtonText: "Yes, delete it!",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                method: 'delete',
                                success: function(response) {
                                    if (response.status == true) {
                                        Swal.fire({
                                            title: "Deleted!",
                                            text: response.message,
                                            icon: "success",
                                            confirmButtonColor: "#1abc9c",
                                        });
                                        currentDt.ajax.reload();
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Error",
                                            text: response.message,
                                            confirmButtonColor: "#3bafda",
                                        });
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {

                                    if (jqXHR.status === 403) {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: "Acces Denied!",
                                            confirmButtonColor: "#3bafda",
                                            footer: '<strong>Error code :</strong> 403',
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: "An error occured",
                                            confirmButtonColor: "#3bafda",
                                        });
                                    }
                                }
                            });
                        }
                    });

                });

                // Select Multiple Records
                $(document).on('click', '#checkAllRows', function() {
                    $('.check-row').prop('checked', $(this).prop('checked'));
                });

                // Delete Multiple records
                $(document).on('click', '.delete-all', function() {
                    var checkedCount = $('.check-row:checked').length;
                    var url = $(this).attr('data-url');

                    if (checkedCount < 1) {
                        Swal.fire(
                            "Ooops...",
                            "You must select at least 2 records",
                            "warning"
                        );
                    } else {
                        var all_id = [];
                        $('input:checkbox[name="single-row"]:checked').each(function() {
                            all_id.push($(this).val());
                        });
                        Swal.fire({
                            title: "Are you sure?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: !0,
                            confirmButtonColor: "#1abc9c",
                            cancelButtonColor: "#f1556c",
                            confirmButtonText: "Yes, delete them!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: url,
                                    method: 'delete',
                                    data: {
                                        all_id: all_id,
                                    },
                                    success: function(response) {
                                        if (response.status == true) {
                                            Swal.fire({
                                                title: "Deleted!",
                                                text: response.message,
                                                icon: "success",
                                                confirmButtonColor: "#1abc9c",
                                            });
                                            currentDt.ajax.reload();
                                        } else if (response.status == false) {
                                            Swal.fire({
                                                icon: "error",
                                                title: "Oops...",
                                                text: response.message,
                                                confirmButtonColor: "#3bafda",
                                            });
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {

                                        if (jqXHR.status === 403) {
                                            Swal.fire({
                                                icon: "error",
                                                title: "Oops...",
                                                text: "Acces Denied!",
                                                confirmButtonColor: "#3bafda",
                                                footer: '<strong>Error code :</strong> 403',
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: "error",
                                                title: "Oops...",
                                                text: "An error occured",
                                                confirmButtonColor: "#3bafda",
                                            });
                                        }
                                    }
                                });
                            }
                        });
                    }
                });

            });
            $(document).on('click', '.btn-select', function(e) {
                e.preventDefault();
                let productId = $(this).data('id');
                console.log("ID envoyé au serveur :", productId);
                let btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: "{{ route('cart-add-sale') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: productId,
                        quantity: 1
                    },
                    success: function(response) {
                        // 1. On affiche un message de succès (Toastr ou Alert)
                        alert(response.message);

                        // 2. On appelle notre fonction magique pour rafraîchir l'affichage
                        reloadCart();

                        // 3. Réactiver le bouton
                        btn.prop('disabled', false);
                    },
                    error: function(xhr) {
                        // Si le stock disponible est à 0, le controller renverra une erreur 422
                        alert(xhr.responseJSON.message);
                        btn.prop('disabled', false);
                    }
                })
            })

        </script>
    </div>
</div>
