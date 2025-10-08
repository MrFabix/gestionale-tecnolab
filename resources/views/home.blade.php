@extends('layouts.app')
@section('content')
    <style>
        body {
            background: linear-gradient(120deg, #f5f6fa 60%, #e9ecef 100%);
        }
        .logo-home {
            max-width: 240px;
            max-height: 130px;
            box-shadow: 0 4px 24px rgba(217,4,41,0.12);
            border-radius: 12px;
            border: 2px solid #d90429;
            background: #fff;
        }
        .welcome-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #222;
            letter-spacing: 1px;
        }
        .lead {
            color: #555;
        }
        .card {
            border-radius: 18px;
            transition: box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 8px 32px rgba(217,4,41,0.18);
        }
        .list-group-item {
            font-size: 1.15rem;
            padding: 1rem 1.2rem;
            border: none;
            background: #f8f9fa;
        }
        .list-group-item i {
            font-size: 1.5rem;
            vertical-align: middle;
        }
        .badge-funzione {
            font-size: 0.95rem;
            margin-left: 8px;
            background: #d90429;
            color: #fff;
        }
        .alert-info {
            background: linear-gradient(90deg, #d90429 0%, #222 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            box-shadow: 0 2px 12px rgba(217,4,41,0.08);
        }
        .bi {
            vertical-align: middle;
        }
    </style>
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo-home mb-2">
                <h1 class="welcome-title mt-3">Benvenuto in {{ config('app.name', 'Gestionale Tecnolab') }}</h1>
                <p class="lead">Gestione clienti, commesse, report e utenti in un unico punto.<br><span class="text-danger fw-bold"></span></p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-danger">Cosa puoi fare:</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-people-fill text-primary me-2"></i>
                                Gestisci i <strong>Clienti</strong>
                                <span class="badge badge-funzione">Anagrafica</span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-briefcase-fill text-success me-2"></i>
                                Gestisci le <strong>Commesse</strong>
                                <span class="badge badge-funzione">Progetti</span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-file-earmark-text-fill text-info me-2"></i>
                                Crea e consulta i <strong>Report</strong>
                                <span class="badge badge-funzione">Documenti</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i> Usa il menu a sinistra per navigare tra le sezioni.<br>
                    <span class="fw-light">Hai bisogno di aiuto? Contatta l'amministratore.</span>
                </div>
            </div>
        </div>
    </div>
@endsection
