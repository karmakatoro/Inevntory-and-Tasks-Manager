@extends('layouts.base')

@section('title', 'Recouvrement Dettes - ' . env('APP_NAME'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Recouvrement & Créances</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.sales') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Dettes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-sm bg-soft-danger rounded">
                                <i class="fe-trending-down avatar-title font-22 text-danger"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark my-1"><span id="displayGlobalDebt" data-plugin="counterup">{{ number_format($totalGlobalDebt ?? 0, 2) }}</span> $</h3>
                                <p class="text-muted mb-1 text-truncate">Dette Totale</p>
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
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0" id="payment-dt" 
                               data-api-url="{{ route('payement.credit', ['agent' => Auth::user()->id]) }}">
                            <thead class="table-light">
                                <tr>
                                    <th>Client</th>
                                    <th>Total Facturé</th>
                                    <th>Total Payé</th>
                                    <th>Reste à Payer</th>
                                    <th style="width: 125px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.payements.create')
    @include('pages.payements.historique')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
            });

            var apiUrl = $("#payment-dt").attr("data-api-url");

            var currentDt = $("#payment-dt").DataTable({
                processing: true,
                serverSide: true,
                ajax: apiUrl,
             columns: [
    { data: "customer", name: "name" },
    { data: "total_du", name: "sales_sum_total_amount", className: "fw-bold" },
    { data: "total_paye", name: "sales_sum_amount_paid", className: "text-success" },
    { data: "reste", name: "reste", searchable: false }, 
    { data: "action", name: "action", orderable: false, searchable: false }
],
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
                    }
                }
            });

            // Script pour ouvrir le modal de paiement
            $(document).on('click', '.btn-paie', function() {
                const customerId = $(this).data('customer');
                const debt = $(this).data('debt');
                
                $('#modal-customer-id').val(customerId);
                $('#display-debt').text(debt + ' $');
                $('#payment-modal').modal('show');
            });
        });
  
$(document).on('click', '.btn-details', function() {
    // On récupère les données stockées dans le bouton
    const customerName = $(this).data('name');
    const history = $(this).data('history');

    // On injecte dans la modal
    $('#detail-customer-name').text(customerName);
    $('#detail-history-content').html(history);

    // On affiche la modal
    $('#modal-details').modal('show');
});
    </script>
@endsection