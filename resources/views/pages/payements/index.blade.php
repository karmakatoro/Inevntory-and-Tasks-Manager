@extends('layouts.base')

@section('title', 'Paiements - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Paiements</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Tableau de bord</a>
                        </li>
                        <li class="breadcrumb-item active">Paiements</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Rapides -->
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
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Total Produits</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-archive font-24"></i>
                                    <h3 class="text-warning">6952</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Produits Disponibles</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-shield font-24"></i>
                                    <h3 class="text-success">18361</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Total Ventes</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xl-3">
                                <div class="py-1">
                                    <i class="fe-users font-24"></i>
                                    <h3 class="text-danger">250</h3>
                                    <p class="text-uppercase mb-1 font-13 fw-medium">Total Vendeurs</p>
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
                        <div class="col-sm-12">
                            <div class="text-sm-end">
                                <button type="button" class="btn btn-danger mb-2 me-1 delete-all">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                                <a href="javascript:void(0);" class="btn btn-primary mb-2">
                                    <i class="mdi mdi-printer me-1"></i> Imprimer
                                </a>
                                <a href="javascript:void(0);" class="btn btn-success mb-2">
                                    <i class="mdi mdi-database-export me-1"></i> Exporter
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3 mb-2">
                            <strong>Succès!</strong> {{ session()->get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-centered dt-responsive nowrap w-100" id="payment-dt"
                            data-api-url="{{ route('payement.show', ['agent' => Auth::user()->id]) }}">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Date</th>
                                    <th>Référence Vente</th>
                                    <th>Client</th>
                                    @if(Auth::user()->type === 'admin')
                                        <th>Encaissé par</th>
                                    @endif
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th style="width: 75px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.payements.create')

   <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        var apiUrl = $("#payment-dt").attr("data-api-url");
        
        // Correction ici : On vérifie proprement la valeur booléenne
        var isAdmin = {{ Auth::user()->type === 'admin' ? 'true' : 'false' }};

        var tableColumns = [
            { data: "checkbox", name: "checkbox", orderable: false, searchable: false },
            { 
                data: "created_at", 
                name: "created_at",
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY HH:mm');
                }
            },
            { data: "ventes", name: "ventes" },
            { data: "customer", name: "customer" }
        ];

        if (isAdmin) {
            tableColumns.push({ data: "recorded_by", name: "recorded_by" });
        }

        tableColumns.push(
            { data: "amount", name: "amount", className: "text-end" },
            { data: "status", name: "status", className: "text-center" },
            { data: "action", name: "action", orderable: false, searchable: false }
        );

        var currentDt = $("#payment-dt").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: apiUrl,
                error: function (xhr, error, thrown) {
                    console.log("Erreur Datatables:", xhr.responseText); // Pour voir l'erreur réelle dans la console
                }
            },
            columns: tableColumns,
            order: [[1, 'desc']], 
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
            },
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $("#checkAll").on("click", function() {
            $(".row-checkbox").prop("checked", $(this).prop("checked"));
        });
    });
</script>
@endsection