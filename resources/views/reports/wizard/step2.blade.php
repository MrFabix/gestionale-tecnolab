@extends('layouts.app')

@section('content')
    <h2>{{ isset($editMode) && $editMode ? 'Modifica Report' : 'Nuovo Report' }} — Step 2: Tipo di Prova</h2>

    <form method="POST" action="{{ isset($editMode) && $editMode ? route('reports.editwizard.step2.post', $report) : route('reports.wizard.step2.post') }}" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tipo di prova</label>
            <select name="tipo_prova" class="form-select" required>
                <option value="resilienza" {{ (isset($editMode) && $editMode ? old('tipo_prova', session('report_edit_wizard.tipo_prova', $report->tipo_prova ?? null)) : session('report_wizard.tipo_prova')) == 'resilienza' ? 'selected' : '' }}>Resilienza</option>
                <option value="trazione" {{ (isset($editMode) && $editMode ? old('tipo_prova', session('report_edit_wizard.tipo_prova', $report->tipo_prova ?? null)) : session('report_wizard.tipo_prova')) == 'trazione' ? 'selected' : '' }}>Trazione</option>
                <option value="chimica" {{ (isset($editMode) && $editMode ? old('tipo_prova', session('report_edit_wizard.tipo_prova', $report->tipo_prova ?? null)) : session('report_wizard.tipo_prova')) == 'chimica' ? 'selected' : '' }}>Analisi Chimica</option>
            </select>
            @error('tipo_prova') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Oggetto</label>
            <input type="text" name="oggetto" class="form-control" value="{{ isset($editMode) && $editMode ? old('oggetto', session('report_edit_wizard.oggetto', $report->oggetto ?? '')) : session('report_wizard.oggetto') }}" required>
            @error('oggetto') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Stato di fornitura</label>
            <input type="text" name="stato_fornitura" class="form-control" value="{{ isset($editMode) && $editMode ? old('stato_fornitura', session('report_edit_wizard.stato_fornitura', $report->stato_fornitura ?? '')) : session('report_wizard.stato_fornitura') }}" required>
            @error('stato_fornitura') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="excel_file" class="form-label">Carica Excel (opzionale)</label>
            <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv">
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ isset($editMode) && $editMode ? route('reports.editwizard.step1', $report) : route('reports.wizard.step1') }}" class="btn btn-outline-secondary">Indietro</a>
            <button type="submit" class="btn btn-primary">Avanti</button>
        </div>
    </form>
@endsection
