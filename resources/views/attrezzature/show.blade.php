@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-tools"></i> {{ $attrezzatura->nome }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('attrezzature.edit', $attrezzatura) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Modifica
            </a>
            <a href="{{ route('attrezzature.index') }}" class="btn btn-outline-secondary">
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

    {{-- ── SCHEDA ANAGRAFICA ─────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Galleria immagini --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold"><i class="bi bi-images"></i> Foto</div>
                <div class="card-body">
                    @if($immagini->count())
                        {{-- Carosello Bootstrap --}}
                        <div id="carouselFoto" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($immagini as $i => $img)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <img src="{{ $img->getUrl() }}" class="d-block w-100 rounded"
                                             style="height:220px;object-fit:contain;" alt="{{ $img->name }}">
                                    </div>
                                @endforeach
                            </div>
                            @if($immagini->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselFoto" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselFoto" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-camera" style="font-size:3rem;"></i>
                            <p>Nessuna foto caricata</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Dati anagrafici --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold"><i class="bi bi-info-circle"></i> Dati tecnici</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th>Codice</th><td>{{ $attrezzatura->codice ?? '—' }}</td></tr>
                        <tr><th>Marca</th><td>{{ $attrezzatura->marca ?? '—' }}</td></tr>
                        <tr><th>Modello</th><td>{{ $attrezzatura->modello ?? '—' }}</td></tr>
                        <tr><th>Matricola</th><td>{{ $attrezzatura->matricola ?? '—' }}</td></tr>
                        <tr><th>Ubicazione</th><td>{{ $attrezzatura->ubicazione ?? '—' }}</td></tr>
                        <tr>
                            <th>Stato</th>
                            <td>
                            <span class="badge {{ $attrezzatura->stato === 'attivo' ? 'bg-success' : ($attrezzatura->stato === 'in_manutenzione' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $attrezzatura->stato)) }}
                            </span>
                            </td>
                        </tr>
                    </table>
                    @if($attrezzatura->note)
                        <hr><p class="small text-muted mb-0">{{ $attrezzatura->note }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Documenti --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold"><i class="bi bi-file-earmark-text"></i> Documenti</div>
                <div class="card-body">
                    @if($documenti->count())
                        <ul class="list-group list-group-flush">
                            @foreach($documenti as $doc)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <a href="{{ $doc->getUrl() }}" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf text-danger"></i>
                                        {{ $doc->name }}
                                    </a>
                                    <form action="{{ route('attrezzature.media.destroy', [$attrezzatura, $doc->id]) }}" method="POST"
                                          data-confirm="Eliminare?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Nessun documento.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── SEZIONE TARATURA ──────────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-info bg-opacity-10">
            <h5 class="mb-0"><i class="bi bi-patch-check"></i> Taratura</h5>
            <button class="btn btn-sm btn-info" data-bs-toggle="collapse" data-bs-target="#formTaratura">
                <i class="bi bi-plus"></i> Aggiungi taratura
            </button>
        </div>
        <div class="collapse" id="formTaratura">
            <div class="card-body border-bottom bg-light">
                <form action="{{ route('attrezzature.tarature.store', $attrezzatura) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Data taratura *</label>
                            <input type="date" name="data_taratura" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Scadenza</label>
                            <input type="date" name="data_scadenza" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Eseguita da</label>
                            <input type="text" name="eseguita_da" class="form-control" placeholder="Ente / tecnico">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Certificato (PDF/immagine)</label>
                            <input type="file" name="certificato" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-save"></i> Salva taratura
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Data</th><th>Scadenza</th><th>Eseguita da</th>
                    <th>Certificato</th><th>Note</th><th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($attrezzatura->tarature as $t)
                    @php $scaduta = $t->data_scadenza && $t->data_scadenza->isPast(); @endphp
                    <tr class="{{ $scaduta ? 'table-danger' : '' }}">
                        <td>{{ $t->data_taratura->format('d/m/Y') }}</td>
                        <td>
                            @if($t->data_scadenza)
                                {{ $t->data_scadenza->format('d/m/Y') }}
                                @if($scaduta) <span class="badge bg-danger">SCADUTA</span> @endif
                            @else —
                            @endif
                        </td>
                        <td>{{ $t->eseguita_da ?? '—' }}</td>
                        <td>
                            @if($t->getFirstMedia('certificato'))
                                <a href="{{ $t->getFirstMediaUrl('certificato') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Scarica
                                </a>
                            @else —
                            @endif
                        </td>
                        <td>{{ $t->note ?? '—' }}</td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#modalEditTaratura"
                                    data-id="{{ $t->id }}"
                                    data-url="{{ route('attrezzature.tarature.update', [$attrezzatura, $t]) }}"
                                    data-data_taratura="{{ $t->data_taratura->format('Y-m-d') }}"
                                    data-data_scadenza="{{ $t->data_scadenza?->format('Y-m-d') }}"
                                    data-eseguita_da="{{ $t->eseguita_da }}"
                                    data-note="{{ $t->note }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('attrezzature.tarature.destroy', [$attrezzatura, $t]) }}" method="POST"
                                  data-confirm="Eliminare questa taratura?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Nessuna taratura registrata.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── SEZIONE MANUTENZIONE ─────────────────────────── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-warning bg-opacity-10">
            <h5 class="mb-0"><i class="bi bi-wrench-adjustable"></i> Manutenzioni</h5>
            <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#formManutenzione">
                <i class="bi bi-plus"></i> Aggiungi manutenzione
            </button>
        </div>
        <div class="collapse" id="formManutenzione">
            <div class="card-body border-bottom bg-light">
                <form action="{{ route('attrezzature.manutenzioni.store', $attrezzatura) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Data intervento *</label>
                            <input type="date" name="data_intervento" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Prossima scadenza</label>
                            <input type="date" name="prossima_scadenza" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="ordinaria">Ordinaria</option>
                                <option value="straordinaria">Straordinaria</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Eseguita da</label>
                            <input type="text" name="eseguita_da" class="form-control" placeholder="Tecnico / ditta">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Documento allegato</label>
                            <input type="file" name="documento" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrizione intervento</label>
                            <textarea name="descrizione" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save"></i> Salva manutenzione
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Data</th><th>Tipo</th><th>Eseguita da</th>
                    <th>Prossima scadenza</th><th>Documento</th><th>Descrizione</th><th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($attrezzatura->manutenzioni as $m)
                    @php $urgente = $m->prossima_scadenza && $m->prossima_scadenza->isPast(); @endphp
                    <tr class="{{ $urgente ? 'table-warning' : '' }}">
                        <td>{{ $m->data_intervento->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $m->tipo === 'ordinaria' ? 'bg-secondary' : 'bg-danger' }}">{{ ucfirst($m->tipo) }}</span></td>
                        <td>{{ $m->eseguita_da ?? '—' }}</td>
                        <td>
                            @if($m->prossima_scadenza)
                                {{ $m->prossima_scadenza->format('d/m/Y') }}
                                @if($urgente) <span class="badge bg-warning text-dark">SCADUTA</span> @endif
                            @else —
                            @endif
                        </td>
                        <td>
                            @if($m->getFirstMedia('documento'))
                                <a href="{{ $m->getFirstMediaUrl('documento') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Scarica
                                </a>
                            @else —
                            @endif
                        </td>
                        <td><small>{{ Str::limit($m->descrizione, 60) ?? '—' }}</small></td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#modalEditManutenzione"
                                    data-url="{{ route('attrezzature.manutenzioni.update', [$attrezzatura, $m]) }}"
                                    data-data_intervento="{{ $m->data_intervento->format('Y-m-d') }}"
                                    data-prossima_scadenza="{{ $m->prossima_scadenza?->format('Y-m-d') }}"
                                    data-tipo="{{ $m->tipo }}"
                                    data-eseguita_da="{{ $m->eseguita_da }}"
                                    data-descrizione="{{ $m->descrizione }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('attrezzature.manutenzioni.destroy', [$attrezzatura, $m]) }}" method="POST"
                                  data-confirm="Eliminare?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Nessuna manutenzione registrata.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- Modal modifica taratura --}}
<div class="modal fade" id="modalEditTaratura" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditTaratura" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Modifica taratura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Data taratura *</label>
                            <input type="date" name="data_taratura" id="et_data_taratura" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Scadenza</label>
                            <input type="date" name="data_scadenza" id="et_data_scadenza" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Eseguita da</label>
                            <input type="text" name="eseguita_da" id="et_eseguita_da" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" id="et_note" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sostituisci certificato (opzionale)</label>
                            <input type="file" name="certificato" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salva</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal modifica manutenzione --}}
<div class="modal fade" id="modalEditManutenzione" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEditManutenzione" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Modifica manutenzione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Data intervento *</label>
                            <input type="date" name="data_intervento" id="em_data_intervento" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prossima scadenza</label>
                            <input type="date" name="prossima_scadenza" id="em_prossima_scadenza" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" id="em_tipo" class="form-select">
                                <option value="ordinaria">Ordinaria</option>
                                <option value="straordinaria">Straordinaria</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Eseguita da</label>
                            <input type="text" name="eseguita_da" id="em_eseguita_da" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrizione intervento</label>
                            <textarea name="descrizione" id="em_descrizione" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sostituisci documento (opzionale)</label>
                            <input type="file" name="documento" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salva</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.getElementById('modalEditTaratura').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('formEditTaratura').action = btn.dataset.url;
    document.getElementById('et_data_taratura').value  = btn.dataset.data_taratura;
    document.getElementById('et_data_scadenza').value  = btn.dataset.data_scadenza ?? '';
    document.getElementById('et_eseguita_da').value    = btn.dataset.eseguita_da ?? '';
    document.getElementById('et_note').value           = btn.dataset.note ?? '';
});

document.getElementById('modalEditManutenzione').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('formEditManutenzione').action    = btn.dataset.url;
    document.getElementById('em_data_intervento').value       = btn.dataset.data_intervento;
    document.getElementById('em_prossima_scadenza').value     = btn.dataset.prossima_scadenza ?? '';
    document.getElementById('em_tipo').value                  = btn.dataset.tipo;
    document.getElementById('em_eseguita_da').value           = btn.dataset.eseguita_da ?? '';
    document.getElementById('em_descrizione').value           = btn.dataset.descrizione ?? '';
});
</script>
@endsection

@endsection
