@extends('layouts.base')

@section('title', 'Products Stock - ' . env('APP_NAME'))

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Products Stock</h4>
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
                        <a href="#stock" data-bs-toggle="tab" aria-expanded="false" class="nav-link ms-0 active"
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
                    <div class="tab-pane active show" id="stock" role="tabpanel">
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
                let url = "{{ route('cart.fetch') }}";

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
                            container.html('<div class="alert alert-warning">Erreur : ' + (response.message || 'Données invalides') + '</div>');
                        }
                    },
                    error: function(xhr) {
                        container.html('<div class="alert alert-danger">Erreur : Impossible de charger le panier.</div>');
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
                ajax: { url: $("#stock-history-dt").attr("data-api-url") },
                columns: [
                    { data: "checkbox", name: "checkbox", orderable: false, searchable: false },
                    { data: "product", name: "product" },
                    { data: "operation", name: "operation" },
                    { data: "date", name: "date" },
                    { data: "price", name: "price" },
                    { data: "quantity", name: "quantity" },
                    { data: "author", name: "author" },
                    { data: "status", name: "status" },
                    { data: "action", name: "action", orderable: false, searchable: false },
                ],
            });

            // --- 4. GESTION DES ONGLETS ---
            $('a[href="#cart-content"]').on('shown.bs.tab', function(e) {
                reloadCart(true);
            });

            // --- 5. ACTIONS DU PANIER (Update Qty) ---
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
                            // Pas besoin de Swal pour chaque clic de quantité (optionnel)
                            reloadCart();
                        } else {
                            Swal.fire("Attention", response.message, "warning");
                        }
                    },
                    error: function() {
                        Swal.fire("Erreur", "Impossible de mettre à jour la quantité", "error");
                    }
                });
            }); // <-- Fermeture correcte ici

            // --- 6. ACTIONS DU PANIER (Remove Item) ---
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
                                    Swal.fire("Supprimé !", response.message, "success");
                                    currentDt.ajax.reload();
                                }
                            }
                        });
                    }
                });
            });

        }); // FIN DU DOCUMENT READY
    </script>
@endsection
