@extends('layouts.base')

@section('title', 'Products Stock - ' . env('APP_NAME'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title text-body">Gestion de Stock</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.sales') }}">Tableau de Bord</a>
                        </li>
                        <li class="breadcrumb-item active">Gestion de Stock</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                   <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <a href="#stock" data-bs-toggle="tab" aria-expanded="false" class="nav-link active py-2" role="tab" aria-selected="true">
            <i class="mdi mdi-storefront me-1 font-18 align-middle"></i> 
            <span class="d-none d-md-inline">Stock Central</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="#cart-content" data-bs-toggle="tab" aria-expanded="false" class="nav-link text-muted py-2" role="tab" aria-selected="false">
            <i class="mdi mdi-basket-plus me-1 font-18 align-middle"></i> 
            <span class="d-none d-md-inline">Panier d'attribution</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="#assign" data-bs-toggle="tab" aria-expanded="true" class="nav-link text-muted py-2" role="tab" aria-selected="false">
            <i class="mdi mdi-dolly me-1 font-18 align-middle"></i> 
            <span class="d-none d-md-inline">Attribution de Stock</span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="#stock-history" data-bs-toggle="tab" aria-expanded="true" class="nav-link text-muted py-2" role="tab" aria-selected="false">
            <i class="mdi mdi-history me-1 font-18 align-middle"></i> 
            <span class="d-none d-md-inline">Historique de mouvement</span>
        </a>
    </li>
</ul>

                    <div class="tab-content pt-3">
                        <div class="tab-pane active show" id="stock" role="tabpanel">
                            @include('pages.product-stock.stock')
                        </div>

                        <div class="tab-pane" id="cart-content" role="tabpanel">
                            <div id="cart-ajax-container">
                                <div class="text-center p-5 text-muted">
                                    <div class="spinner-border text-primary m-2" role="status"></div>
                                    <p class="mt-2">Chargement du panier...</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="stock-history" role="tabpanel">
                            @include('pages.product-stock.stock-history')
                        </div>

                        <div class="tab-pane" id="assign" role="tabpanel">
                            @include('pages.product-stock.assign')
                        </div>
                    </div> </div> </div> </div> </div> <script>
    $(document).ready(function() {
        // --- 1. Configuration Globale ---
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        // --- 2. Initialisation DataTable Stock ---
        var currentDt = $("#stock-history-dt").DataTable({
            autoWidth: false,
            order: [[3, "desc"]], // Tri par défaut sur la date pour la pertinence
            processing: true,
            serverSide: true,
            ajax: {
                url: $("#stock-history-dt").attr("data-api-url"),
            },
            columns: [
                { data: "checkbox", name: "checkbox", orderable: false, searchable: false },
                { data: "product", name: "product" },
                { data: "operation", name: "operation" },
                { data: "date", name: "date" },
                { data: "price", name: "price" },
                { data: "quantity", name: "quantity", className: "text-center" },
                { data: "author", name: "author" },
                { data: "status", name: "status" },
                { data: "action", name: "action", orderable: false, searchable: false },
            ],
            lengthMenu: [10, 25, 50, 100],
            language: {
                "sProcessing": "Traitement en cours...",
                "sSearch": "Rechercher :",
                "sLengthMenu": "Afficher _MENU_ Produits",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ Produits",
                "sEmptyTable": "Aucun mouvement en cours.",
                "oPaginate": {
                    "sNext": "Suivant",
                    "sPrevious": "Précédent"
                }
            },
        });

        // --- 3. Fonction de rechargement du Panier ---
        function reloadCart(showLoader = false) {
            let container = $('#cart-ajax-container');
            let url = "{{ route('cart.fetch') }}";

            if (showLoader) {
                container.html(
                    '<div class="text-center p-5"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Chargement du panier...</p></div>'
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
                        container.html('<div class="alert alert-warning">Erreur : ' + (response.message || 'Données invalides') + '</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    container.html('<div class="alert alert-danger">Erreur : Impossible de charger le panier.</div>');
                },
                complete: function() {
                    container.css('opacity', '1');
                }
            });
        }

        // Rendre la fonction accessible globalement
        window.reloadCart = reloadCart;

        // Recharger le panier quand on clique sur l'onglet
        $('a[href="#cart-content"]').on('shown.bs.tab', function(e) {
            reloadCart(true);
        });

        // --- 4. Gestion Edit / Delete Historique ---
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            let url = $(this).attr('data-url');
            $.get(url, function(response) {
                if (response.status) {
                    $("#movementId").val(response.data.id);
                    $("#product_id").val(response.data.product_id);
                    $("#quantity").val(response.data.quantity);
                    $("#movement-stock-modal").modal('show');
                }
            });
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let url = $(this).attr('data-url');
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
                        success: function(response) {
                            if (response.status) {
                                Swal.fire("Supprimé !", response.message, "success");
                                currentDt.ajax.reload();
                            }
                        }
                    });
                }
            });
        });

        // --- 5. Gestion des actions du Panier ---
        $(document).on('click', '.btn-update-qty', function(e) {
            e.preventDefault();
            let ProductId = $(this).data('id');
            let action = $(this).data('action');
            let url = (action === 'plus') ? "{{ route('cart.increment') }}" : "{{ route('cart.decrement') }}";

            $.ajax({
                url: url,
                method: 'POST',
                data: { product_id: ProductId },
                success: function(response) {
                    if (response.status) {
                        reloadCart();
                    } else {
                        Swal.fire("Attention", response.message, "warning");
                    }
                },
                error: function() {
                    Swal.fire("Erreur", "Impossible de mettre à jour la quantité", "error");
                }
            });
        });

        $(document).on('click', '.btn-remove-item', function(e) {
            e.preventDefault();
            let ProductId = $(this).data('id');
            let url = "{{ route('cart.remove') }}";

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
                        data: { product_id: ProductId },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire("Supprimé !", response.message, "success");
                                reloadCart();
                            }
                        }
                    });
                }
            });
        });

        // --- 6. Configuration de la section Attribution ---
        const $table = $('#assign-dt');
        if ($table.length > 0) {
            const apiUrl = $table.data('api-url');
            let selectedStatus = 'all';

            const tableInstance = $table.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: apiUrl,
                    type: 'GET',
                    data: function (d) {
                        d.status = selectedStatus;
                    },
                    error: function (xhr) {
                        console.error("Erreur DataTables : ", xhr.responseText);
                    }
                },
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false, width: '20px' },
                    { data: 'reference_bon' },
                    { data: 'sender' },
                    { data: 'receiver' },
                    { data: 'created_at' },
                    { data: 'total_distinct_products', className: 'text-center' },
                    { data: 'status', orderable: true },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center', width: '100px' }
                ],
                order: [[4, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
                    "sEmptyTable": "Aucune donnée disponible dans le tableau",
                    "sInfo": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                    "sInfoEmpty": "Affichage de l'élément 0 à 0 sur 0 élément",
                    "sInfoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                    "sLengthMenu": "Afficher _MENU_ éléments",
                    "sLoadingRecords": "Chargement...",
                    "sProcessing": "Traitement...",
                    "sSearch": "Rechercher :",
                    "sZeroRecords": "Aucun élément correspondant trouvé",
                    "oPaginate": {
                        "sFirst": "Premier",
                        "sLast": "Dernier",
                        "sNext": "Suivant",
                        "sPrevious": "Précédent"
                    }
                }
            });

            // Gestion du Dropdown Filter
            $('.btn-status-filter').on('click', function(e) {
                e.preventDefault();
                $('.btn-status-filter').removeClass('active');
                $(this).addClass('active');

                selectedStatus = $(this).data('status');
                const textLabel = $(this).text().trim().replace('●', '');
                $('#active-filter-label').text(textLabel);

                if (selectedStatus !== 'all') {
                    $('#dropdownFilterStatus').removeClass('btn-light').addClass('btn-soft-primary');
                } else {
                    $('#dropdownFilterStatus').removeClass('btn-soft-primary').addClass('btn-light');
                }

                tableInstance.ajax.reload();
            });

            // Récupération des détails du Bon de Sortie (Ajusté pour s'adapter au Dark Mode)
            $(document).on('click', '.btn-view-details', function(e) {
                e.preventDefault();
                const referenceBon = $(this).data('bon');
                const auteur = $(this).data('sender');
                const agent = $(this).data('receiver');

                $('#lbl-numero-bon').text(referenceBon);
                $('#lbl-auteur-bon').text(auteur);
                $('#lbl-agent-bon').text(agent);
                
                $('#tbl-produits-bon').html(`
                    <tr>
                        <td colspan="4" class="text-center py-3 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Récupération des produits...
                        </td>
                    </tr>
                `);
                
                $('#modalDetailsBon').modal('show');

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
                                        <td><span class="fw-semibold text-body">${item.product_name}</span></td>
                                        <td class="text-center fw-bold text-info font-14">${item.quantity}</td>
                                        <td class="text-center font-13">${item.quantity_received !== null ? `<span class="badge bg-soft-success text-success">${item.quantity_received}</span>` : '<span class="text-muted">-</span>'}</td>
                                        <td class="text-center text-muted font-13">${item.quantity_returned ?? 0}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            htmlRows = '<tr><td colspan="4" class="text-center text-danger py-2">Aucun produit trouvé ou accès non autorisé.</td></tr>';
                        }
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

            // Case à cocher globale
            $('#checkAllRows').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', isChecked);
            });
        }
    });
</script>
@endsection