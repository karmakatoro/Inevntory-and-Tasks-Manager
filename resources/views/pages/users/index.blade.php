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
                                <button type="button" class="btn btn-danger mb-2 me-1">
                                    <i class="mdi mdi-trash-can-outline"></i></button>
                                <a href="javascript:void(0);" class="btn btn-primary mb-2"><i
                                        class="mdi mdi-printer me-1"></i> Print</a>
                                <a href="javascript:void(0);" class="btn btn-success mb-2"><i
                                        class="mdi mdi-database-export me-1"></i> Export</a>
                            </div>
                        </div><!-- end col-->
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered dt-responsive nowrap w-100" id="products-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist">
                                            <label class="form-check-label" for="customerlist">&nbsp;</label>
                                        </div>
                                    </th>
                                    <th>Names</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Level</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th style="width: 75px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist01">
                                            <label class="form-check-label" for="customerlist01">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-4.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);" class="text-dark">John
                                                        Delaney</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2048</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>078 5054 8877</td>
                                    <td>
                                        $8,562.25
                                    </td>
                                    <td>
                                        41
                                    </td>
                                    <td>
                                        02 Apr, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist02">
                                            <label class="form-check-label" for="customerlist02">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-5.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);" class="text-dark">George
                                                        Beasley</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2047</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>215-302-3376</td>
                                    <td>
                                        $4,254.65
                                    </td>
                                    <td>
                                        23
                                    </td>
                                    <td>
                                        04 Mar, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist03">
                                            <label class="form-check-label" for="customerlist03">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="avatar-sm me-3">
                                                <div
                                                    class="avatar-title rounded-circle bg-soft-primary fw-medium text-primary">
                                                    M
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Mary
                                                        Gonzalez</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2046</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>050 414 8778</td>
                                    <td>
                                        $874.82
                                    </td>
                                    <td>
                                        34
                                    </td>
                                    <td>
                                        11 Jul, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-danger">Blocked</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist04">
                                            <label class="form-check-label" for="customerlist04">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-6.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Roberto Norton</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2045</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>(216) 75 612 706</td>
                                    <td>
                                        $5,562.45
                                    </td>
                                    <td>
                                        20
                                    </td>
                                    <td>
                                        15 Nov, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-danger">Blocked</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist05">
                                            <label class="form-check-label" for="customerlist05">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-7.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Jose Payson</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2044</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>078 6013 3854</td>
                                    <td>
                                        $1,625.84
                                    </td>
                                    <td>
                                        22
                                    </td>
                                    <td>
                                        21 Feb, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-danger">Blocked</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist06">
                                            <label class="form-check-label" for="customerlist06">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-8.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Frank King</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2043</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>050 414 8778</td>
                                    <td>
                                        $3,568.54
                                    </td>
                                    <td>
                                        28
                                    </td>
                                    <td>
                                        12 Jun, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist07">
                                            <label class="form-check-label" for="customerlist07">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="avatar-sm me-3">
                                                <div
                                                    class="avatar-title rounded-circle bg-soft-primary fw-medium text-primary">
                                                    A
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Anne Martinez</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2042</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>078 5054 8877</td>
                                    <td>
                                        $6,458.21
                                    </td>
                                    <td>
                                        33
                                    </td>
                                    <td>
                                        14 Jul, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist08">
                                            <label class="form-check-label" for="customerlist08">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-9.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Olive Hedin</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2041</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>215-302-3376</td>
                                    <td>
                                        $3,524.27
                                    </td>
                                    <td>
                                        27
                                    </td>
                                    <td>
                                        16 Aug, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist09">
                                            <label class="form-check-label" for="customerlist09">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div class="avatar-sm me-3">
                                                <div
                                                    class="avatar-title rounded-circle bg-soft-primary fw-medium text-primary">
                                                    C
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Clara Ramsey</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2040</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>(216) 75 612 706</td>
                                    <td>
                                        $724.12
                                    </td>
                                    <td>
                                        16
                                    </td>
                                    <td>
                                        13 Sep, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist10">
                                            <label class="form-check-label" for="customerlist10">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-10.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Jeff Luck</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2039</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>(02) 75 150 655</td>
                                    <td>
                                        $1,825.14
                                    </td>
                                    <td>
                                        18
                                    </td>
                                    <td>
                                        18 Nov, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist11">
                                            <label class="form-check-label" for="customerlist11">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-4.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Bruce Williams</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2038</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>215-302-3376</td>
                                    <td>
                                        $2,214.61
                                    </td>
                                    <td>
                                        27
                                    </td>
                                    <td>
                                        14 Apr, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-danger">Blocked</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check font-16 mb-0">
                                            <input class="form-check-input" type="checkbox" id="customerlist12">
                                            <label class="form-check-label" for="customerlist12">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img src="assets/images/users/avatar-5.jpg" alt="table-user"
                                                class="me-3 rounded-circle avatar-sm">
                                            <div class="flex-1">
                                                <h5 class="mt-0 mb-1"><a href="javascript:void(0);"
                                                        class="text-dark">Tyler Davis</a></h5>
                                                <p class="mb-0 font-13">ID : #MN2037</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>050 414 8778</td>
                                    <td>
                                        $2,354.45
                                    </td>
                                    <td>
                                        21
                                    </td>
                                    <td>
                                        21 Dec, 2020
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">Active</span>
                                    </td>

                                    <td>
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-square-edit-outline"></i></a>
                                            </li>
                                            <li class="list-inline-item">
                                                <a href="javascript:void(0);" class="action-icon"> <i
                                                        class="mdi mdi-delete"></i></a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
    </div>
    <!-- end row -->
    @include('pages.users.modal-users')

@endsection
