@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Report</h1>
        <a href="{{ route('reports.wizard.step1') }}" class="btn btn-primary">
            <i class="bi bi-magic"></i> Nuovo Report (Wizard)
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- FILTRI -->
    <form method="GET" action="" class="card mb-4 shadow-sm">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-2">
                <label for="rapporto_numero" class="form-label">Rapporto N°</label>
                <input type="text" name="rapporto_numero" id="rapporto_numero" class="form-control" value="{{ request('rapporto_numero') }}">
            </div>
            <div class="col-md-2">
                <label for="commessa_id" class="form-label">Commessa</label>
                <select name="commessa_id" id="commessa_id" class="form-select">
                    <option value="">Tutte</option>
                    @isset($commesse)
                        @foreach($commesse as $commessa)
                            <option value="{{ $commessa->id }}" {{ request('commessa_id') == $commessa->id ? 'selected' : '' }}>{{ $commessa->codice }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="col-md-2">
                <label for="cliente_id" class="form-label">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-select">
                    <option value="">Tutti</option>
                    @isset($clienti)
                        @foreach($clienti as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->ragione_sociale }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="col-md-2">
                <label for="tipo_prova" class="form-label">Tipo Prova</label>
                <select name="tipo_prova" id="tipo_prova" class="form-select">
                    <option value="">Tutte</option>
                    @isset($tipi_prova)
                        @foreach($tipi_prova as $tipo)
                            <option value="{{ $tipo }}" {{ request('tipo_prova') == $tipo ? 'selected' : '' }}>{{ ucfirst($tipo) }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="col-md-2">
                <label for="stato" class="form-label">Stato</label>
                <select name="stato" id="stato" class="form-select">
                    <option value="">Tutti</option>
                    <option value="bozza" {{ request('stato') == 'bozza' ? 'selected' : '' }}>Bozza</option>
                    <option value="completo" {{ request('stato') == 'completo' ? 'selected' : '' }}>Completo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filtra</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Rapporto N°</th>
                    <th>Commessa</th>
                    <th>Cliente</th>
                    <th>Tipo Prova</th>
                    <th>Stato</th>
                    <th>Creato il</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($reports as $r)
                    <tr>
                        <td>{{ $r->rapporto_numero }}</td>
                        <td>{{ $r->commessa->codice ?? '-' }}</td>
                        <td>{{ $r->commessa->cliente->ragione_sociale ?? '-' }}</td>
                        <td>{{ ucfirst($r->tipo_prova) }}</td>
                        <td>
                            @if($r->stato === 'bozza')
                                <span class="badge bg-secondary">Bozza</span>
                            @elseif($r->stato === 'completo')
                                <span class="badge bg-success">Completo</span>
                            @else
                                {{ $r->stato }}
                            @endif
                        </td>
                                               <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('reports.editwizard.step1', $r) }}" class="btn btn-sm btn-warning" title="Modifica (Wizard)">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <a href="{{ route('reports.show', $r) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('reports.downloadPdf', $r) }}" class="btn btn-sm btn-success" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <form action="{{ route('reports.destroy', $r) }}" method="POST" class="d-inline delete-report-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Nessun report presente.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-report-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Sei sicuro?',
                    text: 'Eliminare questo report?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sì, elimina',
                    cancelButtonText: 'Annulla'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
