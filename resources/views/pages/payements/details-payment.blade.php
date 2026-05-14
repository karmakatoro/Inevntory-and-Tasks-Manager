<!-- Modal Détails Paiement Premium -->
<div id="details-payment-modal" class="modal fade" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-dialog-centered pour le confort visuel -->
        <div class="modal-content border-0 shadow-lg"> <!-- border-0 et shadow-lg pour le style épuré -->
            <div class="modal-header bg-light border-bottom-0 py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle font-size-18">
                            <i class="mdi mdi-receipt-text-outline"></i>
                        </span>
                    </div>
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">Détails des Allocations</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Zone de chargement optimisée -->
                <div id="details-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="text-muted fw-medium">Récupération des factures liées...</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-secondary fw-semibold px-4" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary fw-semibold px-4" onclick="window.print()">
                    <i class="mdi mdi-printer me-1"></i> Imprimer
                </button>
            </div>
        </div>
    </div>
</div>