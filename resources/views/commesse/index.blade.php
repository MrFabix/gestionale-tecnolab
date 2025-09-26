@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Commesse</h1>
        <a href="{{ route('commesse.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuova Commessa
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
                    <th>ID</th>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th>Cliente</th>
                    <th>Stato</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($commesse as $commessa)
                    <tr>
                        <td>{{ $commessa->id }}</td>
                        <td>{{ $commessa->codice }}</td>
                        <td>{{ $commessa->descrizione }}</td>
                        <td>{{ $commessa->cliente->ragione_sociale }}</td>
                        <td>
                            <span class="badge
                                @if($commessa->stato=='aperta') bg-success
                                @elseif($commessa->stato=='in_lavorazione') bg-warning
                                @else bg-secondary @endif">
                                {{ ucfirst($commessa->stato) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('commesse.show', $commessa->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('commesse.edit', $commessa->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('commesse.destroy', $commessa->id) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Nessuna commessa presente.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
