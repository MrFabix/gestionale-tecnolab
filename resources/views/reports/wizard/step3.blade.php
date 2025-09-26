@extends('layouts.app')

@section('content')
    <h2>Nuovo Report — Step 3: Dati Prova ({{ ucfirst($tipo) }})</h2>

    <form method="POST" action="{{ route('reports.wizard.step3.post') }}" class="card p-4 shadow-sm">
        @csrf

        {{-- ================= RESILIENZA ================= --}}
        @if($tipo === 'resilienza')
            <h5 class="mb-3 text-primary"><i class="bi bi-bar-chart"></i> Parametri Resilienza (UNI EN ISO 148-1:2018)</h5>
            <div class="row g-4">
                @for($i = 0; $i < 3; $i++)
                    <div class="col-md-12">
                        <div class="card border-primary shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-hexagon-fill me-2"></i>Provino N° {{ $i+1 }}
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Test N°</label>
                                        <input type="text" name="provini[{{ $i }}][codice]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tipo</label>
                                        <input type="text" name="provini[{{ $i }}][tipo]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Direzione</label>
                                        <input type="text" name="provini[{{ $i }}][direzione]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Spessore (B) mm</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][spessore_mm]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Larghezza (W) mm</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][larghezza_mm]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Lunghezza (L) mm</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][lunghezza_mm]" class="form-control">
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Temperatura (°C)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][temperatura_C]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Valore (J)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][energia_J]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Media (J)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][media_J]" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Area duttile (%)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][area_duttile_percent]" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Espansione laterale (mm)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][espansione_laterale_mm]" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="mb-3">
                <label class="form-label">Note generali</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
            </div>
        @endif

        {{-- ================= TRAZIONE ================= --}}
        @if($tipo === 'trazione')
            <h5 class="mb-3 text-success"><i class="bi bi-bar-chart"></i> Parametri Trazione (UNI EN ISO 6892-1:2019)</h5>
            <div class="card border-success shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-hexagon me-2"></i>Provino
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Test N°</label>
                            <input type="text" name="trazione[codice]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <input type="text" name="trazione[tipo]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Spessore (a₀) mm</label>
                            <input type="number" step="0.1" name="trazione[spessore]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Larghezza (b₀) mm</label>
                            <input type="number" step="0.1" name="trazione[larghezza]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Area (S₀) mm²</label>
                            <input type="number" step="0.01" name="trazione[area]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Lunghezza (L₀) mm</label>
                            <input type="number" step="0.1" name="trazione[lunghezza]" class="form-control">
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Temperatura (°C)</label>
                            <input type="number" step="0.1" name="trazione[temperatura]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Snervamento (RP0,2%) MPa</label>
                            <input type="number" step="0.1" name="trazione[snervamento]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Resistenza (Rm) MPa</label>
                            <input type="number" step="0.1" name="trazione[resistenza]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Allungamento (A%)</label>
                            <input type="number" step="0.1" name="trazione[allungamento]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Strizione (Z%)</label>
                            <input type="number" step="0.1" name="trazione[strizione]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Note generali</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
            </div>
        @endif

        {{-- ================= CHIMICA ================= --}}
        @if($tipo === 'chimica')
            @php
                $medie = session('report_wizard.medie_chimica', []);
            @endphp
            <h5 class="mb-3 text-info"><i class="bi bi-flask"></i> Parametri Analisi Chimica (ASTM E415-21)</h5>
            <div class="card border-info shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-hexagon me-2"></i>Provino
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Provino N°</label>
                            <input type="text" name="chimica[codice]" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Temperatura (°C)</label>
                            <input type="number" step="0.1" name="chimica[temperatura]" class="form-control">
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        @foreach (['C','Si','Mn','P','S','Cr','Mo','Ni','Cu','Al','Nb','Ti','V','Ceq'] as $el)
                            <div class="col-md-2">
                                <label class="form-label">{{ $el }} %</label>
                                <input type="number" step="0.001" name="chimica[{{ $el }}]" class="form-control"
                                       value="{{ old('chimica.'.$el, $medie[$el] ?? '') }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Note generali</label>
                <textarea name="note" class="form-control" rows="3"></textarea>
            </div>
        @endif

        <div class="d-flex justify-content-between">
            <a href="{{ route('reports.wizard.step2') }}" class="btn btn-secondary">Indietro</a>
            <button type="submit" class="btn btn-success">Conferma</button>
        </div>
    </form>
@endsection
