<div class="modal-header">
    <h5 class="modal-title">Contrôle des Stocks - Session de {{ $session->user->name }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('journal.validate.stock', $session->id) }}" method="POST" class="form-cloture-submit">
    @csrf
    <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-centered table-nowrap mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produit</th>
                        <th class="text-center">Quantité Système</th>
                        <th class="text-center" style="width: 180px;">Quantité Réelle (Physique)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($session->assignment as $assignation)
                        <tr>
                            <td>
                                <span class="fw-medium">{{$assignation->product->name }}</span>
                            </td>
                            <td class="text-center font-monospace fw-bold text-primary">
                                {{ $assignation->quantity }}
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           name="stock_physique[{{ $assignation->id }}]" 
                                           class="form-control text-center fw-bold" 
                                           min="0" 
                                           required 
                                           placeholder="0">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-check-all me-1"></i> Intégrer et Valider le Stock
        </button>
    </div>
</form>