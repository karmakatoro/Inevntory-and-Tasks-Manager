@extends('layouts.base')

@section('title', 'Users - ' . env('APP_NAME'))

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
                        <li class="breadcrumb-item active">Users</li>
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
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#users-modal"
                                class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle me-1"></i> Add
                                Users</a>

                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button data-url="{{ route('dm-users') }}" type="button"
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
                        <table class="table table-centered dt-responsive nowrap w-100" id="users-dt"
                            data-api-url="{{ route('users.index') }}">
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
                                    <th>Phone</th>
                                    <th>Email</th>
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
            currentDt = $("#users-dt").DataTable({
                autoWidth: false,
                order: [0, "ASC"],
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                paging: true,
                ajax: {
                    url: $("#users-dt").attr("data-api-url"),
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
                        data: "phone",
                        name: "phone",
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
                    },
                    {
                        data: "email",
                        name: "email",
                        className: "text-900 sort pe-1 align-middle white-space-nowrap",
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
                $("#requestUsers")[0].reset();
                let id = $(this).attr('data-id');
                let url = $(this).attr('data-url');
                $.ajax({
                    url: url,
                    method: 'get',
                    success: function(response) {
                        if (response.status == true) {
                            $("#userId").val(id);
                            $("#name").val(response.data.name);
                            $("#email").val(response.data.email);
                            $("#phone").val(response.data.phone);
                            $("#gender").val(response.data.gender);
                            $("#type").val(response.data.type);
                            $("#accred").val(response.data.accred);
                            $("#statusUser").val(response.data.status);
                            $("#errorsDiv").css("display", "none");
                            $("#users-modal").modal('show');
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
    @include('pages.users.modal-users')


@endsection
