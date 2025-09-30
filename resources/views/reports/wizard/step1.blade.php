@extends('layouts.app')

@section('content')
    <h2>Nuovo Report — Step 1: Intestazione</h2>

    <form method="POST" action="{{ route('reports.wizard.step1.post') }}" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Commessa</label>
            <select name="commessa_id" class="form-select" required>
                <option value="">-- Seleziona --</option>
                @foreach($commesse as $c)
                    <option value="{{ $c->id }}" {{ session('report_wizard.commessa_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->codice }} — {{ $c->descrizione }} ({{ $c->cliente->ragione_sociale ?? '—' }})
                    </option>
                @endforeach
            </select>
            @error('commessa_id') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        {{--DATA--}}
        <div class="mb-3">
            <label class="form-label">Data </label>
            <input type="date" name="data" class="form-control" value="{{ session('report_wizard.data', date('Y-m-d')) }}" required>
            @error('data') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        {{--Accettazzione dell materiale--}}
        <div class="mb-3">
            <label class="form-label">Accettazione materiale del</label>
            <input type="date" name="data_accettazione_materiale" class="form-control" value="{{ session('report_wizard.data_accettazione_materiale', date('Y-m-d')) }}" required>
            @error('data_accettazione_materiale') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <div class="row align-items-center mb-3">
                <!-- Rif. Ordine -->
                <div class="col-md-4">
                    <label for="rif_ordine" class="form-label">Rif. Ordine n°</label>
                    <input type="text" class="form-control" id="rif_ordine" name="rif_ordine" placeholder="12345" value="{{ session('report_wizard.rif_ordine') }}" required>
                </div>

                <!-- Data Ordine -->
                <div class="col-md-4">
                    <label for="data_ordine" class="form-label">del</label>
                    <input type="date" class="form-control" id="data_ordine" name="data_ordine" value="{{ session('report_wizard.data_ordine') }}" required>
                </div>
            </div>
        </div>
        <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <label class="form-label">Rapporto N°</label>
                    <input  type="text" name="rapporto_numero" class="form-control" value="{{ session('report_wizard.rapporto_numero', $rapporto_numero_default) }}" required>
                    @error('rapporto_numero') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Numero revisione</label>
                    <input type="text" name="numero_revisione" class="form-control" value="{{ session('report_wizard.numero_revisione') ?? 0 }}" required>
                    @error('numero_revisione') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Annulla</a>
            <button type="submit" class="btn btn-primary">Avanti</button>
        </div>
    </form>
@endsection
