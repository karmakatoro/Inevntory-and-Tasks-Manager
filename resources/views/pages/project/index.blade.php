@extends('layouts.base')

@section('title', 'Projects - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Projects</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Projects</li>
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
                    <div class="row">
                        <div class="col-lg-8">
                            <form class="d-flex flex-wrap align-items-center">
                                <div class="d-flex flex-wrap align-items-center">
                                    <label for="inputPassword2" class="visually-hidden">Search</label>
                                    <input type="search" class="form-control" id="inputPassword2" placeholder="Search...">
                                </div>
                                <div class="d-flex flex-wrap align-items-center mx-sm-3">
                                    <label for="status-select" class="me-2">Sort By</label>
                                    <div>
                                        <select class="form-select" id="status-select">
                                            <option selected>Name</option>
                                            <option>Latest</option>
                                            <option>Older</option>
                                            <option value="unlaunched">Unlaunched</option>
                                            <option value="pending">Pending</option>
                                            <option value="finished">Finished</option>
                                            <option value="canceled">Canceled</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex gap-2 justify-content-lg-end mt-3 mt-lg-0">
                                <button type="button" class="btn btn-primary waves-effect waves-light"><i
                                        class="mdi mdi-printer me-1"></i> Print</button>
                                <button type="button" class="btn btn-danger waves-effect waves-light"><i
                                        class="mdi mdi-plus-circle me-1"></i> Add New</button>
                            </div>
                        </div><!-- end col-->
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="avatar-md me-3">
                            <div class="avatar-title bg-light rounded-circle">
                                <img src="assets/images/companies/google.png" alt="logo"
                                    class="avatar-sm rounded-circle">
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="my-1"><a href="javascript:void(0);" class="text-dark">Google LLC</a></h4>
                            <p class="text-muted text-truncate mb-0">
                                <i class="ri-map-pin-line align-bottom me-1"></i> Menlo Park, California
                            </p>
                        </div>
                        <div class="dropdown">
                            <a class="text-body dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical font-20"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">
                                    <i class="mdi mdi-eye"></i> View details</a>
                                <a class="dropdown-item" href="#">
                                    <i class="mdi mdi-briefcase-edit-outline"></i> Edit
                                    project</a>
                                <a class="dropdown-item" href="#">
                                    <i class="mdi mdi-trash-can-outline"></i> Delete
                                    project</a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-muted">
                        <div class="row">
                            <div class="col-6">
                                <div>
                                    <p class="text-truncate mb-0">Started</p>
                                    <h5 class="mb-sm-0">110 bn</h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div>
                                    <p class="text-truncate mb-0">Involved Users</p>
                                    <h5 class="mb-sm-0">72k</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        });
    </script>
@endsection
