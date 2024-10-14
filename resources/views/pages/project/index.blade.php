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
                            <button type="button" class="btn btn-success waves-effect waves-light"><i
                                    class="mdi mdi-printer me-1"></i> Print</button>
                            <a href="{{ route('projects.create') }}" class="btn btn-primary waves-effect waves-light"><i
                                    class="mdi mdi-plus-circle me-1"></i>
                                Add New</a>
                        </div>
                    </div><!-- end col-->
                </div> <!-- end row -->
            </div>
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row -->
@if (session()->has('success'))
<div class="alert alert-success solid alert-dismissible fade show mt-3">
    <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor" stroke-width="2" fill="none"
        stroke-linecap="round" stroke-linejoin="round" class="me-2">
        <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2">
        </polygon>
        <line x1="15" y1="9" x2="9" y2="15"></line>
        <line x1="9" y1="9" x2="15" y2="15"></line>
    </svg>
    <strong>Success!</strong> {{ session()->get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
    </button>
</div>
@endif

<div class="row">
    @foreach ($projects as $value)
    @php
    $pr_id = $value->id;
    $project_logo = asset($value->logo);
    $project_show_url = route('projects.show', ['project' => $pr_id]);
    $project_edit_url = route('projects.edit', ['project' => $pr_id]);
    $project_delete_url = route('projects.destroy', ['project' => $pr_id]);
    $start = \Carbon\Carbon::parse($value->start)->locale('en_EN')->isoFormat('DD
    MMMM YYYY');
    @endphp

    <div class="col-xl-4 col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar-md me-3">
                        <div class="avatar-title bg-light rounded-circle">
                            <img src="{{ $project_logo }}" alt="logo" class="avatar-sm rounded-circle">
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="my-1"><a href="{{ $project_show_url }}" class="text-dark">{{ $value->title }}</a>
                        </h5>
                        <p class="text-muted text-truncate mb-0">
                            <i class="ri-folder-user-line align-bottom me-1"></i> {{ auth()->user()->name }}
                        </p>
                    </div>
                    <div class="dropdown">
                        <a class="text-body dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="mdi mdi-dots-vertical font-20"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ $project_show_url }}">
                                <i class="mdi mdi-eye"></i> View details</a>
                            <a class="dropdown-item" href="{{ $project_edit_url }}">
                                <i class="mdi mdi-briefcase-edit-outline"></i> Edit
                                project</a>
                            <a class="dropdown-item delete-btn" data-url="{{ $project_delete_url }}" href="#">
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
                                <h5 class="mb-sm-0">{{$start}}</h5>
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
    @endforeach
</div>
<!-- end row -->
<script>
    $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
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
                                    window.location.reload();
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