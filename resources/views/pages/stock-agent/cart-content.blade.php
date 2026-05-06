<div class="card-body">
    <div class="table-responsive" style="max-height: 300px;">
        <table class="table table-centered mb-0">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-center">Qté</th>
                    <th>Prix</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cart as $id => $details)
                    <tr>
                        <td>{{ $details['name'] }}</td>
                        <td>
                            <div class="input-group input-group-sm" style="width: 100px;">
                                <button class="btn btn-outline-secondary btn-update-qty" data-id="{{ $id }}"
                                    data-action="minus">-</button>
                                <input type="text" class="form-control text-center"
                                    value="{{ $details['quantity'] }}" readonly>
                                <button class="btn btn-outline-secondary btn-update-qty" data-id="{{ $id }}"
                                    data-action="plus">+</button>
                            </div>
                        </td>
                        <td>{{ number_format($details['price'], 2) }} $</td>
                        <td>{{ number_format($details['subtotal'], 2) }} $</td>
                        <td>
                            <button class="btn btn-sm btn-link text-danger btn-remove-item"
                                data-id="{{ $id }}">
                                <i class="mdi mdi-trash-can font-size-16"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-4 text-muted">Votre panier est vide.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top bg-light mt-2">
        <div class="mb-3">
            <label class="form-label font-weight-bold">Client <span class="text-danger">*</span></label>
            <select class="form-control select2" id="client_id" name="client_id">
                <option value="">-- Sélectionner un client --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" data-debt="{{ $client->total_debt }}">
                        {{ $client->name }} (Solde: {{ number_format($client->total_debt, 2) }}$)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-2">
            <div class="col-6">
                <label class="form-label">Montant Versé</label>
                <input type="number" step="0.01" class="form-control form-control-lg fw-bold" id="amount_paid"
                    placeholder="0.00">
                <input type="hidden" name="latitude" id="lat_input">
                <input type="hidden" name="longitude" id="lng_input">
            </div>
            <div class="col-6">
                <label class="form-label">Mode de paiement</label>
                <select class="form-control form-control-lg" id="payment_method">
                    <option value="cash">Cash</option>
                    <option value="m-pesa">M-Pesa</option>
                    <option value="airtel_money">Airtel Money</option>
                </select>
            </div>
        </div>

        <div class="mt-3 p-3 bg-white rounded border shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="font-size-15 fw-bold text-muted">Total Panier :</span>
                <h4 class="m-0 text-primary fw-extrabold">{{ number_format($total ?? 0, 2) }} $</h4>
                <input type="hidden" id="cart-total-hidden" value="{{ $total ?? 0 }}">
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">Reste à payer (Dette) :</span>
                <strong id="display_balance" class="text-danger font-size-16">0.00 $</strong>
            </div>
        </div>

        <button type="button" id="btn-validate-sale" class="btn btn-success btn-lg w-100 mt-3 shadow"
            {{ empty($cart) ? 'disabled' : '' }}>
            <i class="mdi mdi-cash-check me-1"></i> Finaliser et Encaisser
        </button>
    </div>
</div>
<script>
    $('#amount_paid').on('input', function() {
        // 1. Récupérer les valeurs proprement
        let totalCart = parseFloat($('#cart-total-hidden').val()) || 0;
        let amountPaid = parseFloat($(this).val());

        // 2. Gérer le cas où le champ est vide (effacé)
        if (isNaN(amountPaid) || $(this).val() === "") {
            amountPaid = 0;
            $('#display_balance').text(totalCart.toFixed(2) + " $").addClass('text-danger');
            return; // On s'arrête ici
        }

        // 3. Empêcher de dépasser le total
        if (amountPaid > totalCart) {
            alert("Le montant versé ne peut pas dépasser le total du panier (" + totalCart + "$)");
            amountPaid = totalCart;
            $(this).val(totalCart); // On corrige la valeur dans l'input
        }

        // 4. Calcul du reste
        let balance = totalCart - amountPaid;

        // 5. Mise à jour de l'affichage
        $('#display_balance').text(balance.toFixed(2) + " $");

        // 6. Gestion des couleurs pour le feedback visuel
        if (balance === 0) {
            $('#display_balance').removeClass('text-danger').addClass('text-success');
        } else {
            $('#display_balance').removeClass('text-success').addClass('text-danger');
        }
    });
    // On utilise 'var' ou une vérification sur 'window' pour éviter le conflit au rechargement AJAX
    if (typeof window.globalLocation === 'undefined') {
        window.globalLocation = {
            lat: null,
            lng: null
        };
    }

    // Fonction de capture (on ne la déclare qu'une fois aussi)
    if (typeof window.startGeolocCapture === 'undefined') {
        window.startGeolocCapture = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        window.globalLocation.lat = position.coords.latitude;
                        window.globalLocation.lng = position.coords.longitude;
                        console.log("📍 Position pré-capturée :", window.globalLocation);
                    },
                    function(error) {
                        console.warn("GPS non disponible :", error.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000
                    }
                );
            }
        };
    }

    // Lancer la capture
    $(document).ready(function() {
        window.startGeolocCapture();
    });


    // 3. Ton bouton de validation devient instantané
    $('#btn-validate-sale').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        let clientId = $('#client_id').val();
        let amountPaid = $('#amount_paid').val();
        let paymentMethod = $('#payment_method').val();
        let testAgentId = $('meta[name="current-agent-id"]').attr('content');

        if (!clientId) {
            alert("Veuillez sélectionner un client.");
            return;
        }

        // Désactivation immédiate pour éviter les doubles clics
        btn.prop('disabled', true).html('<i class="mdi mdi-spin mdi-loading"></i> Traitement...');

        // On utilise la position déjà capturée (si null, tant pis, on ne bloque pas la vente)
        envoiAjaxVente(clientId, amountPaid, paymentMethod, testAgentId, globalLocation.lat, globalLocation.lng,
            btn);
    });

    // Petite fonction helper pour l'envoi AJAX
    function envoiAjaxVente(clientId, amountPaid, paymentMethod, testAgentId, lat, lng, btn) {
        $.ajax({
            url: "{{ route('sales.store') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                client_id: clientId,
                amount_paid: amountPaid,
                payment_method: paymentMethod,
                agent_id: testAgentId,
                latitude: lat, // On ajoute la latitude
                longitude: lng // On ajoute la longitude
            },
            success: function(response) {
                if (response.status) {
                    alert("Succès : " + response.message);
                    reloadCart();
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Erreur serveur";
                alert("Erreur : " + errorMessage);
                btn.prop('disabled', false).html(
                    '<i class="mdi mdi-cash-check me-1"></i> Finaliser et Encaisser');
            }
        });
    }
</script>