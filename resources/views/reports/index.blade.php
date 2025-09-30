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

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Rapporto N°</th>
                    <th>Commessa</th>
                    <th>Cliente</th>
                    <th>Tipo Prova</th>
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
                        <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-nowrap">
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
                    <tr><td colspan="6" class="text-center">Nessun report presente.</td></tr>
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
