<div class="modal fade" id="stock-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mouvement de Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="errorsDiv" class="alert alert-danger" style="display:none;">
                    <div class="errorsList"></div>
                </div>

                <form id="requestStockMovement" class="needs-validation">
                    @csrf
                    <input type="hidden" name="id" id="movementId" value="0">
                    <input type="hidden" name="product_id" id="modal_product_id">

                    <div class="row">
                        <div class="mb-3 col-12">
                            <label class="form-label">Produit</label>
                            <input type="text" id="modal_product_name" class="form-control" readonly>
                        </div>
                        <div class="mb-3 col-12">
                            <label class="form-label">Prix Unitaire ($)</label>
                            <input type="text" class="form-control" id="unitPrice" disabled>
                        </div>
                        <div class="mb-3 col-12">
                            <label for="quantity" class="form-label">Quantité</label>
                            <input type="number" name="quantity" class="form-control" id="quantity" required min="1">
                            <div class="invalid-feedback">Veuillez entrer une quantité valide.</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" id="btnSaveStock" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Configuration globale du CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 1. Détection du clic sur le bouton "Ajouter Stock" du DataTable
    $(document).on('click', '.btn-add-stock', function (e) {
        e.preventDefault();

        // Récupération des datas du bouton
        let productId = $(this).data('id');
        let productName = $(this).data('name');
        let productPrice = $(this).data('price');

        // Remplissage de la modal avec les bons IDs
        $('#modal_product_id').val(productId);
        $('#modal_product_name').val(productName);
        $('#unitPrice').val(productPrice);
        $('#movementId').val(0); // Reset à 0 pour une nouvelle entrée
        $('#quantity').val(''); // Vide le champ quantité

        // Affichage de la modal
        $('#stock-modal').modal('show');
    });

    // 2. Enregistrement du mouvement de stock via AJAX
    $('#btnSaveStock').on('click', function() {
        let form = $('#requestStockMovement');
        let formData = form.serialize();

        $.ajax({
            url: "{{ route('products-stock.store') }}",
            method: "POST",
            data: formData,
            beforeSend: function() {
                $('#btnSaveStock').prop('disabled', true).text('Enregistrement...');
            },
            success: function(response) {
                if (response.status) {
                    $('#stock-modal').modal('hide');
                    Swal.fire("Succès", response.message, "success");
                    // Recharge le DataTable si 'currentDt' est défini
                    if (typeof currentDt !== 'undefined') currentDt.ajax.reload();
                } else {
                    $('#errorsDiv').show();
                    $('.errorsList').html(response.message);
                }
            },
            error: function(xhr) {
                $('#errorsDiv').show();
                let errors = xhr.responseJSON.errors;
                let errorHtml = '<ul>';
                $.each(errors, function(key, value) {
                    errorHtml += '<li>' + value + '</li>';
                });
                errorHtml += '</ul>';
                $('.errorsList').html(errorHtml);
            },
            complete: function() {
                $('#btnSaveStock').prop('disabled', false).text('Save Changes');
            }
        });
    });
});
</script>
