<div class="modal fade" id="ModalPaie" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">
                    <i class="mdi mdi-cash-multiple me-2"></i>Encaisser un Paiement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="p-3 mb-4 rounded-3 border border-info-subtle shadow-sm" style="background-color: #f8fbff;">
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

                <form id="formPaiementDette" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="customer_id" id="idClientPaie">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant à Encaisser ($)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-info text-info fw-bold">$</span>
                            <input type="number" step="0.01" class="form-control border-info fw-bold"
                                   name="amount" id="amountToPay" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Mode de règlement</label>
                        <select class="form-select border-info" name="payment_method">
                            <option value="cash">💵 Espèces (Cash)</option>
                            <option value="m-pesa">📱 M-Pesa</option>
                            <option value="airtel_money">📱 Airtel Money</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" id="btnSubmitPaiement" class="btn btn-info px-4 text-white shadow">
                    <i class="mdi mdi-check-circle-outline me-1"></i>Confirmer le paiement
                </button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // 1. Ouverture et remplissage du modal
    $(document).on("click", ".btn-paie", function() {
        let clientId = $(this).data('customer');
        let totalDebt = $(this).data('debt');
        let lastPaie = $(this).data('last') || "0.00";
            
        $('#idClientPaie').val(clientId);
        $('#displayDettes').text(totalDebt + " $");
        $('#displayLastPaie').text(lastPaie + " $");

        // On pré-remplit avec le montant total pour aller plus vite, ou on laisse vide selon ton choix
        $('#amountToPay').val('').attr('placeholder', totalDebt);

        $('#ModalPaie').modal('show');
    });

    // 2. Traitement AJAX (Inspiré de ton bouton btnSave)
    $('#btnSubmitPaiement').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        let form = $('#formPaiementDette');
        let amount = $('#amountToPay').val();

        if(amount <= 0 || amount === "") {
            Swal.fire("Attention", "Veuillez saisir un montant valide.", "warning");
            return;
        }

        // État Loading identique à ton autre modal
        btn.prop('disabled', true).html(
            `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Enregistrement...`
        );

        $.ajax({
            url: "{{ route('payement.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(response) {
                if(response.status) {
                    Swal.fire({
                        title: "Succès!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#1abc9c",
                    }).then(() => {
                        $('#ModalPaie').modal('hide');
                        // Recharge la table DataTables si elle s'appelle currentDt
                        if (typeof currentDt !== 'undefined') currentDt.ajax.reload();
                        else location.reload();
                    });
                } else {
                    Swal.fire("Erreur", response.message, "error");
                    btn.prop('disabled', false).html("<i class='mdi mdi-check-circle-outline me-1'></i>Confirmer le paiement");
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html("<i class='mdi mdi-check-circle-outline me-1'></i>Confirmer le paiement");
                Swal.fire("Erreur", "Une erreur technique est survenue.", "error");
            }
        });
    });
});
</script>
