<div class="modal fade" id="ModalPaie" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-hand-holding-usd me-2"></i>Encaisser un Paiement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="p-3 mb-4 rounded-3 border border-info-subtle" style="background-color: #e3f2fd;">
                    <div class="row text-center">
                        <div class="col-6 border-end border-info-subtle">
                            <small class="text-muted d-block mb-1">Dette Totale</small>
                            <h4 class="fw-bold text-danger mb-0" id="displayDettes">0.00 $</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Dernier Versement</small>
                            <h4 class="fw-bold text-info mb-0" id="displayLastPaie">0.00 $</h4>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="font-size: 0.85rem; background-color: #f0f7ff;">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    <span class="text-dark">Le paiement sera affecté aux factures les plus anciennes.</span>
                </div>

                <form id="formPaiementDette">
                    @csrf
                    <input type="hidden" name="customer_id" id="idClientPaie">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Montant à Encaisser ($)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-info text-info fw-bold">$</span>
                            <input type="number" step="0.01" class="form-control border-info fw-bold"
                                   name="amount" id="amountToPay" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Mode de règlement</label>
                            <select class="form-select border-info" name="payment_method">
                                <option value="cash">💵 Cash</option>
                                <option value="m-pesa">📱 M-Pesa</option>
                                <option value="airtel_money">📱 Airtel Money</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="btnSubmitPaiement" class="btn btn-info px-4 text-white shadow">
                    <i class="fas fa-check-circle me-1"></i> Confirmer le paiement
                </button>
            </div>
        </div>
    </div>
</div>
<script>

$(document).ready(function() {
    // 1. Quand on clique sur le bouton "Payer" dans une liste de clients
    $(document).on("click", ".btn-paie", function() {
        let clientId = $(this).data('customer');
        let totalDebt = $(this).data('debt');
        let lastPaie = $(this).data('last');
            
        $('#idClientPaie').val(clientId);
        $('#displayDettes').text(totalDebt + " $");
        $('#displayLastPaie').text(lastPaie + " $");

        // On réinitialise le champ montant
        $('#amountToPay').val('').attr('max', totalDebt);

        $('#ModalPaie').modal('show');
    });

    // 2. Validation du paiement
    $('#btnSubmitPaiement').on('click', function() {
        let btn = $(this);
        let form = $('#formPaiementDette');

        if($('#amountToPay').val() <= 0) {
            Swal.fire("Erreur", "Veuillez saisir un montant valide.", "error");
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enregistrement...');

        $.ajax({
            url: "{{ route('payement.store') }}", // Crée cette route dans Laravel
            method: "POST",
            data: form.serialize(),
            success: function(response) {
                if(response.status) {
                    Swal.fire("Succès", response.message, "success").then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Confirmer le paiement');
                Swal.fire("Erreur", "Une erreur est survenue lors du traitement.", "error");
            }
        });
    });
});
 </script>
