@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Nuova Commessa</h1>

        <form action="{{ route('commesse.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Codice</label>
                <input type="text" class="form-control" name="codice" value="{{ old('codice') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrizione</label>
                <textarea class="form-control" name="descrizione" required>{{ old('descrizione') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Cliente</label>
                <select class="form-select" name="cliente_id" required>
                    <option value="">-- Seleziona cliente --</option>
                    @foreach($clienti as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->ragione_sociale }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data inizio</label>
                    <input type="date" class="form-control" name="data_inizio" value="{{ old('data_inizio') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data fine</label>
                    <input type="date" class="form-control" name="data_fine" value="{{ old('data_fine') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Stato</label>
                <select class="form-select" name="stato" required>
                    <option value="aperta" {{ old('stato') == 'aperta' ? 'selected' : '' }}>Aperta</option>
                    <option value="in_lavorazione" {{ old('stato') == 'in_lavorazione' ? 'selected' : '' }}>In lavorazione</option>
                    <option value="chiusa" {{ old('stato') == 'chiusa' ? 'selected' : '' }}>Chiusa</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Crea</button>
            <a href="{{ route('commesse.index') }}" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
@endsection
