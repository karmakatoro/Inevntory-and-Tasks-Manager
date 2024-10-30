@extends('layouts.base')

@section('title', 'Products Stock - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Products Stock</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Products Stock</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills navtab-bg" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#stock" data-bs-toggle="tab" aria-expanded="false" class="nav-link ms-0"
                            aria-selected="false" role="tab">
                            <i class="mdi mdi-cart-minus me-1"></i>Availiable Products
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#stock-history" data-bs-toggle="tab" aria-expanded="true" class="nav-link active"
                            aria-selected="true" role="tab" tabindex="-1">
                            <i class="mdi mdi-history me-1"></i>History
                        </a>
                    </li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane " id="stock" role="tabpanel">

                        @include('pages.product-stock.stock')
                    </div>


                    <div class="tab-pane active show" id="stock-history" role="tabpanel">
                        @include('pages.product-stock.stock-history')
                    </div>


                </div> <!-- end tab-content -->
            </div>
        </div> <!-- end card-->

    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });
            currentDt = $("#stock-history-dt").DataTable({
                autoWidth: false,
                order: [0, "ASC"],
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                paging: true,
                ajax: {
                    url: $("#stock-history-dt").attr("data-api-url"),
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
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
                    },
                    {
                        data: "operation",
                        name: "operation",
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
                    },
                    {
                        data: "date",
                        name: "date",
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
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
                        data: "author",
                        name: "author",
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
                    },
                    {
                        data: "status",
                        name: "status",
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

            $(document).on('click', '.edit-btn', function(e) {
                e.preventDefault();
                $("#requestStockMovement")[0].reset();
                let url = $(this).attr('data-url');
                $.ajax({
                    url: url,
                    method: 'get',
                    success: function(response) {
                        if (response.status == true) {
                            $("#movementId").val(respose.date.id);
                            $("#movement").val(response.data.name);
                            $("#product").val(response.data.email);
                            $("#phone").val(response.data.phone);
                            $("#gender").val(response.data.gender);
                            $("#type").val(response.data.type);
                            $("#accred").val(response.data.accred);
                            $("#statusUser").val(response.data.status);
                            $("#errorsDiv").css("display", "none");
                            $("#movement-stock-modal").modal('show');
                        } else {
                            Swal.fire("Erreur", response.message, 'warning');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                let id = $(this).attr('data-id');
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
                        "Vous devez selectionner au minimum 2 enregistrements",
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
    </script>
@endsection
