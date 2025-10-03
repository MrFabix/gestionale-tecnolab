@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifica Report</h1>
    <form action="{{ route('reports.update', $report) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="commessa_id" class="form-label">Commessa</label>
            <select name="commessa_id" id="commessa_id" class="form-select" required>
                @foreach($commesse as $commessa)
                    <option value="{{ $commessa->id }}" @if($report->commessa_id == $commessa->id) selected @endif>
                        {{ $commessa->codice }} - {{ $commessa->cliente->ragione_sociale ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="tipo_prova" class="form-label">Tipo Prova</label>
            <input type="text" name="tipo_prova" id="tipo_prova" class="form-control" value="{{ old('tipo_prova', $report->tipo_prova) }}" required>
        </div>
        <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" name="data" id="data" class="form-control" value="{{ old('data', $report->data ? $report->data->format('Y-m-d') : '') }}">
        </div>
        <div class="mb-3">
            <label for="data_accettazione_materiale" class="form-label">Data Accettazione Materiale</label>
            <input type="date" name="data_accettazione_materiale" id="data_accettazione_materiale" class="form-control" value="{{ old('data_accettazione_materiale', $report->data_accettazione_materiale ? $report->data_accettazione_materiale->format('Y-m-d') : '') }}">
        </div>
        <div class="mb-3">
            <label for="rif_ordine" class="form-label">Rif. Ordine</label>
            <input type="text" name="rif_ordine" id="rif_ordine" class="form-control" value="{{ old('rif_ordine', $report->rif_ordine) }}">
        </div>
        <div class="mb-3">
            <label for="data_ordine" class="form-label">Data Ordine</label>
            <input type="date" name="data_ordine" id="data_ordine" class="form-control" value="{{ old('data_ordine', $report->data_ordine ? $report->data_ordine->format('Y-m-d') : '') }}">
        </div>
        <div class="mb-3">
            <label for="oggetto" class="form-label">Oggetto</label>
            <input type="text" name="oggetto" id="oggetto" class="form-control" value="{{ old('oggetto', $report->oggetto) }}">
        </div>
        <div class="mb-3">
            <label for="stato_fornitura" class="form-label">Stato Fornitura</label>
            <input type="text" name="stato_fornitura" id="stato_fornitura" class="form-control" value="{{ old('stato_fornitura', $report->stato_fornitura) }}">
        </div>
        <div class="mb-3">
            <label for="rapporto_numero" class="form-label">Rapporto Numero</label>
            <input type="text" name="rapporto_numero" id="rapporto_numero" class="form-control" value="{{ old('rapporto_numero', $report->rapporto_numero) }}">
        </div>
        <div class="mb-3">
            <label for="numero_revisione" class="form-label">Numero Revisione</label>
            <input type="text" name="numero_revisione" id="numero_revisione" class="form-control" value="{{ old('numero_revisione', $report->numero_revisione) }}">
        </div>
        <button type="submit" class="btn btn-primary">Salva modifiche</button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Annulla</a>
    </form>
</div>
@endsection

