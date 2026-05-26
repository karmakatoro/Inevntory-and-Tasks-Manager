<div class="modal fade" id="modalDetailsBon" tabindex="-1" aria-labelledby="modalDetailsBonLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalDetailsBonLabel">Détails du Bon de Sortie : <span id="lbl-numero-bon" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#statusAssign" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 pb-2 border-bottom text-muted font-13">
                    <div class="col-md-6"><strong>Auteur :</strong> <span id="lbl-auteur-bon"></span></div>
                    <div class="col-md-6 text-md-end"><strong>Agent Destinataire :</strong> <span id="lbl-agent-bon"></span></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-centered table-bordered mb-0">
                        <thead class="table-dark font-12">
                            <tr>
                                <th>Désignation du Produit</th>
                                <th class="text-center">Quantité Envoyée</th>
                                <th class="text-center">Quantité Reçue</th>
                                <th class="text-center">Restant / Retour</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-produits-bon">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>