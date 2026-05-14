{{-- Toolbar --}}
<div class="d-flex gap-2 mb-3">
    <button class="btn btn-outline-secondary btn-sm" onclick="openDrawer('drawerCartella')">
        <i class="bi bi-folder-plus"></i> Nuova cartella
    </button>
    <button class="btn btn-primary btn-sm" onclick="openDrawer('drawerDocumento')">
        <i class="bi bi-file-earmark-plus"></i> Nuovo documento
    </button>
</div>

{{-- Sottocartelle --}}
@if($cartelle->count())
    <div class="row g-2 mb-3">
        @foreach($cartelle as $c)
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card h-100 shadow-sm border-0 bg-light">
                    <a href="{{ route('cartelle.show', $c) }}" class="card-body text-center text-decoration-none text-dark py-3">
                        <i class="bi bi-folder-fill text-warning" style="font-size:2rem;"></i>
                        <div class="small fw-semibold mt-1 text-truncate">{{ $c->nome }}</div>
                    </a>
                    <div class="card-footer p-1 text-end border-0 bg-light">
                        <button class="btn btn-link text-muted p-0 me-1"
                                onclick="openDrawerRinomina('{{ route('cartelle.update', $c) }}', '{{ addslashes($c->nome) }}')">
                            <i class="bi bi-pencil" style="font-size:.75rem;"></i>
                        </button>
                        <form action="{{ route('cartelle.destroy', $c) }}" method="POST" class="d-inline"
                              data-confirm="Eliminare la cartella e tutto il suo contenuto?">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0">
                                <i class="bi bi-trash" style="font-size:.75rem;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- File --}}
@if($documenti->count())
    <div class="card shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nome</th><th>Stato</th><th>Rev.</th><th>Data</th><th></th></tr>
            </thead>
            <tbody>
            @foreach($documenti as $doc)
                @php $rev = $doc->ultimaRevisione; @endphp
                <tr>
                    <td>
                        <a href="{{ route('documenti.show', $doc) }}" class="text-decoration-none fw-semibold">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i>{{ $doc->titolo }}
                        </a>
                        @if($doc->descrizione)
                            <br><small class="text-muted">{{ Str::limit($doc->descrizione, 60) }}</small>
                        @endif
                    </td>
                    <td>
                        @php $badge = match($doc->stato) { 'attivo' => 'bg-success', 'obsoleto' => 'bg-secondary', default => 'bg-warning text-dark' }; @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($doc->stato) }}</span>
                    </td>
                    <td>@if($rev)<span class="badge bg-info text-dark">Rev. {{ $rev->numero_revisione }}</span>@else —@endif</td>
                    <td>{{ $rev?->data_revisione?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-end">
                        @php $media = $doc->ultimaRevisione?->getFirstMedia('file'); @endphp
                        @if($media)
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="openPreview('{{ $media->getUrl() }}','{{ $media->mime_type }}','{{ $doc->titolo }}')">
                                <i class="bi bi-eye"></i>
                            </button>
                        @endif
                        <a href="{{ route('documenti.show', $doc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-right"></i></a>
                        <form action="{{ route('documenti.destroy', $doc) }}" method="POST" class="d-inline"
                              data-confirm="Eliminare il documento e tutte le revisioni?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@elseif($cartelle->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-folder2-open" style="font-size:3rem;"></i>
        <p class="mt-2">Cartella vuota</p>
    </div>
@endif

{{-- ═══════════════════════════════════════════════
     DRAWER OVERLAY
════════════════════════════════════════════════ --}}
<div id="drawerBackdrop" onclick="closeAllDrawers()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1040;transition:opacity .25s;"></div>

{{-- Drawer: Nuova cartella --}}
<div id="drawerCartella" class="doc-drawer">
    <div class="doc-drawer-header">
        <span><i class="bi bi-folder-plus me-2"></i>Nuova cartella</span>
        <button class="btn-close btn-close-white" onclick="closeAllDrawers()"></button>
    </div>
    <div class="doc-drawer-body">
        <form action="{{ route('cartelle.store') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $parentId ?? '' }}">
            <div class="mb-3">
                <label class="form-label">Nome cartella *</label>
                <input type="text" name="nome" class="form-control form-control-lg" placeholder="Es. Procedure operative" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-lg me-1"></i> Crea cartella
            </button>
        </form>
    </div>
</div>

{{-- Drawer: Nuovo documento --}}
<div id="drawerDocumento" class="doc-drawer" style="width:480px;">
    <div class="doc-drawer-header">
        <span><i class="bi bi-file-earmark-plus me-2"></i>Nuovo documento</span>
        <button class="btn-close btn-close-white" onclick="closeAllDrawers()"></button>
    </div>
    <div class="doc-drawer-body">
        <form action="{{ route('documenti.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="cartella_id" value="{{ $parentId ?? '' }}">

            <div class="mb-3">
                <label class="form-label">Titolo *</label>
                <input type="text" name="titolo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descrizione</label>
                <textarea name="descrizione" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Stato</label>
                <select name="stato" class="form-select">
                    <option value="bozza">Bozza</option>
                    <option value="attivo" selected>Attivo</option>
                    <option value="obsoleto">Obsoleto</option>
                </select>
            </div>

            <hr>
            <p class="text-muted small fw-semibold mb-2">Prima revisione</p>

            <div class="row g-2 mb-2">
                <div class="col-4">
                    <label class="form-label">N° Rev. *</label>
                    <input type="number" name="numero_revisione" class="form-control" value="0" min="0" required>
                </div>
                <div class="col-8">
                    <label class="form-label">Data *</label>
                    <input type="date" name="data_revisione" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Redatto da</label>
                <input type="text" name="redatto_da" class="form-control" placeholder="Nome / ufficio">
            </div>
            <div class="mb-2">
                <label class="form-label">Motivo</label>
                <input type="text" name="motivo" class="form-control" placeholder="Prima emissione…">
            </div>
            <div class="mb-3">
                <label class="form-label">File (PDF, Word, immagine)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save me-1"></i> Crea documento
            </button>
        </form>
    </div>
</div>

{{-- Drawer: Rinomina cartella --}}
<div id="drawerRinomina" class="doc-drawer">
    <div class="doc-drawer-header">
        <span><i class="bi bi-pencil me-2"></i>Rinomina cartella</span>
        <button class="btn-close btn-close-white" onclick="closeAllDrawers()"></button>
    </div>
    <div class="doc-drawer-body">
        <form id="formRinomina" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nuovo nome *</label>
                <input type="text" name="nome" id="rinominaNome" class="form-control form-control-lg" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-lg me-1"></i> Salva
            </button>
        </form>
    </div>
</div>

{{-- Drawer: Anteprima file --}}
<div id="drawerPreview" class="doc-drawer" style="width:min(720px,90vw);">
    <div class="doc-drawer-header">
        <span id="previewTitle"><i class="bi bi-eye me-2"></i>Anteprima</span>
        <div class="d-flex gap-2 align-items-center">
            <a id="previewDownload" href="#" target="_blank" class="btn btn-sm btn-light">
                <i class="bi bi-download"></i>
            </a>
            <button class="btn-close btn-close-white" onclick="closeAllDrawers()"></button>
        </div>
    </div>
    <div class="doc-drawer-body p-0" style="overflow:hidden;">
        <div id="previewContent" style="height:100%;display:flex;align-items:center;justify-content:center;"></div>
    </div>
</div>

{{-- ═══ STILI + JS ═══ --}}
<style>
.doc-drawer {
    position: fixed;
    top: 0; right: 0; bottom: 0;
    width: 360px;
    background: #fff;
    box-shadow: -4px 0 24px rgba(0,0,0,.18);
    z-index: 1050;
    display: flex;
    flex-direction: column;
    transform: translateX(110%);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
    border-radius: 16px 0 0 16px;
}
.doc-drawer.open { transform: translateX(0); }
.doc-drawer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.1rem 1.25rem;
    background: #0d6efd;
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 16px 0 0 0;
    flex-shrink: 0;
}
.doc-drawer-body {
    padding: 1.5rem 1.25rem;
    overflow-y: auto;
    flex: 1;
}
</style>

@section('scripts')
<script>
function openDrawer(id) {
    closeAllDrawers(false);
    document.getElementById('drawerBackdrop').style.display = 'block';
    requestAnimationFrame(() => document.getElementById(id).classList.add('open'));
}
function openDrawerRinomina(url, nome) {
    document.getElementById('formRinomina').action = url;
    document.getElementById('rinominaNome').value  = nome;
    openDrawer('drawerRinomina');
}
function openPreview(url, mime, titolo) {
    openDrawer('drawerPreview'); // apre e chiude gli altri drawer
    // imposta contenuto DOPO openDrawer (che chiama closeAllDrawers(false) senza svuotare)
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
}
function closeAllDrawers(hideBackdrop = true) {
    document.querySelectorAll('.doc-drawer').forEach(d => d.classList.remove('open'));
    if (hideBackdrop) {
        document.getElementById('drawerBackdrop').style.display = 'none';
        // svuota iframe solo alla chiusura definitiva
        const box = document.getElementById('previewContent');
        if (box) box.innerHTML = '';
    }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllDrawers(); });
</script>
@endsection
