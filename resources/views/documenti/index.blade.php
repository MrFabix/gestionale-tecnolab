@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-folder2-open"></i> Documenti</h1>
        <a href="{{ route('documenti.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuovo documento
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($documenti->isEmpty())
        <div class="alert alert-info">Nessun documento ancora. Creane uno!</div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titolo</th>
                            <th>Categoria</th>
                            <th>Stato</th>
                            <th>Ultima revisione</th>
                            <th>Data</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($documenti as $doc)
                        @php $rev = $doc->ultimaRevisione; @endphp
                        <tr>
                            <td>
                                <a href="{{ route('documenti.show', $doc) }}" class="fw-semibold text-decoration-none">
                                    {{ $doc->titolo }}
                                </a>
                                @if($doc->descrizione)
                                    <br><small class="text-muted">{{ Str::limit($doc->descrizione, 60) }}</small>
                                @endif
                            </td>
                            <td>{{ $doc->categoria ?? '—' }}</td>
                            <td>
                                @php
                                    $badge = match($doc->stato) {
                                        'attivo'   => 'bg-success',
                                        'obsoleto' => 'bg-secondary',
                                        default    => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ ucfirst($doc->stato) }}</span>
                            </td>
                            <td>
                                @if($rev)
                                    <span class="badge bg-info text-dark">Rev. {{ $rev->numero_revisione }}</span>
                                    {{ $rev->redatto_da ? '— ' . $rev->redatto_da : '' }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $rev?->data_revisione?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('documenti.show', $doc) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('documenti.edit', $doc) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('documenti.destroy', $doc) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Eliminare il documento e tutte le sue revisioni?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
