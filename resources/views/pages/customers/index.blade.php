@extends('layouts.base')

@section('title', 'Customers - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Utilisateurs</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Tableau de Bord</a>
                        </li>
                        <li class="breadcrumb-item active">Clients</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
   
<div class="row" id="stats-container">
    <div class="col-md-6 col-xl-3">
        <div class="card widget-flat custom-card-rounded border-primary-sep shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <div class="avatar-sm bg-soft-primary rounded-circle">
                        <i class="fe-tag avatar-title font-22 text-primary"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase mt-0">Total Produits</h6>
                <h3 class="my-2">25,563</h3>
                <p class="mb-0 text-muted"><span class="small fw-medium">Catalogue complet</span></p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card widget-flat custom-card-rounded border-warning-sep shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <div class="avatar-sm bg-soft-warning rounded-circle">
                        <i class="fe-archive avatar-title font-22 text-warning"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase mt-0">Disponibles</h6>
                <h3 class="my-2 text-warning">6,952</h3>
                <p class="mb-0 text-muted"><span class="small fw-medium">Prêt à la vente</span></p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card widget-flat custom-card-rounded border-success-sep shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <div class="avatar-sm bg-soft-success rounded-circle">
                        <i class="fe-shield avatar-title font-22 text-success"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase mt-0">Chiffre d'Affaires</h6>
                <h3 class="my-2 text-success">18,361 $</h3>
                <p class="mb-0 text-muted"><span class="text-success small"><i class="mdi mdi-trending-up"></i> +12.5%</span></p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card widget-flat custom-card-rounded border-danger-sep shadow-sm">
            <div class="card-body">
                <div class="float-end">
                    <div class="avatar-sm bg-soft-danger rounded-circle">
                        <i class="fe-users avatar-title font-22 text-danger"></i>
                    </div>
                </div>
                <h6 class="text-muted text-uppercase mt-0">Réseau Agents</h6>
                <h3 class="my-2 text-danger">250</h3>
                <p class="mb-0 text-muted"><span class="small fw-medium">Vendeurs actifs</span></p>
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
                                class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle me-1"></i>Nouveau Client</a>

                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <button data-url="{{ route('dm-cs') }}" type="button"
                                    class="btn btn-danger mb-2 me-1 delete-all">
                                    <i class="mdi mdi-trash-can-outline"></i></button>
                                <a href="javascript:void(0);" class="btn btn-primary mb-2"><i
                                        class="mdi mdi-printer me-1"></i>Imprimer</a>
                                <a href="javascript:void(0);" class="btn btn-success mb-2"><i
                                        class="mdi mdi-database-export me-1"></i>Exporter</a>
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
                                    <th>Noms</th>
                                    <th>Contacts</th>
                                    <th>Addresse</th>
                                    <th>Date de création</th>
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
                 language: {
               "sProcessing": "Traitement en cours...",
                    "sSearch": "Rechercher un client :",
                    "sLengthMenu": "Afficher _MENU_ clients",
                    "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ clients",
                    "sEmptyTable": "Aucune dette en cours.",
                    "oPaginate": {
                        "sNext": "Suivant",
                        "sPrevious": "Précédent"

            }},
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
