@extends('layouts.app')

@section('content')
    <h1>Nuovo Cliente</h1>

    {{-- Messaggi di errore validazione --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $errore)
                    <li>{{ $errore }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clienti.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Ragione Sociale *</label>
            <input type="text" name="ragione_sociale" class="form-control" value="{{ old('ragione_sociale') }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Indirizzo</label>
                <input type="text" name="indirizzo" class="form-control" value="{{ old('indirizzo') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">CAP</label>
                <input type="text" name="cap" class="form-control" value="{{ old('cap') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Città</label>
                <input type="text" name="citta" class="form-control" value="{{ old('citta') }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 mb-3">
                <label class="form-label">Provincia</label>
                <input type="text" name="provincia" class="form-control" value="{{ old('provincia') }}">
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">Partita IVA</label>
                <input type="text" name="partita_iva" class="form-control" value="{{ old('partita_iva') }}">
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">Codice Fiscale</label>
                <input type="text" name="codice_fiscale" class="form-control" value="{{ old('codice_fiscale') }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Telefono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Referente</label>
            <input type="text" name="referente" class="form-control" value="{{ old('referente') }}">
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('clienti.index') }}" class="btn btn-secondary">Annulla</a>
            <button type="submit" class="btn btn-success">Salva Cliente</button>
        </div>
    </form>
@endsection
