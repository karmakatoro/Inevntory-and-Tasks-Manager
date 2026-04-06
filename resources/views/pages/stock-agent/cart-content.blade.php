<table class="table table-centered mb-0">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Qté</th>
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
                        <button class="btn btn-outline-secondary btn-update-qty" data-id="{{ $id }}" data-action="minus">-</button>
                        <input type="text" class="form-control text-center" value="{{ $details['quantity'] }}" readonly>
                        <button class="btn btn-outline-secondary btn-update-qty" data-id="{{ $id }}" data-action="plus">+</button>
                    </div>
                </td>
                <td>{{ number_format($details['price'], 2) }} $</td>
                <td>{{ number_format($details['subtotal'], 2) }} $</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-remove-item" data-id="{{ $id }}">
                        <i class="mdi mdi-trash-can"></i>
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
<div class="card-body border-top bg-light">
    <div class="mb-3">
        <label class="form-label font-weight-bold">Client <span class="text-danger">*</span></label>
        <select class="form-control select2" id="client_id" name="client_id">
            <option value="">-- Sélectionner un client --</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" data-debt="{{ $client->total_debt }}">
                    {{ $client->name }} (Solde: {{ $client->total_debt }}$)
                </option>
            @endforeach
        </select>
    </div>

    <hr>

    <div class="row g-2">
        <div class="col-6">
            <label class="form-label">Montant Versé</label>
            <input type="number" step="0.01" class="form-control form-control-lg" id="amount_paid" placeholder="0.00">
        </div>
        <div class="col-6">
            <label class="form-label">Mode</label>
            <select class="form-control" id="payment_method">
                <option value="cash">Cash</option>
                <option value="m-pesa">M-Pesa</option>
                <option value="airtel_money">Airtel Money</option>
            </select>
        </div>
    </div>

    <div class="mt-3 p-2 bg-white rounded border">
        <div class="d-flex justify-content-between">
            <span>Reste à payer (Dette) :</span>
            <strong id="display_balance" class="text-danger">0.00 $</strong>
        </div>
    </div>

    <button type="button" id="btn-validate-sale" class="btn btn-primary btn-lg w-100 mt-3">
        <i class="mdi mdi-check-all"></i> Valider la Vente
    </button>
</div>
<div class="mt-3 border-top pt-3">
    <div class="d-flex justify-content-between">
        <h4>Total :</h4>
        <h4 class="text-primary">{{ number_format($total ?? 0, 2) }} $</h4>
    </div>
    <button class="btn btn-success w-100 mt-2" data-bs-toggle="modal" data-bs-target="#paymentModal" {{ empty($cart) ? 'disabled' : '' }}>
        <i class="mdi mdi-cash-check"></i> Encaisser la vente
    </button>
</div>
