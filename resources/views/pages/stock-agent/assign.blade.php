<div class="row">
    <div class="col-12">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-4">
                <div class="col-sm-4">
    <div class="dropdown mb-2">
        <button class="btn btn-light border dropdown-toggle" type="button" id="dropdownFilterStatus" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-filter-variant me-1 text-muted"></i>
            Statut : <span id="active-filter-label" class="fw-semibold text-primary">Tous</span>
        </button>
        
        <ul class="dropdown-menu dropdown-menu-animated shadow-sm" aria-labelledby="dropdownFilterStatus">
            <li>
                <button class="dropdown-item btn-status-filter active" data-status="all" type="button">
                    <i class="mdi mdi-format-list-bulleted me-2 text-muted"></i>Tous les bons
                </button>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <button class="dropdown-item btn-status-filter" data-status="en_attente" type="button">
                    <span class="badge bg-warning-lighten text-warning rounded-circle me-2">●</span>En attente
                </button>
            </li>
            <li>
                <button class="dropdown-item btn-status-filter" data-status="approuve" type="button">
                    <span class="badge bg-success-lighten text-success rounded-circle me-2">●</span>Approuvés
                </button>
            </li>
        </ul>
    </div>
</div>
            </div>
            <div class="col-sm-8">
                <div class="text-sm-end">
                    <button data-url="{{ route('dm-prcat') }}" type="button" class="btn btn-danger mb-2 me-1 delete-all">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                    <a href="javascript:void(0);" class="btn btn-primary mb-2"><i class="mdi mdi-printer me-1"></i>Imprimer</a>
                    <a href="javascript:void(0);" class="btn btn-success mb-2"><i class="mdi mdi-database-export me-1"></i>Exporter</a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-centered table-nowrap table-hover w-100" id="assign-dt" data-api-url="{{ route('show-assign-stock') }}">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20px;">
                            <div class="form-check mb-0">
                                <input id="checkAllRows" class="form-check-input" type="checkbox">
                                <label class="form-check-label" for="checkAllRows">&nbsp;</label>
                            </div>
                        </th>
                        <th>N° Bon de Sortie</th>
                        <th>Auteur (Assigné par)</th>
                        <th>Bénéficiaire (Agent)</th>
                        <th>Date d'Opération</th>
                        <th>Total Produits</th> <th>Statut</th>
                        <th style="width: 100px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>
@include('pages.product-stock.modal-details-bon') 






