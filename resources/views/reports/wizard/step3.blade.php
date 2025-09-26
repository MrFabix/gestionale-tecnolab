@extends('layouts.app')

@section('content')
    <h2>Nuovo Report — Step 3: Dati Prova ({{ ucfirst($tipo) }})</h2>

    <form method="POST" action="{{ route('reports.wizard.step3.post') }}" class="card p-4 shadow-sm">
        @csrf

        @if($tipo === 'resilienza')
            <h5 class="mb-3 text-primary"><i class="bi bi-bar-chart"></i> Parametri Resilienza</h5>
            <div class="row g-4">
                @for($i = 0; $i < 3; $i++)
                    <div class="col-md-12">
                        <div class="card border-primary shadow-sm mb-4">
                            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-hexagon-fill me-2"></i>Provino <span class="badge bg-light text-primary">{{ $i+1 }}</span></span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Provino N</label>
                                        <input type="text" name="provini[{{ $i }}][codice]" class="form-control" placeholder="Codice">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Tipo</label>
                                        <input type="text"  name="provini[{{ $i }}][tipo]" class="form-control" placeholder="Tipo">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Direzione</label>
                                        <input type="text"  name="provini[{{ $i }}][direzione]" class="form-control" placeholder="Direzione">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Spessore (mm)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][spessore_mm]" class="form-control" placeholder="Spessore">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Larghezza (mm)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][larghezza_mm]" class="form-control" placeholder="Larghezza">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Lunghezza (mm)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][lunghezza_mm]" class="form-control" placeholder="Lunghezza">
                                    </div>
                                </div>
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label">Temperatura (°C)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][temperatura_C]" class="form-control" placeholder="Temperatura">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Energia (J)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][energia_J]" class="form-control" placeholder="Joules">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Media (J)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][media_J]" class="form-control" placeholder="Media">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Area duttile (%)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][area_duttile_percent]" class="form-control" placeholder="%">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Espansione laterale (mm)</label>
                                        <input type="number" step="0.1" name="provini[{{ $i }}][espansione_laterale_mm]" class="form-control" placeholder="mm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
            <div class="mb-3">
                <label class="form-label">Note generali</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Eventuali note"></textarea>
            </div>
        @endif


        @if($tipo === 'trazione')
            <h5 class="mb-3 text-primary"><i class="bi bi-bar-chart"></i> Parametri Trazione</h5>
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card border-success shadow-sm mb-4">
                        <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-hexagon me-2"></i>Provino <span class="badge bg-light text-success">1</span></span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Provino N°</label>
                                    <input type="text" name="trazione[codice]" class="form-control" placeholder="Codice">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tipo</label>
                                    <input type="text" name="trazione[tipo]" class="form-control" placeholder="Tipo">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Spessore (mm)</label>
                                    <input type="number" step="0.1" name="trazione[spessore]" class="form-control" placeholder="Spessore">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Larghezza (mm)</label>
                                    <input type="number" step="0.1" name="trazione[larghezza]" class="form-control" placeholder="Larghezza">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Area (mm²)</label>
                                    <input type="number" step="0.01" name="trazione[area]" class="form-control" placeholder="Area">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Lunghezza (mm)</label>
                                    <input type="number" step="0.1" name="trazione[lunghezza]" class="form-control" placeholder="Lunghezza">
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Temperatura (°C)</label>
                                    <input type="number" step="0.1" name="trazione[temperatura]" class="form-control" placeholder="Temperatura">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Snervamento(RP 0,2%) (MPa)</label>
                                    <input type="number" step="0.1" name="trazione[snervamento]" class="form-control" placeholder="Snervamento">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Resistenza (MPa)</label>
                                    <input type="number" step="0.1" name="trazione[resistenza]" class="form-control" placeholder="Resistenza">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Allungamento(A%) (%)</label>
                                    <input type="number" step="0.1" name="trazione[allungamento]" class="form-control" placeholder="Allungamento">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Strizione (%)</label>
                                    <input type="number" step="0.1" name="trazione[strizione]" class="form-control" placeholder="Strizione">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Note generali</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Eventuali note"></textarea>
            </div>
        @endif

        @if($tipo === 'chimica')
            <h5 class="mb-3 text-info"><i class="bi bi-flask"></i> Parametri Chimica</h5>
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card border-info shadow-sm mb-4">
                        <div class="card-header bg-info text-white d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-hexagon me-2"></i>Provino <span class="badge bg-light text-info">1</span></span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Provino N°</label>
                                    <input type="text" name="chimica[codice]" class="form-control" placeholder="Codice">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Temperatura (°C)</label>
                                    <input type="number" step="0.1" name="chimica[temperatura]" class="form-control" placeholder="Temperatura">
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-2"><label class="form-label">C (%)</label><input type="number" step="0.001" name="chimica[C]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Si (%)</label><input type="number" step="0.001" name="chimica[Si]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Mn (%)</label><input type="number" step="0.001" name="chimica[Mn]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">P (%)</label><input type="number" step="0.001" name="chimica[P]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">S (%)</label><input type="number" step="0.001" name="chimica[S]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Cr (%)</label><input type="number" step="0.001" name="chimica[Cr]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Mo (%)</label><input type="number" step="0.001" name="chimica[Mo]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Ni (%)</label><input type="number" step="0.001" name="chimica[Ni]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Cu (%)</label><input type="number" step="0.001" name="chimica[Cu]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Al (%)</label><input type="number" step="0.001" name="chimica[Al]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Nb (%)</label><input type="number" step="0.001" name="chimica[Nb]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Ti (%)</label><input type="number" step="0.001" name="chimica[Ti]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">V (%)</label><input type="number" step="0.001" name="chimica[V]" class="form-control"></div>
                                <div class="col-md-2"><label class="form-label">Ceq (%)</label><input type="number" step="0.001" name="chimica[Ceq]" class="form-control"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Note generali</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Eventuali note"></textarea>
            </div>
        @endif

        <div class="d-flex justify-content-between">
            <a href="{{ route('reports.wizard.step2') }}" class="btn btn-secondary">Indietro</a>
            <button type="submit" class="btn btn-success">Conferma</button>
        </div>
    </form>
@endsection
