@extends('layouts.auth') {{-- Remplace par le nom de ton layout login si différent --}}

@section('title', 'Ouverture de Session - ' . env('APP_NAME'))

@section('content')
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4 class="text-uppercase mt-0">Ouverture de Journée</h4>
                    <p class="text-muted">Veuillez initialiser votre caisse pour commencer.</p>
                </div>

                <form id="session-open-form" method="POST" action="{{ route('sessions.store') }}">
                    @csrf
                    <div id="session-alert" class="alert d-none"></div>
                    <div class="mb-3">
                        <label for="opening_cash" class="form-label">Fonds de caisse initial ($)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-cash-register"></i></span>
                            <input class="form-control" type="number" step="0.01" name="opening_cash" id="opening_cash"
                                placeholder="0.00" required autofocus>
                        </div>
                        <small class="text-muted">Comptez l'argent physique présent dans votre tiroir ce matin.</small>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Note d'ouverture (Optionnel)</label>
                        <textarea class="form-control" name="note" id="note" rows="2" placeholder="Ex: Reliquat d'hier..."></textarea>
                    </div>

                    <div class="mb-0 text-center d-grid">
                        <button class="btn btn-primary" type="submit">
                            <i class="mdi mdi-play-circle-outline me-1"></i> Démarrer la Session
                        </button>
                    </div>

                </form>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <p class="text-muted">Besoin d'aide ? Contactez l'administrateur.</p>
            </div>
        </div>
</div> @endsection

