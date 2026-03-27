@extends('layouts.app')
@section('content')

    <style>
        .profile-header {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; color: #fff;
        }
        .profile-avatar { width:90px; height:90px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,.4); }
        .profile-avatar-ph {
            width:90px; height:90px; border-radius:50%;
            background:rgba(255,255,255,.15);
            display:flex; align-items:center; justify-content:center;
            font-size:2rem; font-weight:700; border:3px solid rgba(255,255,255,.3);
        }
        .section-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; margin-bottom:1.5rem; overflow:hidden; }
        .section-card .section-head {
            padding:.85rem 1.25rem;
            border-bottom:1px solid #f0f0f0;
            display:flex; justify-content:space-between; align-items:center;
            background:#fafafa;
        }
        .section-card .section-head h5 { margin:0; font-size:.95rem; font-weight:700; }
        .info-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem; padding:1.25rem; }
        .info-item label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; display:block; margin-bottom:.2rem; }
        .info-item span  { font-size:.9rem; color:#374151; }

        .badge-stato { padding:.3rem .75rem; border-radius:999px; font-size:.78rem; font-weight:700; }
        .badge-attivo   { background:rgba(220,252,231,.3); color:#86efac; border:1px solid rgba(134,239,172,.3); }
        .badge-in_ferie { background:rgba(219,234,254,.3); color:#93c5fd; border:1px solid rgba(147,197,253,.3); }
        .badge-dimesso  { background:rgba(243,244,246,.2); color:#9ca3af; border:1px solid rgba(156,163,175,.3); }
        .badge-sospeso  { background:rgba(254,249,195,.2); color:#fde68a; border:1px solid rgba(253,230,138,.3); }

        .badge-f { padding:.25rem .6rem; border-radius:999px; font-size:.72rem; font-weight:600; }
        .badge-f-ok   { background:#dcfce7; color:#15803d; }
        .badge-f-warn { background:#fef9c3; color:#92400e; }
        .badge-f-scad { background:#fee2e2; color:#b91c1c; }
        .badge-f-none { background:#f3f4f6; color:#9ca3af; }

        .form-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
        .form-table th { background:#f8fafc; border-bottom:2px solid #e5e7eb; padding:.65rem 1rem; font-size:.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; }
        .form-table td { padding:.65rem 1rem; border-bottom:1px solid #f5f5f5; vertical-align:middle; }
        .form-table tr:last-child td { border-bottom:none; }
        .form-table tr:hover td { background:#fafbff; }

        .btn-action { width:28px; height:28px; border-radius:6px; border:1px solid #e5e7eb; background:#fff; display:inline-flex; align-items:center; justify-content:center; color:#374151; font-size:.8rem; text-decoration:none; transition:all .12s; }
        .btn-action:hover     { background:#f3f4f6; color:#2c5364; }
        .btn-action.del:hover { background:#fee2e2; color:#b91c1c; border-color:#fca5a5; }
    </style>

    {{-- Profile Header --}}
    <div class="profile-header">
        <div class="d-flex align-items-center gap-3 mb-3">
            @php $thumb = $personale->getFirstMediaUrl('foto','thumb'); $iniziali = strtoupper(mb_substr($personale->cognome,0,1).mb_substr($personale->nome,0,1)); @endphp
            @if($thumb)
                <img src="{{ $thumb }}" class="profile-avatar" alt="">
            @else
                <div class="profile-avatar-ph">{{ $iniziali }}</div>
            @endif
            <div>
                <h2 class="mb-0 fw-bold">{{ $personale->cognome }} {{ $personale->nome }}</h2>
                <p class="mb-1 opacity-75">{{ $personale->qualifica ?? 'Nessuna qualifica' }}
                    @if($personale->reparto) &nbsp;·&nbsp; {{ $personale->reparto }} @endif
                </p>
                @php $statoMap = ['attivo'=>'Attivo','in_ferie'=>'In ferie','dimesso'=>'Dimesso','sospeso'=>'Sospeso']; @endphp
                <span class="badge-stato badge-{{ $personale->stato }}">{{ $statoMap[$personale->stato] ?? $personale->stato }}</span>
                @if($personale->user)
                    <span class="ms-2" style="background:rgba(167,139,250,.2);color:#c4b5fd;padding:.3rem .75rem;border-radius:999px;font-size:.78rem;font-weight:600;border:1px solid rgba(196,181,253,.3);">
                    <i class="bi bi-person-check me-1"></i>{{ $personale->user->username }}
                </span>
                @endif
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('personale.edit', $personale) }}" class="btn btn-light btn-sm fw-semibold">
                    <i class="bi bi-pencil me-1"></i> Modifica
                </a>
                <a href="{{ route('personale.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-3">
        {{-- Colonna sinistra: dati anagrafici + aziendali + documenti --}}
        <div class="col-md-5">

            {{-- Anagrafica --}}
            <div class="section-card">
                <div class="section-head"><h5><i class="bi bi-person-vcard me-2 text-muted"></i>Anagrafica</h5></div>
                <div class="info-grid">
                    <div class="info-item"><label>Codice Fiscale</label><span>{{ $personale->codice_fiscale ?? '—' }}</span></div>
                    <div class="info-item"><label>Data di nascita</label><span>{{ $personale->data_nascita?->format('d/m/Y') ?? '—' }}</span></div>
                    <div class="info-item"><label>Luogo di nascita</label><span>{{ $personale->luogo_nascita ?? '—' }}</span></div>
                    <div class="info-item"><label>Indirizzo</label><span>{{ $personale->indirizzo ?? '—' }}</span></div>
                    <div class="info-item"><label>Telefono</label><span>{{ $personale->telefono ?? '—' }}</span></div>
                    <div class="info-item"><label>Email</label><span>{{ $personale->email ?? '—' }}</span></div>
                </div>
            </div>

            {{-- Dati aziendali --}}
            <div class="section-card">
                <div class="section-head"><h5><i class="bi bi-briefcase me-2 text-muted"></i>Dati aziendali</h5></div>
                <div class="info-grid">
                    <div class="info-item"><label>Data assunzione</label><span>{{ $personale->data_assunzione?->format('d/m/Y') ?? '—' }}</span></div>
                    <div class="info-item"><label>Fine rapporto</label><span>{{ $personale->data_fine_rapporto?->format('d/m/Y') ?? '—' }}</span></div>
                    @if($personale->note)
                        <div class="info-item" style="grid-column:1/-1;"><label>Note</label><span>{{ $personale->note }}</span></div>
                    @endif
                </div>
            </div>

            {{-- Documenti --}}
            <div class="section-card">
                <div class="section-head"><h5><i class="bi bi-file-earmark-text me-2 text-muted"></i>Documenti</h5></div>
                <div class="p-3">
                    @if($documenti->count())
                        <ul class="list-group list-group-flush">
                            @foreach($documenti as $doc)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <a href="{{ $doc->getUrl() }}" target="_blank" class="text-decoration-none small">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $doc->name }}
                                    </a>
                                    <form action="{{ route('personale.media.destroy', [$personale, $doc->id]) }}" method="POST"
                                          onsubmit="return confirm('Eliminare?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action del"><i class="bi bi-trash"></i></button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small mb-0">Nessun documento caricato.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonna destra: Formazione --}}
        <div class="col-md-7">
            <div class="section-card">
                <div class="section-head">
                    <h5><i class="bi bi-mortarboard me-2 text-muted"></i>Formazione & Corsi</h5>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#formFormazione">
                        <i class="bi bi-plus"></i> Aggiungi
                    </button>
                </div>

                {{-- Form aggiungi formazione --}}
                <div class="collapse" id="formFormazione">
                    <div class="p-3 border-bottom bg-light">
                        <form action="{{ route('personale.formazioni.store', $personale) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label form-label-sm">Titolo corso / certificazione *</label>
                                    <input type="text" name="titolo" class="form-control form-control-sm" required
                                           placeholder="es: Corso D.Lgs 81/08, HACCP, ...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm">Ente erogatore</label>
                                    <input type="text" name="ente_erogatore" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label form-label-sm">Data conseguimento *</label>
                                    <input type="date" name="data_conseguimento" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label form-label-sm">Scadenza</label>
                                    <input type="date" name="data_scadenza" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label form-label-sm">Attestato (PDF/immagine)</label>
                                    <input type="file" name="attestato" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label form-label-sm">Note</label>
                                    <input type="text" name="note" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-save"></i> Salva
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabella formazioni --}}
                <div style="overflow-x:auto;">
                    <table class="form-table">
                        <thead>
                        <tr>
                            <th>Corso / Certificazione</th>
                            <th>Ente</th>
                            <th>Conseguito</th>
                            <th>Scadenza</th>
                            <th>Attestato</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($personale->formazioni as $f)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $f->titolo }}</span>
                                    @if($f->note)<br><small class="text-muted">{{ $f->note }}</small>@endif
                                </td>
                                <td><small>{{ $f->ente_erogatore ?? '—' }}</small></td>
                                <td><small>{{ $f->data_conseguimento->format('d/m/Y') }}</small></td>
                                <td>
                                    @if(!$f->data_scadenza)
                                        <span class="badge-f badge-f-none">Nessuna</span>
                                    @elseif($f->isScaduta())
                                        <span class="badge-f badge-f-scad"><i class="bi bi-exclamation-triangle me-1"></i>{{ $f->data_scadenza->format('d/m/Y') }}</span>
                                    @elseif($f->isInScadenza(30))
                                        <span class="badge-f badge-f-warn"><i class="bi bi-clock me-1"></i>{{ $f->data_scadenza->format('d/m/Y') }}</span>
                                    @else
                                        <span class="badge-f badge-f-ok"><i class="bi bi-check me-1"></i>{{ $f->data_scadenza->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($f->getFirstMedia('attestato'))
                                        <a href="{{ $f->getFirstMediaUrl('attestato') }}" target="_blank" class="btn-action" title="Scarica attestato">
                                            <i class="bi bi-file-earmark-arrow-down"></i>
                                        </a>
                                    @else <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn-action" title="Modifica"
                                        data-bs-toggle="modal" data-bs-target="#modalEditFormazione"
                                        data-id="{{ $f->id }}"
                                        data-titolo="{{ $f->titolo }}"
                                        data-ente="{{ $f->ente_erogatore }}"
                                        data-conseguimento="{{ $f->data_conseguimento->format('Y-m-d') }}"
                                        data-scadenza="{{ $f->data_scadenza?->format('Y-m-d') }}"
                                        data-note="{{ $f->note }}"
                                        data-action="{{ route('personale.formazioni.update', [$personale, $f]) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('personale.formazioni.destroy', [$personale, $f]) }}" method="POST"
                                          onsubmit="return confirm('Eliminare questa formazione?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action del"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Nessuna formazione registrata.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

{{-- Modal modifica formazione --}}
<div class="modal fade" id="modalEditFormazione" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditFormazione" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Modifica formazione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label form-label-sm">Titolo corso / certificazione *</label>
                            <input type="text" name="titolo" id="ef_titolo" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label form-label-sm">Ente erogatore</label>
                            <input type="text" name="ente_erogatore" id="ef_ente" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm">Data conseguimento *</label>
                            <input type="date" name="data_conseguimento" id="ef_conseguimento" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label form-label-sm">Scadenza</label>
                            <input type="date" name="data_scadenza" id="ef_scadenza" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm">Sostituisci attestato (opzionale)</label>
                            <input type="file" name="attestato" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm">Note</label>
                            <input type="text" name="note" id="ef_note" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Salva modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('modalEditFormazione').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('formEditFormazione').action = btn.dataset.action;
    document.getElementById('ef_titolo').value        = btn.dataset.titolo;
    document.getElementById('ef_ente').value          = btn.dataset.ente ?? '';
    document.getElementById('ef_conseguimento').value = btn.dataset.conseguimento;
    document.getElementById('ef_scadenza').value      = btn.dataset.scadenza ?? '';
    document.getElementById('ef_note').value          = btn.dataset.note ?? '';
});
</script>
@endsection
