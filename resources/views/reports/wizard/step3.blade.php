@extends('layouts.app')

@section('content')
    <h2>{{ isset($editMode) && $editMode ? 'Modifica Report' : 'Nuovo Report' }} — Step 3: Dati Prova ({{ ucfirst($tipo) }})</h2>

    <form method="POST" action="{{ isset($editMode) && $editMode ? route('reports.editwizard.step3.post', $report) : route('reports.wizard.step3.post') }}" class="card p-4 shadow-sm">
        @csrf

        {{-- ================= RESILIENZA ================= --}}
        @if($tipo === 'resilienza')
            <h5 class="mb-3 text-primary"><i class="bi bi-bar-chart"></i> Parametri Resilienza (UNI EN ISO 148-1:2018)</h5>
            <div class="row g-4">
                @for($i = 0; $i < 3; $i++)
                    @php
                        $provino = isset($editMode) && $editMode
                            ? (old('provini.' . $i) ?? ($report->dati['provini'][$i] ?? []))
                            : (old('provini.' . $i) ?? (session('report_wizard.provini.' . $i) ?? []));
                    @endphp
                    <div class="col-md-12">
                        <div class="card border-primary shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-hexagon-fill me-2"></i>Provino N° {{ $i+1 }}
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Test N°</label>
                                        <input type="text" name="provini[{{ $i }}][codice]" class="form-control" value="{{ $provino['codice'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tipo</label>
                                        <input type="text" name="provini[{{ $i }}][tipo]" class="form-control" value="{{ $provino['tipo'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Direzione</label>
                                        <input type="text" name="provini[{{ $i }}][direzione]" class="form-control" value="{{ $provino['direzione'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Spessore (B) mm</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][spessore_mm]" class="form-control" value="{{ $provino['spessore_mm'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Larghezza (W) mm</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][larghezza_mm]" class="form-control" value="{{ $provino['larghezza_mm'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Lunghezza (L) mm</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][lunghezza_mm]" class="form-control" value="{{ $provino['lunghezza_mm'] ?? '' }}">
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Temperatura (°C)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][temperatura_C]" class="form-control" value="{{ $provino['temperatura_C'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Energia (J)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][energia_J]" class="form-control" value="{{ $provino['energia_J'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Media (J)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][media_J]" class="form-control" value="{{ $provino['media_J'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Area duttile (%)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][area_duttile_percent]" class="form-control" value="{{ $provino['area_duttile_percent'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Espansione laterale (mm)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][espansione_laterale_mm]" class="form-control" value="{{ $provino['espansione_laterale_mm'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Note</label>
                                    <input type="text" name="provini[{{ $i }}][note]" class="form-control" value="{{ $provino['note'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
        {{-- ================= TRAZIONE ================= --}}
        @if($tipo === 'trazione')
            @php
                $trazione = isset($editMode) && $editMode
                    ? (old('trazione', $report->dati['trazione'] ?? []))
                    : (old('trazione', session('report_wizard.trazione', [])));
            @endphp
            <h5 class="mb-3 text-primary"><i class="bi bi-graph-up"></i> Parametri Trazione</h5>
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Test N°</label>
                    <input type="text" name="trazione[codice]" class="form-control" value="{{ $trazione['codice'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <input type="text" name="trazione[tipo]" class="form-control" value="{{ $trazione['tipo'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Spessore (a₀)</label>
                    <input type="number" step="0.1" name="trazione[spessore]" class="form-control" value="{{ $trazione['spessore'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Larghezza (b₀)</label>
                    <input type="number" step="0.1" name="trazione[larghezza]" class="form-control" value="{{ $trazione['larghezza'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Area (S₀)</label>
                    <input type="number" step="0.1" name="trazione[area]" class="form-control" value="{{ $trazione['area'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lunghezza (L₀)</label>
                    <input type="number" step="0.1" name="trazione[lunghezza]" class="form-control" value="{{ $trazione['lunghezza'] ?? '' }}">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-2">
                    <label class="form-label">Temperatura</label>
                    <input type="number" step="0.1" name="trazione[temperatura]" class="form-control" value="{{ $trazione['temperatura'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Snervamento (RP0,2%)</label>
                    <input type="number" step="0.1" name="trazione[snervamento]" class="form-control" value="{{ $trazione['snervamento'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Resistenza (Rm)</label>
                    <input type="number" step="0.1" name="trazione[resistenza]" class="form-control" value="{{ $trazione['resistenza'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Allungamento (A%)</label>
                    <input type="number" step="0.1" name="trazione[allungamento]" class="form-control" value="{{ $trazione['allungamento'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Strizione (Z%)</label>
                    <input type="number" step="0.1" name="trazione[strizione]" class="form-control" value="{{ $trazione['strizione'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Note</label>
                    <input type="text" name="trazione[note]" class="form-control" value="{{ $trazione['note'] ?? '' }}">
                </div>
            </div>
        @endif
        {{-- ================= CHIMICA ================= --}}
        @if($tipo === 'chimica')
            @php
                $chimica = isset($editMode) && $editMode
                    ? (old('chimica', $report->dati['chimica'] ?? []))
                    : (old('chimica', session('report_wizard.chimica', [])));
            @endphp
            <h5 class="mb-3 text-primary"><i class="bi bi-beaker"></i> Parametri Analisi Chimica</h5>
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Provino N°</label>
                    <input type="text" name="chimica[codice]" class="form-control" value="{{ $chimica['codice'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Temperatura</label>
                    <input type="number" step="0.1" name="chimica[temperatura]" class="form-control" value="{{ $chimica['temperatura'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">C</label>
                    <input type="number" step="0.001" name="chimica[C]" class="form-control" value="{{ $chimica['C'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Si</label>
                    <input type="number" step="0.001" name="chimica[Si]" class="form-control" value="{{ $chimica['Si'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mn</label>
                    <input type="number" step="0.001" name="chimica[Mn]" class="form-control" value="{{ $chimica['Mn'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">P</label>
                    <input type="number" step="0.001" name="chimica[P]" class="form-control" value="{{ $chimica['P'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">S</label>
                    <input type="number" step="0.001" name="chimica[S]" class="form-control" value="{{ $chimica['S'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cr</label>
                    <input type="number" step="0.001" name="chimica[Cr]" class="form-control" value="{{ $chimica['Cr'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mo</label>
                    <input type="number" step="0.001" name="chimica[Mo]" class="form-control" value="{{ $chimica['Mo'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ni</label>
                    <input type="number" step="0.001" name="chimica[Ni]" class="form-control" value="{{ $chimica['Ni'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cu</label>
                    <input type="number" step="0.001" name="chimica[Cu]" class="form-control" value="{{ $chimica['Cu'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Al</label>
                    <input type="number" step="0.001" name="chimica[Al]" class="form-control" value="{{ $chimica['Al'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nb</label>
                    <input type="number" step="0.001" name="chimica[Nb]" class="form-control" value="{{ $chimica['Nb'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ti</label>
                    <input type="number" step="0.001" name="chimica[Ti]" class="form-control" value="{{ $chimica['Ti'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">V</label>
                    <input type="number" step="0.001" name="chimica[V]" class="form-control" value="{{ $chimica['V'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ceq</label>
                    <input type="number" step="0.001" name="chimica[Ceq]" class="form-control" value="{{ $chimica['Ceq'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Note</label>
                    <input type="text" name="chimica[note]" class="form-control" value="{{ $chimica['note'] ?? '' }}">
                </div>
            </div>
        @endif
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ isset($editMode) && $editMode ? route('reports.editwizard.step2', $report) : route('reports.wizard.step2') }}" class="btn btn-outline-secondary">Indietro</a>
            <button type="submit" class="btn btn-primary">Salva</button>
        </div>
    </form>
@endsection
