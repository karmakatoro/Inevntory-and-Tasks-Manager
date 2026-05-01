<div class="modal fade" id="close-Day" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-lock me-2"></i>Clôture de ma Journée
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="p-3 mb-4 rounded-3 bg-light border">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="row">
                            <small class="text-muted d-block mb-1">Ventes du jour</small>
                            <h4 class="fw-bold text-dark mb-0" id="displayVentes">0.00 $</h4>
                            </div>
                            <div class="row">
                                <small class="text-muted d-block mb-1">Cash du jour</small>
                            <h4 class="fw-bold text-dark mb-0" id="displayCash">0.00 $</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block mb-1">Articles à rendre</small>
                            <h4 class="fw-bold text-primary mb-0" id="displayStock">0</h4>
                        </div>
                    </div>
                </div>

                <div class="alert alert-soft-warning d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>En validant, tout votre stock restant sera réintégré au Stock Central.</small>
                </div>

                <div id="errorsDiv" class="alert alert-danger" style="display:none;">
                    <div class="errorsList"></div>
                </div>

                <form id="requestCloseDay" class="needs-validation">
                    @csrf
                    <input type="hidden" name="agent_id" id="idCloseDay" value="0">

                    <input type="hidden" id="montantVendu" name="total_sales">

                    <div class="mb-3">
                        <div class="mb-3 col-lg-12 col-sm-12">
                            <label for="cashReceived" class="form-label">Montant Cash à remettre ($)</label>
                            <input type="text" class="form-control" name="cashReceived" id="cashReceived"
                                placeholder="0.00" required="">
                            <div class="form-text mt-2">Saisissez le montant physique total que vous déposez.</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 px-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="btnSaveClose" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Valider la clôture
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Configuration CSRF
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        // 1. CHARGEMENT DES STATS (Quand on ouvre le modal)
        $(document).on("click", '[data-bs-target="#close-Day"]', function() {
            let url = "{{ route('closing.stats') }}";

            // On met un petit loader visuel le temps que l'AJAX arrive
            $('#displayVentes').text('Chargement...');
            $('#displayCash').text('Chargement...');
            $('#displayStock').text('...');

            $.ajax({
                url: url,
                method: "GET",
                success: function(data) {
                    $('#displayVentes').text(data.total_sales + " $");
                    $('#displayStock').text(data.total_stock);
                    $('#displayCash').text(data.total_cash);
                    $('#montantVendu').val(data.total_sales);
                    $('#idCloseDay').val(data.agent_id);
                },
                error: function() {
                    console.error("Impossible de récupérer les stats de clôture");
                    $('#displayVentes').text("Erreur");
                    $('#displayCash').text("Erreur");
                } // La virgule manquante était ici
            });
        });

        // 2. ENVOI DE LA CLÔTURE
        $(document).on("click", "#btnSaveClose", function(e) {
            e.preventDefault();
            var form = $("#requestCloseDay");
            var submitBtn = $(this);

            // Désactiver le bouton
            submitBtn.html(
                `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Traitement...`
                );
            submitBtn.prop("disabled", true);

            $.ajax({
                // Assure-toi que cette route existe dans ton web.php
                url: "{{ route('sessions.close') }}",
                method: "POST",
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == true) {
                        Swal.fire({
                            title: "Félicitations!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#1abc9c",
                        }).then(() => {
                            // Recharger la page ou le tableau après succès
                            location.reload();
                        });

                        $("#close-Day").modal("hide");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Erreur",
                            text: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorString = "";
                    $.each(errors, function(key, value) {
                        errorString += value[0] + "<br>";
                    });
                    $(".errorsList").html(errorString);
                    $("#errorsDiv").fadeIn();
                },
                complete: function() {
                    submitBtn.html(
                        '<i class="fas fa-check-circle me-1"></i> Valider la clôture');
                    submitBtn.prop("disabled", false);
                }
            });
        });
    });
</script>
