<div class="modal-header bg-primary text-white">
    <h5 class="modal-title" id="clotureModalLabel">
        <i class="mdi mdi-package-variant me-2"></i> Étape 1 : Recomptage du Stock — Agent : <strong>{{ $session->user->name }}</strong>
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('journal.stock-submit', $session->id) }}" method="POST" class="form-cloture-submit">
    @csrf
    <div class="modal-body">
        <div class="alert alert-info border-0 mb-4" role="alert">
            <i class="mdi mdi-information-outline me-2"></i>
            Veuillez compter physiquement les articles retournés par l'agent et saisir les quantités réelles ci-dessous. Le système calculera automatiquement les écarts.
        </div>

        <div class="table-responsive">
            <table class="table table-centered table-nowrap mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Article / Produit</th>
                        <th class="text-center" style="width: 150px;">Reçu (Matin)</th>
                        <th class="text-center" style="width: 150px;">Vendu (Théorique)</th>
                        <th class="text-center" style="width: 150px;">Reste Attendu</th>
                        <th class="text-center bg-soft-primary" style="width: 180px;">Physique Réel</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $assignment)
                        @php
                            // Calcul du reste attendu (théorique) en fonction de tes colonnes
                            $resteAttendu = $assignment->quantity_received - ($assignment->quantity_sold ?? 0);
                        @endphp
                        <tr>
                            <td>
                                <h6 class="font-size-14 mb-1">{{ $assignment->product->name ?? 'Produit inconnu' }}</h6>
                                <small class="text-muted">Réf Bon: {{ $assignment->reference_bon }}</small>
                            </td>
                            <td class="text-center font-monospace">{{ $assignment->quantity_received }}</td>
                            <td class="text-center text-danger font-monospace">{{ $assignment->quantity_sold ?? 0 }}</td>
                            <td class="text-center text-success font-monospace fw-bold">{{ $resteAttendu }}</td>
                            <td class="bg-soft-primary">
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           name="quantities[{{ $assignment->id }}]" 
                                           class="form-control text-center fw-bold input-stock-reel" 
                                           value="{{ $resteAttendu }}" 
                                           min="0" 
                                           data-attendu="{{ $resteAttendu }}"
                                           required>
                                    <span class="input-group-text">U</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aucun produit assigné à cette session.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal-footer table-light">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary" {{ $products->isEmpty() ? 'disabled' : '' }}>
            <i class="mdi mdi-check-circle-outline me-1"></i> Valider et passer à l'étape Cash
        </button>
    </div>
</form>