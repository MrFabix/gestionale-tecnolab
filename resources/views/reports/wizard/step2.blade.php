@extends('layouts.app')

@section('content')
    <h2>Nuovo Report — Step 2: Tipo di Prova</h2>

    <form method="POST" action="{{ route('reports.wizard.step2.post') }}" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tipo di prova</label>
            <select name="tipo_prova" class="form-select" required>
                <option value="resilienza">Resilienza</option>
                <option value="trazione">Trazione</option>
                <option value="chimica">Analisi Chimica</option>
            </select>
            @error('tipo_prova') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Oggetto</label>
            <input type="text" name="oggetto" class="form-control" value="{{ session('report_wizard.oggetto') }}" required>
            @error('oggetto') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Stato di fornitura</label>
            <input type="text" name="stato_fornitura" class="form-control" value="{{ session('report_wizard.stato_fornitura') }}" required>
            @error('stato_fornitura') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="excel_file" class="form-label">Carica Excel (opzionale)</label>
            <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv">
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('reports.wizard.step1') }}" class="btn btn-outline-secondary">Indietro</a>
            <button type="submit" class="btn btn-primary">Avanti</button>
        </div>
    </form>
@endsection
