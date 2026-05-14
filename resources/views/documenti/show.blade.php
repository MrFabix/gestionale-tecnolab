@extends('layouts.app')
@section('content')

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('documenti.index') }}"><i class="bi bi-folder2-open"></i> Documenti</a>
            </li>
            @if($documento->cartella)
                @foreach($documento->cartella->breadcrumb() as $c)
                    <li class="breadcrumb-item">
                        <a href="{{ route('cartelle.show', $c) }}">{{ $c->nome }}</a>
                    </li>
                @endforeach
            @endif
            <li class="breadcrumb-item active">{{ $documento->titolo }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-file-earmark-text"></i> {{ $documento->titolo }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('documenti.edit', $documento) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Modifica
            </a>
            <form action="{{ route('documenti.destroy', $documento) }}" method="POST"
                  data-confirm="Eliminare il documento e tutte le sue revisioni?">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Elimina</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    {{-- Scheda --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-2">
                    <span class="text-muted small">Stato</span>
                    <div>
                        @php $badge = match($documento->stato) { 'attivo' => 'bg-success', 'obsoleto' => 'bg-secondary', default => 'bg-warning text-dark' }; @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($documento->stato) }}</span>
                    </div>
                </div>
                @if($documento->descrizione)
                <div class="col-md-10">
                    <span class="text-muted small">Descrizione</span>
                    <div>{{ $documento->descrizione }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Storico revisioni --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary bg-opacity-10">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Storico revisioni</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#formRevisione">
                <i class="bi bi-plus"></i> Nuova revisione
            </button>
        </div>

        <div class="collapse" id="formRevisione">
            <div class="card-body border-bottom bg-light">
                <form action="{{ route('documenti.revisioni.store', $documento) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">N° Rev. *</label>
                            <input type="number" name="numero_revisione" class="form-control"
                                   value="{{ ($documento->revisioni->first()?->numero_revisione ?? -1) + 1 }}" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Data *</label>
                            <input type="date" name="data_revisione" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Redatto da</label>
                            <input type="text" name="redatto_da" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">File</label>
                            <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Motivo revisione</label>
                            <input type="text" name="motivo" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="1"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Salva revisione</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Rev.</th><th>Data</th><th>Redatto da</th><th>Motivo</th><th>Note</th><th>File</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($documento->revisioni as $rev)
                    <tr {{ $loop->first ? 'class=table-primary' : '' }}>
                        <td>
                            <span class="badge bg-primary">Rev. {{ $rev->numero_revisione }}</span>
                            @if($loop->first)<span class="badge bg-success ms-1">Corrente</span>@endif
                        </td>
                        <td>{{ $rev->data_revisione->format('d/m/Y') }}</td>
                        <td>{{ $rev->redatto_da ?? '—' }}</td>
                        <td>{{ $rev->motivo ?? '—' }}</td>
                        <td><small>{{ $rev->note ?? '—' }}</small></td>
                        <td>
                            @php $media = $rev->getFirstMedia('file'); @endphp
                            @if($media)
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary"
                                            onclick="openPreview('{{ $media->getUrl() }}','{{ $media->mime_type }}','{{ addslashes($documento->titolo) }} — Rev. {{ $rev->numero_revisione }}')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            @else —
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('documenti.revisioni.destroy', [$documento, $rev]) }}"
                                  method="POST" data-confirm="Eliminare questa revisione?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Nessuna revisione.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Drawer anteprima --}}
<div id="drawerBackdrop" onclick="closePreview()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1040;"></div>

<div id="drawerPreview"
     style="position:fixed;top:0;right:0;bottom:0;width:min(720px,90vw);background:#fff;
            box-shadow:-4px 0 24px rgba(0,0,0,.18);z-index:1050;display:flex;flex-direction:column;
            transform:translateX(110%);transition:transform .3s cubic-bezier(.4,0,.2,1);border-radius:16px 0 0 16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem 1.25rem;
                background:#0d6efd;color:#fff;font-weight:600;border-radius:16px 0 0 0;flex-shrink:0;">
        <span id="previewTitle"><i class="bi bi-eye me-2"></i>Anteprima</span>
        <div class="d-flex gap-2 align-items-center">
            <a id="previewDownload" href="#" target="_blank" class="btn btn-sm btn-light"><i class="bi bi-download"></i></a>
            <button class="btn-close btn-close-white" onclick="closePreview()"></button>
        </div>
    </div>
    <div id="previewContent" style="flex:1;overflow:hidden;display:flex;align-items:center;justify-content:center;"></div>
</div>

@section('scripts')
<script>
function openPreview(url, mime, titolo) {
    document.getElementById('previewTitle').innerHTML = '<i class="bi bi-eye me-2"></i>' + titolo;
    document.getElementById('previewDownload').href = url;
    const box = document.getElementById('previewContent');
    if (mime.startsWith('image/')) {
        box.innerHTML = `<img src="${url}" style="max-width:100%;max-height:100%;object-fit:contain;padding:1rem;">`;
    } else if (mime === 'application/pdf') {
        box.innerHTML = `<iframe src="${url}" style="width:100%;height:100%;border:none;"></iframe>`;
    } else {
        box.innerHTML = `<div class="text-center text-muted p-4">
            <i class="bi bi-file-earmark-text" style="font-size:3rem;"></i>
            <p class="mt-2">Anteprima non disponibile per questo tipo di file.</p>
            <a href="${url}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-download me-1"></i>Scarica</a>
        </div>`;
    }
    document.getElementById('drawerBackdrop').style.display = 'block';
    requestAnimationFrame(() => document.getElementById('drawerPreview').style.transform = 'translateX(0)');
}
function closePreview() {
    document.getElementById('drawerPreview').style.transform = 'translateX(110%)';
    document.getElementById('drawerBackdrop').style.display = 'none';
    document.getElementById('previewContent').innerHTML = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePreview(); });
</script>
@endsection

@endsection
