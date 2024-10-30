@extends('layouts.base')

@section('title', 'Customers - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Users</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-tag font-24"></i>
                                    <h3>25563</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Total Customers</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-archive font-24"></i>
                                    <h3 class="text-warning">6952</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Active Users</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-shield font-24"></i>
                                    <h3 class="text-success">18361</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Male Customers</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-delete font-24"></i>
                                    <h3 class="text-danger">250</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Female Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#customers-modal"
                                class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle me-1"></i> Add
                                Customer</a>

                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button data-url="{{ route('dm-cs') }}" type="button"
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
                        <table class="table table-centered dt-responsive nowrap w-100" id="customers-dt"
                            data-api-url="{{ route('customers.index') }}">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check font-16 mb-0">
                                            <input id="checkAllRows" class="form-check-input" type="checkbox"
                                                id="customerlist">
                                            <label class="form-check-label" for="customerlist">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th>Names</th>
                                    <th>Contacts</th>
                                    <th>Address</th>
                                    <th>Join</th>
                                    <th>Status</th>
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
            currentDt = $("#customers-dt").DataTable({
                autoWidth: false,
                order: [0, "ASC"],
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                paging: true,
                ajax: {
                    url: $("#customers-dt").attr("data-api-url"),
                },
                iDisplayLength: "10",
                columns: [{
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
                        data: "contacts",
                        name: "contacts",
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
                    },
                    {
                        data: "address",
                        name: "address",
                        className: "text-900 sort pe-1 align-middle white-space-wrap",
                    },
                    {
                        data: "join",
                        name: "join",
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
                $("#requestCustomers")[0].reset();
                let url = $(this).attr('data-url');
                $.ajax({
                    url: url,
                    method: 'get',
                    success: function(response) {
                        if (response.status == true) {
                            $("#customerId").val(response.data.id);
                            $("#name").val(response.data.name);
                            $("#email").val(response.data.email);
                            $("#phone").val(response.data.phone);
                            $("#gender").val(response.data.gender);
                            $("#address").val(response.data.address);
                            $("#statusUser").val(response.data.status);
                            $("#errorsDiv").css("display", "none");
                            $("#customers-modal").modal('show');
                        } else {
                            Swal.fire("Erreur", response.message, 'warning');
                        }
                    }
                });
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
    </script>
    @include('pages.customers.customers-modal')

@endsection
