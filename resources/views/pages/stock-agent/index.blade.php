@extends('layouts.base')

@section('title', 'Products Stock - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
 <div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">

            <div class="page-title-main">
                <h4 class="page-title mb-0">
                    Products Stock
                    <span class="text-muted fw-light mx-2">|</span>
                    <span class="text-primary">{{ $agent->name }}</span>
                </h4>
            </div>

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
                        <a href="#assign" data-bs-toggle="tab" aria-expanded="false" class="nav-link ms-0 active"
                            aria-selected="true" role="tab">
                            <i class="mdi mdi-cart-minus me-1"></i> Mes assignations
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#stock" data-bs-toggle="tab" aria-expanded="false" class="nav-link ms-0 "
                            aria-selected="true" role="tab">
                            <i class="mdi mdi-cart-minus me-1"></i> Stock Central
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#cart-content" data-bs-toggle="tab" aria-expanded="false" class="nav-link ms-0 "
                            aria-selected="true" role="tab">
                            <i class="mdi mdi-cart-minus me-1"></i> Panier de vente
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#stock-history" data-bs-toggle="tab" aria-expanded="false" class="nav-link ms-0 "
                            aria-selected="true" role="tab">
                            <i class="mdi mdi-cart-minus me-1"></i> Historique de mouvement de Stock Central
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active show" id="assign" role="tabpanel">
                        @include('pages.stock-agent.assign')
                    </div>
                    <div class="tab-pane" id="stock" role="tabpanel">
                        @include('pages.stock-agent.stock')
                    </div>

                    <div class="tab-pane" id="cart-content" role="tabpanel">
                        <div id="cart-ajax-container">
                            <div class="text-center p-5 text-muted">
                                <div class="spinner-border text-primary m-2" role="status"></div>
                                <p>Chargement du panier...</p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="stock-history" role="tabpanel">
                        @include('pages.stock-agent.stock-history')
                    </div>
                </div> <!-- end tab-content -->
            </div>
        </div> <!-- end card-->

    </div>

 <script>
        $(document).ready(function() {
            // --- 1. CONFIGURATION GLOBALE ---
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            // --- 2. FONCTION RELOAD CART ---
            function reloadCart(showLoader = false) {
                let container = $('#cart-ajax-container');
                let url = "{{ route('cart-index') }}";

                if (showLoader) {
                    container.html(
                        '<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-2">Chargement du panier...</p></div>'
                    );
                } else {
                    container.css('opacity', 0.6);
                }

                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === true) {
                            container.html(response.html);
                            if ($('.select2').length) {
                                $('.select2').select2({
                                    dropdownParent: $('#cart-content')
                                });
                            }
                        } else {
                            container.html('<div class="alert alert-warning">Erreur : ' + (response
                                .message || 'Données invalides') + '</div>');
                        }
                    },
                    error: function(xhr) {
                        container.html(
                            '<div class="alert alert-danger">Erreur : Impossible de charger le panier.</div>'
                            );
                    },
                    complete: function() {
                        container.css('opacity', '1');
                    }
                });
            }

            // Rendre accessible pour les autres scripts
            window.reloadCart = reloadCart;
            // Alias pour éviter les erreurs "undefined"

            // --- 3. INITIALISATION DATATABLE ---
            var currentDt = $("#stock-history-dt").DataTable({
                autoWidth: false,
                order: [0, "ASC"],
                processing: true,
                serverSide: true,
                ajax: {
                    url: $("#stock-history-dt").attr("data-api-url")
                },
                columns: [{
                        data: "checkbox",
                        name: "checkbox",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "product",
                        name: "product"
                    },
                    {
                        data: "operation",
                        name: "operation"
                    },
                    {
                        data: "date",
                        name: "date"
                    },
                    {
                        data: "price",
                        name: "price"
                    },
                    {
                        data: "quantity",
                        name: "quantity"
                    },
                    {
                        data: "author",
                        name: "author"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            // --- 4. GESTION DES ONGLETS ---
            $('a[href="#cart-content"]').on('shown.bs.tab', function(e) {
                reloadCart(true);
            });

            // --- 5. ACTIONS DU PANIER (Update Qty) ---
            $(document).on('click', '.btn-update-qty', function(e) {
                e.preventDefault();
                let testAgentId = $('meta[name="current-agent-id"]').attr('content');
                console.log("Test - Agent ID récupéré :", testAgentId);
                let productId = $(this).data('id');
                let action = $(this).data('action'); // 'plus' ou 'minus'
                let btn = $(this);

                // Désactiver le bouton temporairement pour éviter les doubles clics
                btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('cart-update') }}", // Assure-toi que c'est le bon nom de route
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: productId,
                        action: action,

                    },
                    success: function(response) {
                        if (response.status) {
                            // Si tout est ok, on rafraîchit l'affichage du panier
                            reloadCart();
                        } else {
                            // Si le stock est dépassé (message 'Stock maximum atteint')
                            alert(response.message);
                        }
                        btn.prop('disabled', false);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        btn.prop('disabled', false);
                    }
                });
            }); // <-- Fermeture correcte ici

            // --- 6. ACTIONS DU PANIER (Remove Item) ---
            $(document).on('click', '.btn-remove-item', function(e) {
                e.preventDefault();
                let ProductId = $(this).data('id');
                let url = "{{ route('cart-delete') }}";

                Swal.fire({
                    title: "Êtes-vous sûr ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Oui, supprimer !",
                    confirmButtonColor: "#1abc9c",
                    cancelButtonColor: "#f1556c",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            data: {
                                product_id: ProductId
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire("Supprimé !", response.message,
                                    "success");
                                    reloadCart();
                                }
                            }
                        });
                    }
                });
            });

            // --- 7. ACTIONS HISTORIQUE (Edit/Delete) ---
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                let url = $(this).attr('data-url');
                Swal.fire({
                    title: "Êtes-vous sûr ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Oui, supprimer !",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire("Supprimé !", response.message,
                                    "success");
                                    currentDt.ajax.reload();
                                }
                            }
                        });
                    }
                });
            });

        });
 $(document).ready(function() {
    // 1. Configuration et Sécurisation des variables de base
    const $table = $('#assign-dt');
    if ($table.length === 0) return; // Sécurité si la table n'est pas sur la page

    const apiUrl = $table.data('api-url');
    let selectedStatus = 'all'; // Statut de filtre par défaut

    // 2. Initialisation de la DataTable (Server-Side)
    const tableInstance = $table.DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: apiUrl,
            type: 'GET',
            data: function (d) {
                // Injection dynamique du statut choisi dans la requête Laravel
                d.status = selectedStatus;
            },
            error: function (xhr) {
                console.error("Erreur DataTables : ", xhr.responseText);
            }
        },
        columns: [
            { 
                data: 'checkbox', 
                orderable: false, 
                searchable: false,
                width: '20px'
            },
            { data: 'reference_bon' },
            { data: 'sender' },
            { data: 'receiver' },
            { data: 'created_at' },
            { data: 'total_distinct_products', className: 'text-center' },
            { data: 'status', orderable: true },
            { 
                data: 'action', 
                orderable: false, 
                searchable: false, 
                className: 'text-center',
                width: '100px'
            }
        ],
        order: [[4, 'desc']], // Tri par défaut sur la colonne 'Date d'Opération'
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json', // Interface en français
            
    "sEmptyTable":     "Aucune donnée disponible dans le tableau",
    "sInfo":           "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
    "sInfoEmpty":      "Affichage de l'élément 0 à 0 sur 0 élément",
    "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
    "sLengthMenu":     "Afficher _MENU_ éléments",
    "sLoadingRecords": "Chargement...",
    "sProcessing":     "Traitement...",
    "sSearch":         "Rechercher :",
    "sZeroRecords":    "Aucun élément correspondant trouvé",
    "oPaginate": {
        "sFirst":    "Premier",
        "sLast":     "Dernier",
        "sNext":     "Suivant",
        "sPrevious": "Précédent"
    }

        }
    });

    // 3. UI/UX : Gestion du menu déroulant (Dropdown Filter)
    $('.btn-status-filter').on('click', function(e) {
        e.preventDefault();

        // Gestion active visuelle dans le menu
        $('.btn-status-filter').removeClass('active');
        $(this).addClass('active');

        // Récupération de la nouvelle valeur de filtre
        selectedStatus = $(this).data('status');

        // Mise à jour de l'intitulé du bouton principal (sans les icônes/badges)
        const textLabel = $(this).text().trim().replace('●', '');
        $('#active-filter-label').text(textLabel);

        // Changement de style cosmétique si un filtre est actif
        if (selectedStatus !== 'all') {
            $('#dropdownFilterStatus').removeClass('btn-light').addClass('btn-soft-primary');
        } else {
            $('#dropdownFilterStatus').removeClass('btn-soft-primary').addClass('btn-light');
        }

        // Rechargement instantané de la DataTable avec le nouveau filtre
        tableInstance.ajax.reload();
    });

    // 4. UI/UX : Interception du clic pour charger les détails du Bon de Sortie
    $(document).on('click', '.btn-view-details', function(e) {
        e.preventDefault();

        const referenceBon = $(this).data('bon');
        const auteur = $(this).data('sender');
        const agent = $(this).data('receiver');

        // Préparation et affichage immédiat du modal avec un loader
        $('#lbl-numero-bon').text(referenceBon);
        $('#lbl-auteur-bon').text(auteur);
        $('#lbl-agent-bon').text(agent);
        
        $('#tbl-produits-bon').html(`
            <tr>
                <td colspan="4" class="text-center py-3 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Récupération des produits depuis la base de données...
                </td>
            </tr>
        `);
        
        $('#modalDetailsBon').modal('show');

        // Appel AJAX pour récupérer les produits masqués derrière ce numéro de bon
        $.ajax({
            url: `/products-stock/details/${referenceBon}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let htmlRows = '';

                if (response.success && response.data.length > 0) {
                    response.data.forEach(item => {
                        htmlRows += `
                            <tr>
                                <td><span class="fw-semibold text-dark">${item.product_name}</span></td>
                                <td class="text-center fw-bold text-info font-14">${item.quantity}</td>
                                <td class="text-center font-13">${item.quantity_received !== null ? `<span class="badge bg-success-lighten text-success">${item.quantity_received}</span>` : '<span class="text-muted">-</span>'}</td>
                                <td class="text-center text-muted font-13">${item.quantity_returned ?? 0}</td>
                            </tr>
                        `;
                    });
                } else {
                    htmlRows = '<tr><td colspan="4" class="text-center text-danger py-2">Aucun produit trouvé ou accès non autorisé.</td></tr>';
                }
                
                // Injection des lignes dans le tableau du modal
                $('#tbl-produits-bon').html(htmlRows);
            },
            error: function(xhr) {
                console.error("Erreur lors de la récupération du bon : ", xhr);
                $('#tbl-produits-bon').html(`
                    <tr>
                        <td colspan="4" class="text-center text-danger py-2">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Échec du chargement des détails (Code ${xhr.status}).
                        </td>
                    </tr>
                `);
            }
        });
    });

    // 5. Case à cocher globale (Sélection de tous les bons)
    $('#checkAllRows').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
    });
});
$(document).on('click', '.btn-action-assign', function(e) {
    e.preventDefault();
    
    const $btn = $(this).closest('.btn-action-assign');
    const referenceBon = $btn.data('bon'); // 🌟 On se base uniquement sur la référence du bon
    const actionType = $btn.data('action'); 

    if (actionType === 'confirmer') {
        if (!referenceBon) {
            alert("Erreur technique : Le numéro de bon est introuvable.");
            return;
        }
        
        if (confirm(`Voulez-vous confirmer la réception globale du bon N° ${referenceBon} ?`)) {
            const originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="mdi mdi-spin mdi-loading me-1"></i> Traitement...');

            $.ajax({
                url: `/assignments/accept/${referenceBon}`, // 🌟 Envoi direct avec le numéro de bon
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    price: 0 
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        $('#assign-dt').DataTable().ajax.reload(null, false);
                    } else {
                        alert("Échec : " + response.message);
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Une erreur est survenue lors du traitement.");
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        }
    }
    // ... (Reste du code pour annuler)
});
</script>
@endsection
