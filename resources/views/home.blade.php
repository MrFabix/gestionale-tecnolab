@extends('layouts.app')

@section('content')
    <h1>Benvenuto, {{ Auth::user()->name }}</h1>
    <p class="text-muted">Questa è la tua dashboard di {{ config('app.name') }}.</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center p-4">
                <i class="bi bi-people-fill fs-1 text-primary"></i>
                <h3 class="mt-2">{{ \App\Models\Cliente::count() }}</h3>
                <p class="text-muted">Clienti</p>
                <a href="{{ route('clienti.index') }}" class="btn btn-outline-primary btn-sm">Vai ai clienti</a>
            </div>
        </div>

@endsection
