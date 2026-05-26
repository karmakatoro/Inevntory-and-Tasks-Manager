@php $totalGeneral = 0; @endphp

@if (count(session('assign_cart', [])) > 0)
    <div class="card-body border-bottom bg-light">
        <div class="form-group mb-0">
            <label class="form-label text-primary fw-bold">Attribuer ce lot à :</label>
            <select class="form-select select2" name="agent_id" id="target_agent_id">
                <option value="">-- Choisir l'agent --</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif

<div class="table-responsive">
    <table class="table align-middle">
        <tbody>
            @forelse(session('assign_cart', []) as $id => $item)
                @php
                    $sousTotal = $item['price'] * $item['quantity'];
                    $totalGeneral += $sousTotal;
                    $res = \App\Models\TemporaryReservation::where('session_id', session()->getId())->where('product_id', $id)->first();
                    $expiry = $res ? $res->expires_at->toIso8601String() : now()->toIso8601String();
                @endphp
                <tr>
                    <td>
                        <h6 class="m-0">{{ $item['name'] }}</h6>
                        <small class="text-muted">${{ number_format($item['price'], 2) }}</small>
                    </td>
                    <td>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <button class="btn btn-outline-secondary btn-update-qty" data-id="{{ $id }}" data-action="minus">-</button>
                            <input type="text" class="form-control text-center" value="{{ $item['quantity'] }}" readonly>
                            <button class="btn btn-outline-secondary btn-update-qty" data-id="{{ $id }}" data-action="plus">+</button>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-light text-dark border timer-badge" data-expiry="{{ $expiry }}">
                            <i class="fas fa-clock me-1 text-primary"></i>
                            <span class="timer-text">10:00</span>
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-danger btn-remove-item" data-id="{{ $id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4">Panier vide</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (count(session('assign_cart', [])) > 0)
    <div class="p-3 border-top">
        <div class="d-flex justify-content-between mb-3">
            <span class="fw-bold">Total :</span>
            <span class="fw-bold text-primary">${{ number_format($totalGeneral, 2) }}</span>
        </div>
        <button type="button" id="btn-validate-assignment" class="btn btn-primary w-100">
            Valider l'attribution
        </button>
    </div>
@endif
<script>
function updateTimers() {
    document.querySelectorAll('.timer-badge').forEach(function(badge) {
        const expiryDate = new Date(badge.getAttribute('data-expiry'));
        const now = new Date();
        const diff = expiryDate - now;
        const textElement = badge.querySelector('.timer-text');

        if (!textElement) return;

        if (diff <= 0) {
            textElement.innerHTML = "EXPIRÉ";
            badge.classList.remove('bg-light');
            badge.classList.add('bg-secondary', 'text-white');
        } else {
            const minutes = Math.floor((diff / 1000 / 60) % 60);
            const seconds = Math.floor((diff / 1000) % 60);
            textElement.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (diff < 60000) {
                badge.classList.remove('bg-light');
                badge.classList.add('bg-danger', 'text-white', 'animate-pulse');
            }
        }
    });
}

setInterval(updateTimers, 1000);

$(document).off('click', '#btn-validate-assignment').on('click', '#btn-validate-assignment', function (e) {
    e.preventDefault();

    const btn = $(this);
    const agentId = $('#target_agent_id').val();

    if (!agentId) {
        alert("Choisissez un agent !");
        return;
    }

    if (!confirm("Confirmer le transfert ?")) return;

    // ÉTAPE CRUCIALE : On désactive le bouton immédiatement
    btn.prop('disabled', true).text('Traitement en cours...');

    $.ajax({
        url: "{{ route('stock.assgin') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            agent_id: agentId
        },
        success: function(response) {
            if (response.status) {
                alert(response.message);
                location.reload();
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON ? xhr.responseJSON.message : "Erreur inconnue";
            alert("Erreur système : " + msg);
            // On réactive le bouton seulement en cas d'erreur
            btn.prop('disabled', false).text('Valider l\'attribution');
        }
    });
});

</script>

<style>
.animate-pulse {
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
