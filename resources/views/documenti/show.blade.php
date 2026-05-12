@extends('layouts.app')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-file-earmark-text"></i> {{ $documento->titolo }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('documenti.edit', $documento) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Modifica
            </a>
            <a href="{{ route('documenti.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Elenco
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Scheda documento --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <span class="text-muted small">Categoria</span>
                    <div class="fw-semibold">{{ $documento->categoria ?? '—' }}</div>
                </div>
                <div class="col-md-2">
                    <span class="text-muted small">Stato</span>
                    <div>
                        @php
                            $badge = match($documento->stato) {
                                'attivo'   => 'bg-success',
                                'obsoleto' => 'bg-secondary',
                                default    => 'bg-warning text-dark',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($documento->stato) }}</span>
                    </div>
                </div>
                @if($documento->descrizione)
                <div class="col-md-7">
                    <span class="text-muted small">Descrizione</span>
                    <div>{{ $documento->descrizione }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sezione revisioni --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary bg-opacity-10">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Storico revisioni</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#formRevisione">
                <i class="bi bi-plus"></i> Nuova revisione
            </button>
        </div>

        {{-- Form nuova revisione --}}
        <div class="collapse" id="formRevisione">
            <div class="card-body border-bottom bg-light">
                <form action="{{ route('documenti.revisioni.store', $documento) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Data revisione *</label>
                            <input type="date" name="data_revisione" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Redatto da</label>
                            <input type="text" name="redatto_da" class="form-control" placeholder="Nome / ufficio">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Motivo revisione</label>
                            <input type="text" name="motivo" class="form-control" placeholder="Es. Aggiornamento normativa…">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">File (PDF, Word, immagine)</label>
                            <input type="file" name="file" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salva revisione
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabella revisioni --}}
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rev.</th>
                        <th>Data</th>
                        <th>Redatto da</th>
                        <th>Motivo</th>
                        <th>Note</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($documento->revisioni as $rev)
                    <tr {{ $loop->first ? 'class=table-primary' : '' }}>
                        <td>
                            <span class="badge bg-primary">Rev. {{ $rev->numero_revisione }}</span>
                            @if($loop->first)
                                <span class="badge bg-success ms-1">Corrente</span>
                            @endif
                        </td>
                        <td>{{ $rev->data_revisione->format('d/m/Y') }}</td>
                        <td>{{ $rev->redatto_da ?? '—' }}</td>
                        <td>{{ $rev->motivo ?? '—' }}</td>
                        <td><small>{{ $rev->note ?? '—' }}</small></td>
                        <td>
                            @if($rev->getFirstMedia('file'))
                                <a href="{{ $rev->getFirstMediaUrl('file') }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Scarica
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('documenti.revisioni.destroy', [$documento, $rev]) }}"
                                  method="POST" onsubmit="return confirm('Eliminare questa revisione?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">Nessuna revisione ancora.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
