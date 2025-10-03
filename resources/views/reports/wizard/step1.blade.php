@extends('layouts.app')

@section('content')
    <h2>{{ isset($editMode) && $editMode ? 'Modifica Report' : 'Nuovo Report' }} — Step 1: Intestazione</h2>

    <form method="POST" action="{{ isset($editMode) && $editMode ? route('reports.editwizard.step1.post', $report) : route('reports.wizard.step1.post') }}" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Commessa</label>
            <select name="commessa_id" class="form-select" required>
                <option value="">-- Seleziona --</option>
                @foreach($commesse as $c)
                    <option value="{{ $c->id }}"
                        @if((isset($editMode) && $editMode && old('commessa_id', session('report_edit_wizard.commessa_id', $report->commessa_id ?? null)) == $c->id) || (!isset($editMode) && session('report_wizard.commessa_id') == $c->id)) selected @endif>
                        {{ $c->codice }} — {{ $c->descrizione }} ({{ $c->cliente->ragione_sociale ?? '—' }})
                    </option>
                @endforeach
            </select>
            @error('commessa_id') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        {{--DATA--}}
        <div class="mb-3">
            <label class="form-label">Data </label>
            <input type="date" name="data" class="form-control" value="{{ isset($editMode) && $editMode ? old('data', session('report_edit_wizard.data', $report->data ? $report->data->format('Y-m-d') : date('Y-m-d'))) : session('report_wizard.data', date('Y-m-d')) }}" required>
            @error('data') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        {{--Accettazione materiale--}}
        <div class="mb-3">
            <label class="form-label">Accettazione materiale del</label>
            <input type="date" name="data_accettazione_materiale" class="form-control" value="{{ isset($editMode) && $editMode ? old('data_accettazione_materiale', session('report_edit_wizard.data_accettazione_materiale', $report->data_accettazione_materiale ? $report->data_accettazione_materiale->format('Y-m-d') : date('Y-m-d'))) : session('report_wizard.data_accettazione_materiale', date('Y-m-d')) }}" required>
            @error('data_accettazione_materiale') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <div class="row align-items-center mb-3">
                <!-- Rif. Ordine -->
                <div class="col-md-4">
                    <label for="rif_ordine" class="form-label">Rif. Ordine n°</label>
                    <input type="text" class="form-control" id="rif_ordine" name="rif_ordine" placeholder="12345" value="{{ isset($editMode) && $editMode ? old('rif_ordine', session('report_edit_wizard.rif_ordine', $report->rif_ordine ?? '')) : session('report_wizard.rif_ordine') }}" required>
                </div>
                <!-- Data Ordine -->
                <div class="col-md-4">
                    <label for="data_ordine" class="form-label">del</label>
                    <input type="date" class="form-control" id="data_ordine" name="data_ordine" value="{{ isset($editMode) && $editMode ? old('data_ordine', session('report_edit_wizard.data_ordine', $report->data_ordine ? $report->data_ordine->format('Y-m-d') : '')) : session('report_wizard.data_ordine') }}" required>
                </div>
            </div>
        </div>
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <label class="form-label">Rapporto N°</label>
                <input type="text" name="rapporto_numero" class="form-control"
                       value="{{ isset($editMode) && $editMode
                        ? (old('rapporto_numero') ?: (session('report_edit_wizard.rapporto_numero') ?? $report->rapporto_numero))
                        : (old('rapporto_numero') ?: session('report_wizard.rapporto_numero', $rapporto_numero_default)) }}"
                required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Numero Revisione</label>
                <input type="text" name="numero_revisione" class="form-control" value="{{ isset($editMode) && $editMode ? old('numero_revisione', session('report_edit_wizard.numero_revisione', $report->numero_revisione ?? '')) : session('report_wizard.numero_revisione') }}">
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Annulla</a>
            <button type="submit" class="btn btn-primary">Avanti</button>
        </div>
    </form>
@endsection
