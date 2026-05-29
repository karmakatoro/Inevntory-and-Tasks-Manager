@extends('layouts.base') 
@section('title', 'Journal - ' . env('APP_NAME'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Journal des Clôtures</h4>
                <div class="page-title-right">
                    <span class="badge bg-soft-info text-info font-size-12 fw-bold p-2">
                        <i class="mdi mdi-circle-medium text-info animate-pulse me-1"></i> File d'attente active
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent py-3 border-bottom-0">
                    <h5 class="card-title mb-0 font-size-15">
                        <i class="mdi mdi-tray-full me-2 text-primary"></i>Sessions en attente de validation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 align-middle" id="journal-table">
                            <thead>
                                <tr>
                                    <th>Agent de vente</th>
                                    <th>Début Session</th>
                                    <th class="text-center">Étape 1 : Stock</th>
                                    <th class="text-center">Étape 2 : Cash</th>
                                    <th class="text-end">Action requise</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($queueSessions as $session)
                                    <tr>
                                        <td class="fw-semibold">
                                            <i class="mdi mdi-account-circle-outline me-2 text-muted font-size-16"></i>
                                            {{ $session->user->name ?? 'Agent inconnu' }}
                                        </td>
                                        
                                        <td class="text-muted font-monospace">
                                            {{ $session->created_at->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="text-center">
                                            @if($session->status == 'en_attente_cloture')
                                                <span class="badge bg-soft-warning text-warning p-2 px-3">
                                                    <i class="mdi mdi-clock-outline me-1"></i> À contrôler
                                                </span>
                                            @else
                                                <span class="badge bg-soft-success text-success p-2 px-3">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i> Réceptionné
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if($session->status == 'stock_valide')
                                                <span class="badge bg-soft-danger text-danger p-2 px-3">
                                                    <i class="mdi mdi-cash-register me-1"></i> À encaisser
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary text-muted p-2 px-3">
                                                    <i class="mdi mdi-lock-outline me-1"></i> Bloqué
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            @if($session->status == 'en_attente_cloture')
                                                <button class="btn btn-sm btn-primary px-3 btn-open-cloture" data-url="{{ route('journal.stock.modal', $session->id) }}">
                                                    <i class="mdi mdi-package-variant me-1"></i> Réceptionner Stock
                                                </button>
                                            @elseif($session->status == 'stock_valide')
                                                <button class="btn btn-sm btn-success px-3 btn-open-cloture" data-url="{{ route('journal.cash.modal', $session->id) }}">
                                                    <i class="mdi mdi-cash-multiple me-1"></i> Encaisser Cash
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-check-all text-success display-4 d-block mb-2"></i>
                                            <span class="fw-medium">Félicitations ! La file d'attente est vide.</span><br>
                                            <small>Toutes les sessions opérationnelles ont été traitées et clôturées.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clotureDynamicModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0 bg-body" id="modal-dynamic-content">
            </div>
        </div>
    </div>
    <script>
$(document).ready(function() {
    // Configuration globale AJAX pour sécuriser les requêtes (comme sur ta page de référence)
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    console.log("Le script dynamique du journal s'exécute correctement !");

    // 1. Chargement dynamique du modal (Stock ou Cash) au clic
    $(document).on('click', '.btn-open-cloture', function(e) {
        e.preventDefault();
        
        const targetUrl = $(this).data('url');
        console.log("Événement déclenché ! Récupération de l'URL : ", targetUrl);

        if (!targetUrl) {
            console.error("Erreur : data-url est manquant.");
            return;
        }

        // Loader temporaire dans la structure du modal
        $('#modal-dynamic-content').html(`
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2 mb-0">Chargement des données de clôture...</p>
            </div>
        `);

        $('#clotureDynamicModal').modal('show');

        // Récupération HTML via AJAX GET
        $.get(targetUrl, function(htmlResult) {
            $('#modal-dynamic-content').html(htmlResult);
        }).fail(function(xhr) {
            $('#clotureDynamicModal').modal('hide');
            console.error("Échec du chargement du modal : ", xhr.status);
            alert("Erreur technique : Impossible de charger le formulaire.");
        });
    });

    // 2. Traitement de la soumission asynchrone des formulaires chargés
    $(document).on('submit', '.form-cloture-submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const submitBtn = $form.find('button[type="submit"]');
        const originalBtnHtml = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="mdi mdi-spin mdi-loading me-1"></i> Traitement...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#clotureDynamicModal').modal('hide');
                    alert(response.message);
                    window.location.reload(); 
                } else {
                    alert("Échec : " + response.message);
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("Une erreur interne est survenue lors de la validation.");
                submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
});
</script>
@endsection

