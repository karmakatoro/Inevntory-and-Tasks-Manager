<div class="modal-header bg-success text-white">
    <h5 class="modal-title" id="clotureModalLabel">
        <i class="mdi mdi-cash-multiple me-2"></i> Étape 2 : Encaissement Financier — Agent : <strong>{{ $session->user->name }}</strong>
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('journal.cash-submit', $session->id) }}" method="POST" class="form-cloture-submit">
    @csrf
    <div class="modal-body px-4">
        
        <div class="d-flex align-items-center p-3 bg-soft-success text-success rounded mb-4">
            <i class="mdi mdi-checkbox-marked-circle-outline font-size-24 me-3"></i>
            <div>
                <h6 class="mb-0 fw-bold text-success">Étape Logistique Validée</h6>
                <small class="text-muted-success">Les invendus de stock ont été vérifiés et réintégrés au dépôt.</small>
            </div>
        </div>

        <div class="row g-4 mb-3">
            <div class="col-md-6">
                <div class="p-3 border rounded bg-light h-100">
                    <span class="text-muted text-uppercase font-size-12 fw-semibold">Attendu Système</span>
                    <h2 class="mt-2 text-primary fw-bold font-monospace">
                        {{ number_format($totalSalesCash, 2, ',', ' ') }} <small class="font-size-14">USD</small>
                    </h2>
                    <p class="text-muted mb-0 font-size-13 mt-3">
                        Ce montant correspond au cumul net de toutes les ventes déclarées "Cash" par l'agent au cours de sa session.
                    </p>
                    <input type="hidden" name="expected_amount" value="{{ $totalSalesCash }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 border rounded bg-soft-secondary h-100">
                    <label for="actual_amount" class="text-muted text-uppercase font-size-12 fw-semibold">Montant Physique Reçu</label>
                    <div class="input-group input-group-lg mt-2">
                        <input type="number" 
                               step="0.01" 
                               id="actual_amount" 
                               name="actual_amount" 
                               class="form-control fw-bold text-success font-monospace" 
                               value="{{ $totalSalesCash }}" 
                               min="0" 
                               required>
                        <span class="input-group-text bg-success text-white font-size-16 fw-bold">USD</span>
                    </div>
                    <p class="text-muted mb-0 font-size-13 mt-3">
                        Comptez les billets physiques remis par l'agent et saisissez la somme exacte collectée dans la caisse centrale.
                    </p>
                </div>
            </div>
        </div>

    </div>
    
    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-success">
            <i class="mdi mdi-lock-check me-1"></i> Confirmer l'encaissement & Sceller la session
        </button>
    </div>
</form>