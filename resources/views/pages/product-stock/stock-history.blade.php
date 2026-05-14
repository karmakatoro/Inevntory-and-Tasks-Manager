<div class="row">
    <div class="col-12">
        <div class="row mb-2">
            <div class="col-sm-4">
                <a href="#" class="btn btn-success mb-2" data-bs-toggle="modal"
                    data-bs-target="#movement-stock-modal"><i class="mdi mdi-history me-1"></i>Approvisionner le stock central</a>

            </div>
            <div class="col-sm-8">
                <div class="text-sm-end">
                    <button data-url="{{ route('dm-prcat') }}" type="button"
                        class="btn btn-danger mb-2 me-1 delete-all">
                        <i class="mdi mdi-trash-can-outline"></i></button>
                    <a href="javascript:void(0);" class="btn btn-primary mb-2"><i class="mdi mdi-printer me-1"></i>Imprimer</a>
                    <a href="javascript:void(0);" class="btn btn-success mb-2"><i
                            class="mdi mdi-database-export me-1"></i>Exporter</a>
                </div>
            </div><!-- end col-->
        </div>

        <div class="table-responsive">
            <table class="table table-centered dt-responsive nowrap w-100" id="stock-history-dt"
                data-api-url="{{ route('products-stock.index') }}">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20px;">
                            <div class="form-check font-16 mb-0">
                                <input id="checkAllRows" class="form-check-input" type="checkbox" id="customerlist">
                                <label class="form-check-label" for="customerlist">&nbsp;</label>
                            </div>
                        </th>
                        <th>Informations sur les Produits</th>
                        <th>Operation</th>
                        <th>Date de création</th>
                        <th>Prix </th>
                        <th>Quantité</th>
                        <th>Auteur</th>
                        <th>Status</th>
                        <th style="width: 75px;">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@include('pages.product-stock.modal-movement-stock');
