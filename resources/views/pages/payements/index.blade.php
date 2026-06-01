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
    @include('pages.payements.details-payment')

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
            { data: "customer", name: "customer",}
        ];

        if (isAdmin) {
            tableColumns.push({ data: "recorded_by", name: "recorded_by" });
        }

        tableColumns.push(
            { data: "amount", name: "amount", className: "text-center" },
           { 
            data: "status", 
            className: "text-center",
            render: function(data) {
                let color = (data === 'Validé' || data === 'Confirmé') ? 'success' : 'warning';
                return `<span class="badge bg-soft-${color} text-${color} border border-${color}-subtle rounded-pill px-2">
                            <i class="mdi mdi-circle font-10 me-1"></i>${data}
                        </span>`;
            }
        },
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
               "sProcessing": "Traitement en cours...",
                    "sSearch": "Rechercher un client :",
                    "sLengthMenu": "Afficher _MENU_ clients",
                    "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ clients",
                    "sEmptyTable": "Aucune dette en cours.",
                    "oPaginate": {
                        "sNext": "Suivant",
                        "sPrevious": "Précédent"

            }},
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $("#checkAll").on("click", function() {
            $(".row-checkbox").prop("checked", $(this).prop("checked"));
        });
    });
$(document).on('click', '.dropdown-item[data-id]', function(e) {
    e.preventDefault();
    
    // CORRECTION : $(this) ou $(e.currentTarget) ciblent TOUJOURS l'élément portant l'écouteur (.dropdown-item)
    // même si on a cliqué sur l'icône à l'intérieur.
    let rawId = $(this).attr('data-id') || $(this).data('id');

    if (!rawId) {
        console.error("Échec de récupération de l'ID sur l'élément cliqué.");
        return;
    }

    // Nettoyage de l'ID
    let paymentId = String(rawId).replace(/[/\\%22"']/g, "");

    // Si après nettoyage c'est vide, on bloque avant d'appeler l'AJAX
    if (!paymentId || paymentId === "undefined") {
        alert("Impossible de charger les détails : L'ID de ce paiement est invalide ou corrompu.");
        return;
    }

    let modal = $('#details-payment-modal');
    let container = $('#details-content');

    container.html('<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Chargement des données...</p></div>');
    modal.modal('show');

    $.ajax({
        url: `/payement/${paymentId}/details`,
        method: 'GET',
        success: function(response) {
            container.html(response);
        },
        error: function(xhr) {
            container.html('<div class="alert alert-danger">Erreur de chargement (Code: ' + xhr.status + ').</div>');
            console.error("Détails:", xhr.responseText);
        }
    });
});
</script>
@endsection