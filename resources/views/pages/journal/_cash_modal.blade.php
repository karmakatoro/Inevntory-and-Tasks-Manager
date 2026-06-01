<div class="modal-header bg-info-subtle text-info py-2 px-3">
    <h5 class="modal-title d-flex align-items-center fw-bold fs-6" id="closeDayTitle">
        <i class="fas fa-cash-register me-2"></i>
        <span>Fin de Service : <strong>{{ $session->user->name }}</strong></span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('journal.cash-validate', $session->id) }}" method="POST" class="form-cloture-submit needs-validation" id="requestCloseDay" novalidate>
    @csrf
    <div class="modal-body p-3">
        
        <div id="errorsDivClose" class="alert alert-danger mb-3 py-2 small" style="display:none;" role="alert">
            <strong>Erreur de validation</strong>
            <div class="errorsListClose mt-1"></div>
        </div>

        <div class="card bg-light-subtle mb-3 shadow-none border">
            <div class="card-body p-2">
                <div class="list-group list-group-flush small">
                    
                    <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-1.5 px-1">
                        <span class="text-muted">Ventes Totales</span>
                        <span class="fw-bold text-primary font-monospace">{{ number_format($totalSales, 2, ',', ' ') }} $</span>
                    </div>
                    
                    <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-1.5 px-1">
                        <span class="text-muted">Répartition</span>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success-subtle text-success font-monospace">Cash : {{ number_format($cashTodaySale, 2, ',', ' ') }} $</span>
                            <span class="badge bg-danger-subtle text-danger font-monospace">Dettes : {{ number_format($debtToSale, 2, ',', ' ') }} $</span>
                        </div>
                    </div>
                    
                    <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-1.5 px-1 border-0">
                        <span class="text-muted">Provenance Cash</span>
                        <div class="font-monospace text-end">
                            <span class="fw-bold text-body">{{ number_format($toPay, 2, ',', ' ') }} $</span> <small class="text-muted">(Ventes)</small>
                            <span class="mx-1 text-muted">|</span>
                            <span class="fw-bold text-body">{{ number_format($debtOldPay, 2, ',', ' ') }} $</span> <small class="text-muted">(Recouv.)</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card bg-body-tertiary text-center p-3 mb-3 border shadow-none">
            <small class="text-muted text-uppercase fw-bold tracking-wider d-block" style="font-size: 10px; letter-spacing: 0.05em;">Montant Théorique Attendu</small>
            <h3 class="fw-bold my-1 text-info font-monospace">{{ number_format($expectedAmount, 2, ',', ' ') }} $</h3>
            
            <div class="d-flex justify-content-center align-items-center gap-2 mt-1 small text-muted">
                <span>Fond Initial: <strong class="text-body font-monospace">{{ number_format($workSession->opening_cash, 2, ',', ' ') }} $</strong></span>
                <span class="opacity-50">•</span>
                <span>Flux Encaissés: <strong class="text-body font-monospace">{{ number_format($totalCashIn, 2, ',', ' ') }} $</strong></span>
            </div>
        </div>

        <div class="row justify-content-center pt-1">
            <div class="col-md-9 col-12 text-center">
                <label for="actual_amount" class="form-label small fw-bold text-secondary mb-2">
                    Combien de Cash ($) avez-vous physiquement en main ?
                </label>
                
                <div class="input-group input-group-md shadow-sm mx-auto" style="max-width: 290px;">
                    <span class="input-group-text"><i class="fas fa-calculator text-muted"></i></span>
                    <input type="number" 
                           step="0.01" 
                           class="form-control text-center fw-bold text-success font-monospace fs-5" 
                           name="actual_amount" 
                           id="actual_amount" 
                           value="{{ $expectedAmount }}" 
                           min="0" 
                           required 
                           autocomplete="off">
                    <span class="input-group-text bg-success text-white px-3 fw-bold">USD</span>
                </div>
            </div>
        </div>

    </div>
    
    <div class="modal-footer bg-body-tertiary border-top py-2 px-3 justify-content-end">
        <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" id="btnSaveClose" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="mdi mdi-lock-check me-1"></i> Valider & Fermer
        </button>
    </div>
</form>