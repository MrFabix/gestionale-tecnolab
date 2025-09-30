@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" style="max-width:220px; max-height:120px;">
                <h1 class="mt-3">Benvenuto in {{ config('app.name', 'Gestionale Tecnolab') }}</h1>
                <p class="lead">Gestione clienti, commesse, report e utenti in un unico punto.</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Cosa puoi fare:</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="bi bi-people-fill text-primary me-2"></i>Gestisci i <strong>Clienti</strong></li>
                            <li class="list-group-item"><i class="bi bi-briefcase-fill text-success me-2"></i>Gestisci le <strong>Commesse</strong></li>
                            <li class="list-group-item"><i class="bi bi-file-earmark-text-fill text-info me-2"></i>Crea e consulta i <strong>Report</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> Usa il menu a sinistra per navigare tra le sezioni.
                </div>
            </div>
        </div>
    </div>
@endsection

