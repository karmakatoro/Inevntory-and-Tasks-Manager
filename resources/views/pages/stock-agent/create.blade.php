<div class="modal fade" id="close-Day" tabindex="-1" role="dialog" aria-labelledby="closeDayTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeDayTitle">
                    <i class="fas fa-cash-register me-2 text-warning"></i>Fin de Service : <span id="displayAgentName">...</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="errorsDivClose" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade mb-3" style="display:none;" role="alert">
                    <strong>Erreur de validation</strong><br>
                    <div class="errorsListClose"></div>
                </div>

                <div class="row text-center mb-3 g-2">
                    <div class="col-md-3 col-sm-6">
                        <div class="p-2 border rounded">
                            <small class="text-muted d-block text-uppercase small">Ventes Totales</small>
                            <h4 class="fw-bold mb-0 text-primary" id="displayVentes">0.00 $</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-2 border rounded">
                            <small class="text-muted d-block text-uppercase small">Part Cash</small>
                            <h4 class="fw-bold mb-0 text-success" id="displayPartCash">0.00 $</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-2 border rounded">
                            <small class="text-muted d-block text-uppercase small">Part Crédit</small>
                            <h4 class="fw-bold mb-0 text-danger" id="displayPartCredit">0.00 $</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-2 border rounded">
                            <small class="text-muted d-block text-uppercase small">Stock Restant</small>
                            <h4 class="fw-bold mb-0 text-info" id="displayStock">0</h4>
                        </div>
                    </div>
                </div>

                <div class="row text-center mb-4 g-2">
                    <div class="col-md-6 col-sm-12">
                        <div class="p-2 border rounded">
                            <small class="text-muted d-block small">Encaissements sur Ventes du Jour</small>
                            <h5 class="fw-semibold mb-0" id="displaySurVentesJour">0.00 $</h5>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="p-2 border rounded">
                            <small class="text-muted d-block small">Recouvrements Dettes Anciennes</small>
                            <h5 class="fw-semibold mb-0" id="displaySurDettesAnciennes">0.00 $</h5>
                        </div>
                    </div>
                </div>

                <div class="card bg-light mb-4 text-center border-0">
                    <div class="card-body p-3">
                        <span class="text-muted text-uppercase small fw-semibold">Montant théorique attendu en caisse</span>
                        <h2 class="display-6 fw-bold my-1 text-dark" id="displayCash">0.00 $</h2>
                        <small class="text-muted">
                            Fond initial : <span id="displayFondInitial" class="fw-semibold">0.00 $</span> | 
                            Total Flux Cash Entré : <span id="displayTotalCashEntre" class="fw-semibold">0.00 $</span>
                        </small>
                    </div>
                </div>

                <div id="statusFeedback" class="alert align-items-center mb-4" style="display:none !important;" role="alert">
                    <i class="fas id-icon me-2"></i>
                    <span id="statusText" class="fw-medium"></span>
                </div>

                <form class="needs-validation" method="POST" action="{{ route('sessions.close') }}" id="requestCloseDay" novalidate>
                    @csrf
                    <input type="hidden" name="agent_id" id="idCloseDay" value="0">
                    <input type="hidden" name="total_sales" id="montantVendu" value="0">

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-sm-12">
                            <label for="cashReceived" class="form-label fw-bold">Combien de Cash ($) avez-vous physiquement en main ?</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                                <input type="number" step="0.01" class="form-control" name="cashReceived" id="cashReceived" placeholder="0.00" required autocomplete="off">
                                <div class="invalid-feedback">
                                    Veuillez inscrire le montant physique compté dans votre caisse.
                                </div>
                            </div>
                            <div class="form-text text-center mt-2">
                                Contrôlez rigoureusement votre encaisse avant de soumettre les données.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="btnSaveClose" class="btn btn-primary">Confirmer la clôture</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
    let expectedRaw = 0;

    // Chargement des données à l'ouverture du modal
    $(document).on("click", '[data-bs-target="#close-Day"]', function() {
        let url = "{{ route('closing.stats') }}";
        
        // Nettoyage visuel initial (Placeholders)
        $('#displayVentes, #displayPartCash, #displayPartCredit, #displayStock, #displaySurVentesJour, #displaySurDettesAnciennes, #displayCash, #displayFondInitial, #displayTotalCashEntre').text('...');
        $('#displayAgentName').text('Chargement...');
        $('#errorsDivClose').hide();

        $.get(url, function(data) {
            // Extraction depuis la structure JSON imbriquée
            let ventes = data.stats.ventes_du_jour;
            let encaissements = data.stats.encaissements;
            let bilan = data.stats.bilan_caisse;

            // Injection des données de performance et stocks
            $('#displayAgentName').text(data.agent_name || "Agent");
            $('#displayVentes').text(ventes.total + " $");
            $('#displayPartCash').text(ventes.part_cash + " $");
            $('#displayPartCredit').text(ventes.part_credit + " $");
            $('#displayStock').text(ventes.stock || "0");
            
            // Injection des segments d'encaissement demandés
            $('#displaySurVentesJour').text(encaissements.sur_ventes_du_jour + " $");
            $('#displaySurDettesAnciennes').text(encaissements.sur_dettes_anciennes + " $");
            $('#displayTotalCashEntre').text(encaissements.total_cash_entre + " $");
            
            // Injection des totaux du bilan comptable
            $('#displayCash').text(bilan.total_attendu + " $");
            $('#displayFondInitial').text(bilan.fond_initial + " $");
            
            // Nettoyage de la chaîne formatée pour la logique mathématique JS
            let cleanExpected = bilan.total_attendu.replace(/,/g, '');
            expectedRaw = parseFloat(cleanExpected) || 0;
            
            // Hydratation des variables invisibles du formulaire
            $('#idCloseDay').val(data.agent_id || 0);
            $('#montantVendu').val(ventes.total);
            
            // Remise à zéro du champ de saisie utilisateur
            $('#cashReceived').val('');
            $('#statusFeedback').attr('style', 'display:none !important');
        }).fail(function() {
            console.error("Impossible de joindre l'API de clôture");
            $('#displayVentes, #displayStock, #displayCash').text("Erreur");
        });
    });

    // Évaluation en temps réel des flux de caisse (Ecart / Juste / Surplus)
    $('#cashReceived').on('input', function() {
        let valInput = $(this).val();
        let typed = parseFloat(valInput) || 0;
        let diff = typed - expectedRaw;
        let feedback = $('#statusFeedback');
        let text = $('#statusText');

        if (valInput.length > 0) {
            feedback.attr('style', 'display:flex !important'); // Force l'affichage Bootstrap
            
            if (Math.abs(diff) < 0.01) {
                feedback.attr('class', 'alert alert-success align-items-center mb-4');
                text.html('<strong>Caisse équilibrée :</strong> Le tiroir-caisse correspond aux écritures système.');
                $('.id-icon').attr('class', 'fas fa-check-circle id-icon me-2');
            } else if (diff < 0) {
                feedback.attr('class', 'alert alert-warning align-items-center mb-4');
                text.html('<strong>Déficit détecté :</strong> Il manque <strong>' + Math.abs(diff).toFixed(2) + ' $</strong> par rapport au théorique.');
                $('.id-icon').attr('class', 'fas fa-exclamation-triangle id-icon me-2');
            } else {
                feedback.attr('class', 'alert alert-info align-items-center mb-4');
                text.html('<strong>Excédent de caisse :</strong> Un surplus de <strong>' + diff.toFixed(2) + ' $</strong> est enregistré.');
                $('.id-icon').attr('class', 'fas fa-info-circle id-icon me-2');
            }
        } else {
            feedback.attr('style', 'display:none !important');
        }
    });

    // Soumission du formulaire en AJAX lors du clic sur le bouton de confirmation
    $('#btnSaveClose').on('click', function(e) {
        e.preventDefault();
        
        let form = $('#requestCloseDay');
        let btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Traitement...');
        $('#errorsDivClose').hide().find('.errorsListClose').empty();

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method') || 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.status && response.redirect) {
                    // Force la redirection reçue du contrôleur
                    window.location.href = response.redirect;
                } else {
                    btn.prop('disabled', false).text('Confirmer la clôture');
                    alert(response.message || "Une erreur est survenue.");
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Confirmer la clôture');
                $('#errorsDivClose').show();
                
                if (xhr.status === 422) { // Erreurs de validation Laravel
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('.errorsListClose').append('<li>' + value[0] + '</li>');
                    });
                } else {
                    $('.errorsListClose').append('<li>' + (xhr.responseJSON.message || "Erreur système lors de la clôture.") + '</li>');
                }
            }
        });
    });
});
    </script>