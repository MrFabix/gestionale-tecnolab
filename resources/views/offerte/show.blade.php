@extends('layouts.app')
@section('content')
    <style>
        .off-show-header { background:linear-gradient(135deg,#1a1a2e,#c00000 200%); border-radius:16px; padding:1.75rem 2rem; margin-bottom:1.5rem; color:#fff; }
        .off-show-header h2 { font-size:1.9rem; font-weight:700; margin:0; }

        .badge-stato { padding:.35rem .85rem; border-radius:999px; font-size:.8rem; font-weight:700; }
        .badge-bozza     { background:rgba(243,244,246,.2); color:#e5e7eb; border:1px solid rgba(229,231,235,.3); }
        .badge-inviata   { background:rgba(219,234,254,.3); color:#93c5fd; border:1px solid rgba(147,197,253,.3); }
        .badge-accettata { background:rgba(220,252,231,.3); color:#86efac; border:1px solid rgba(134,239,172,.3); }
        .badge-rifiutata { background:rgba(254,226,226,.3); color:#fca5a5; border:1px solid rgba(252,165,165,.3); }

        .section-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; margin-bottom:1.5rem; overflow:hidden; }
        .section-head { padding:.85rem 1.25rem; border-bottom:1px solid #f0f0f0; background:#fafafa; display:flex; justify-content:space-between; align-items:center; }
        .section-head h5 { margin:0; font-size:.95rem; font-weight:700; }

        .info-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:1rem; padding:1.25rem; }
        .info-item label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; display:block; margin-bottom:.2rem; }
        .info-item span  { font-size:.9rem; color:#374151; }

        .righe-table { width:100%; border-collapse:collapse; font-size:.875rem; }
        .righe-table thead th { background:#f8fafc; border-bottom:2px solid #e5e7eb; padding:.65rem 1rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; }
        .righe-table tbody tr { border-bottom:1px solid #f5f5f5; }
        .righe-table tbody tr:nth-child(even) { background:#fafafa; }
        .righe-table td { padding:.65rem 1rem; }
        .righe-table tfoot td { padding:.85rem 1rem; background:#f8fafc; border-top:2px solid #e5e7eb; font-weight:700; font-size:1rem; }
        .totale-big { font-size:1.4rem; font-weight:800; color:#c00000; }

        .commessa-link { background:#dcfce7; color:#15803d; padding:.4rem 1rem; border-radius:999px; font-weight:700; font-size:.85rem; text-decoration:none; }
        .commessa-link:hover { background:#bbf7d0; color:#15803d; }

        .btn-action-lg { padding:.5rem 1.1rem; border-radius:9px; font-size:.85rem; font-weight:600; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; border:1.5px solid; transition:all .15s; }
    </style>

    @php $o = $offerta; @endphp

    <div class="off-show-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h2>{{ $o->numero }}</h2>
                    <span class="badge-stato badge-{{ $o->stato }}">{{ ucfirst($o->stato) }}</span>
                </div>
                <p class="mb-1 opacity-75" style="font-size:1.05rem;">{{ $o->cliente->ragione_sociale ?? "-" }}</p>
                <p class="opacity-50" style="font-size:.85rem;">
                    Data: {{ $o->data->format('d/m/Y') ?? "-" }}
                    @if($o->validita_fino) &nbsp;·&nbsp; Valida fino: {{ $o->validita_fino->format('d/m/Y') }} @endif
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                {{-- Scarica Word --}}
                <a href="{{ route('offerte.downloadWord', $o) }}"
                   class="btn-action-lg" style="background:rgba(109,40,217,.15);border-color:rgba(196,181,253,.5);color:#c4b5fd;">
                    <i class="bi bi-file-earmark-word"></i> Scarica Word
                </a>
                {{-- Accetta → Commessa --}}
                @if(!in_array($o->stato, ['accettata','rifiutata']))
                    <form action="{{ route('offerte.accetta', $o) }}" method="POST"
                          onsubmit="return confirm('Accettare l\'offerta e creare automaticamente la commessa?')">
                        @csrf
                        <button class="btn-action-lg" style="background:rgba(21,128,61,.15);border-color:rgba(134,239,172,.5);color:#86efac;">
                            <i class="bi bi-check-circle"></i> Accetta → Commessa
                        </button>
                    </form>
                @endif
                {{-- Modifica --}}
                <a href="{{ route('offerte.edit', $o) }}"
                   class="btn-action-lg" style="background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.3);color:#fff;">
                    <i class="bi bi-pencil"></i> Modifica
                </a>
                {{-- Torna --}}
                <a href="{{ route('offerte.index') }}"
                   class="btn-action-lg" style="background:transparent;border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Commessa collegata --}}
    @if($o->commessa)
        <div class="alert alert-success d-flex align-items-center gap-3 mb-3">
            <i class="bi bi-briefcase-fill fs-4"></i>
            <div>
                Offerta accettata — commessa creata automaticamente:
                <a href="{{ route('commesse.show', $o->commessa) }}" class="commessa-link ms-2">
                    <i class="bi bi-briefcase me-1"></i>{{ $o->commessa->codice }}
                </a>
            </div>
        </div>
    @endif

    <div class="row g-3">
        {{-- Dati intestazione --}}
        <div class="col-md-6">
            <div class="section-card">
                <div class="section-head"><h5><i class="bi bi-info-circle text-muted me-2"></i>Dati offerta</h5></div>
                <div class="info-grid">
                    <div class="info-item"><label>Cliente</label><span>{{ $o->cliente->ragione_sociale }}</span></div>
                    @if($o->attenzione)<div class="info-item"><label>Attenzione</label><span>{{ $o->attenzione }}</span></div>@endif
                    @if($o->riferimento)<div class="info-item"><label>Vs. Riferimento</label><span>{{ $o->riferimento }}</span></div>@endif
                    <div class="info-item"><label>Consegna</label><span>{{ $o->consegna ?? '—' }}</span></div>
                    <div class="info-item"><label>Pagamento</label><span>{{ $o->pagamento ?? '—' }}</span></div>
                    @if($o->commessa_rif)<div class="info-item"><label>Rif. Commessa</label><span>{{ $o->commessa_rif }}</span></div>@endif
                    @if($o->note)<div class="info-item" style="grid-column:1/-1;"><label>Note</label><span>{{ $o->note }}</span></div>@endif
                    @if($o->note_interne)<div class="info-item" style="grid-column:1/-1;background:#fffbeb;padding:.5rem;border-radius:8px;"><label>Note interne</label><span>{{ $o->note_interne }}</span></div>@endif
                </div>
            </div>
        </div>

        {{-- Allegati --}}
        <div class="col-md-6">
            <div class="section-card h-100">
                <div class="section-head"><h5><i class="bi bi-paperclip text-muted me-2"></i>Allegati</h5></div>
                <div class="p-3">
                    @if($allegati->count())
                        <ul class="list-group list-group-flush">
                            @foreach($allegati as $all)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <a href="{{ $all->getUrl() }}" target="_blank" class="text-decoration-none small">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $all->name }}
                                    </a>
                                    <form action="{{ route('offerte.media.destroy', [$o, $all->id]) }}" method="POST"
                                          data-confirm="Eliminare?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small mb-0">Nessun allegato.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabella voci --}}
    <div class="section-card">
        <div class="section-head"><h5><i class="bi bi-list-ul text-muted me-2"></i>Voci / Servizi offerti</h5></div>
        <div style="overflow-x:auto;">
            <table class="righe-table">
                <thead>
                <tr>
                    <th style="width:52%">Descrizione beni / servizi</th>
                    <th style="width:8%;text-align:center;">UM</th>
                    <th style="width:10%;text-align:right;">Q.tà</th>
                    <th style="width:15%;text-align:right;">Prezzo unitario</th>
                    <th style="width:15%;text-align:right;">Totale riga</th>
                </tr>
                </thead>
                <tbody>
                @foreach($o->righe as $r)
                    <tr>
                        <td>{{ $r->descrizione }}</td>
                        <td style="text-align:center;">{{ $r->um }}</td>
                        <td style="text-align:right;">{{ number_format($r->quantita,2,',','.') }}</td>
                        <td style="text-align:right;">€ {{ number_format($r->prezzo_unitario,2,',','.') }}</td>
                        <td style="text-align:right;font-weight:600;">€ {{ number_format($r->totale_riga,2,',','.') }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;color:#6b7280;font-size:.85rem;">
                        TOTALE IMPONIBILE (escluso IVA)
                    </td>
                    <td style="text-align:right;">
                        <span class="totale-big">€ {{ number_format($o->totale,2,',','.') }}</span>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
