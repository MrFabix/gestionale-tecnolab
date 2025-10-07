@extends('layouts.app')

@section('content')
    <h1>Report #{{ $report->id }}</h1>

    @if(session('wizard_cleared'))
        <div class="alert alert-success">Dati del wizard puliti correttamente.</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Commessa</strong><br>{{ $report->commessa->codice }} — {{ $report->commessa->descrizione }}</div>
                <div class="col-md-4"><strong>Cliente</strong><br>{{ $report->commessa->cliente->ragione_sociale ?? '-' }}</div>
                <div class="col-md-4"><strong>Tipo Prova</strong><br>{{ ucfirst($report->tipo_prova) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Dati di prova</div>
        <div class="card-body">
            @php $d = $report->dati ?? []; @endphp

            @if($report->tipo_prova === 'resilienza')
                <h5 class="mb-3 text-primary">Resilienza</h5>
                @if(!empty($d['provini']))
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Provino N°</th>
                                    <th>Tipo</th>
                                    <th>Direzione</th>
                                    <th>Spessore (mm)</th>
                                    <th>Larghezza (mm)</th>
                                    <th>Lunghezza (mm)</th>
                                    <th>Temperatura (°C)</th>
                                    <th>Energia assorbita (J)</th>
                                    <th>Media energia (J)</th>
                                    <th>Area duttile (%)</th>
                                    <th>Area duttile (mm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($d['provini'] as $p)
                                    <tr>
                                        <td>{{ $p['numero'] ?? '-' }}</td>
                                        <td>{{ $p['tipo'] ?? '-' }}</td>
                                        <td>{{ $p['direzione'] ?? '-' }}</td>
                                        <td>{{ $p['spessore_mm'] ?? '-' }}</td>
                                        <td>{{ $p['larghezza'] ?? '-' }}</td>
                                        <td>{{ $p['lunghezza'] ?? '-' }}</td>
                                        <td>{{ $p['temperatura'] ?? ($d['temperatura'] ?? '-') }}</td>
                                        <td>{{ $p['energia_assorbita'] ?? '-' }}</td>
                                        <td>{{ $p['media_energia'] ?? ($d['media_energia'] ?? '-') }}</td>
                                        <td>{{ $p['area_duttile_percent'] ?? ($d['area_duttile_perc'] ?? '-') }}</td>
                                        <td>{{ $p['area_duttile_mm'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif
                <p><strong>Note:</strong> {{ $d['note'] ?? '-' }}</p>

            @elseif($report->tipo_prova === 'trazione')
                <h5 class="mb-3 text-success">Trazione</h5>
                @if(!empty($d['trazione']))
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Provino N°</th>
                                    <th>Tipo</th>
                                    <th>Spessore (mm)</th>
                                    <th>Larghezza (mm)</th>
                                    <th>Area (mm²)</th>
                                    <th>Lunghezza (mm)</th>
                                    <th>Temperatura (°C)</th>
                                    <th>Snervamento (MPa)</th>
                                    <th>Resistenza (MPa)</th>
                                    <th>Allungamento (%)</th>
                                    <th>Strizione (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $d['trazione']['codice'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['tipo'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['spessore'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['larghezza'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['area'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['lunghezza'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['temperatura'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['snervamento'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['resistenza'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['allungamento'] ?? '-' }}</td>
                                    <td>{{ $d['trazione']['strizione'] ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <p><strong>Note:</strong> {{ $d['note'] ?? '-' }}</p>

            @elseif($report->tipo_prova === 'chimica')
                <h5 class="mb-3 text-info">Chimica</h5>
                @if(!empty($d['chimica']))
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Provino N°</th>
                                    <th>Temperatura (°C)</th>
                                    <th>C (%)</th>
                                    <th>Si (%)</th>
                                    <th>Mn (%)</th>
                                    <th>P (%)</th>
                                    <th>S (%)</th>
                                    <th>Cr (%)</th>
                                    <th>Mo (%)</th>
                                    <th>Ni (%)</th>
                                    <th>Cu (%)</th>
                                    <th>Al (%)</th>
                                    <th>Nb (%)</th>
                                    <th>Ti (%)</th>
                                    <th>V (%)</th>
                                    <th>Ceq</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $d['chimica']['codice'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['temperatura'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['C'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Si'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Mn'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['P'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['S'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Cr'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Mo'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Ni'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Cu'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Al'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Nb'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Ti'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['V'] ?? '-' }}</td>
                                    <td>{{ $d['chimica']['Ceq'] ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                <p><strong>Note:</strong> {{ $d['note'] ?? '-' }}</p>

            @else
                <pre class="mb-0">{{ json_encode($d, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            @endif
        </div>
    </div>



    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Indietro</a>
        <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Eliminare questo report?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger"><i class="bi bi-trash"></i> Elimina</button>
        </form>
    </div>
@endsection
