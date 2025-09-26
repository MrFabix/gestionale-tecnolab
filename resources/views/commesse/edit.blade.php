@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Modifica Commessa</h1>

        <form action="{{ route('commesse.update', $commessa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Codice</label>
                <input type="text" class="form-control" name="codice" value="{{ old('codice', $commessa->codice) }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrizione</label>
                <textarea class="form-control" name="descrizione" required>{{ old('descrizione', $commessa->descrizione) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Cliente</label>
                <select class="form-select" name="cliente_id" required>
                    @foreach($clienti as $cliente)
                        <option value="{{ $cliente->id }}" {{ $commessa->cliente_id == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->ragione_sociale }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data inizio</label>
                    <input type="date" class="form-control" name="data_inizio" value="{{ old('data_inizio', $commessa->data_inizio) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Data fine</label>
                    <input type="date" class="form-control" name="data_fine" value="{{ old('data_fine', $commessa->data_fine) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Stato</label>
                <select class="form-select" name="stato" required>
                    <option value="aperta" {{ $commessa->stato == 'aperta' ? 'selected' : '' }}>Aperta</option>
                    <option value="in_lavorazione" {{ $commessa->stato == 'in_lavorazione' ? 'selected' : '' }}>In lavorazione</option>
                    <option value="chiusa" {{ $commessa->stato == 'chiusa' ? 'selected' : '' }}>Chiusa</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Aggiorna</button>
            <a href="{{ route('commesse.index') }}" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
@endsection
