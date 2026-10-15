@extends('layouts.app')

@section('content')
    <style>
        .forn-header {
            background: linear-gradient(135deg, #111422 0%, #1e2238 60%, #0f121d 100%);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .forn-header h1 { font-size: 1.75rem; font-weight: 800; margin: 0; letter-spacing: -.5px; }
        .forn-header p  { margin: 0.25rem 0 0; opacity: .7; font-size: .88rem; }

        .stat-mini-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-mini-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: transform 0.15s ease;
        }
        .stat-mini-box:hover { transform: translateY(-2px); }

        .stat-mini-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .filter-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .forn-table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .table-forn {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .table-forn thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-forn tbody td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
        }
        .table-forn tbody tr:last-child td { border-bottom: none; }
        .table-forn tbody tr:hover { background: #fbfcfd; }

        .badge-qual {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .badge-qual.qualificato { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-qual.in_valutazione { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-qual.sospeso { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-qual.non_qualificato { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 0.9rem;
            transition: all 0.15s ease;
            text-decoration: none;
            padding: 0;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .btn-action:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .btn-action.word:hover {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #93c5fd;
        }
        .btn-action.edit:hover {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }
        .btn-action.del:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fca5a5;
        }
    </style>

    <div class="forn-header">
        <div>
            <h1><i class="bi bi-truck me-2"></i> Registro Fornitori</h1>
            <p>Elenco fornitori qualificati, scheda MD PG04/04-00 e registro non conformità (RNC / RAC)</p>
        </div>
        <div>
            <a href="{{ route('fornitori.create') }}" class="btn btn-danger fw-semibold px-3 py-2 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Nuovo Fornitore
            </a>
        </div>
    </div>

    {{-- ── 4 STATISTICHE RAPIDE ── --}}
    <div class="stat-mini-bar">
        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#fee2e2;color:#d90429;">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-dark lh-1">{{ $totaliFornitori }}</div>
                <div class="text-muted small">Fornitori Registrati</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#dcfce7;color:#15803d;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-success lh-1">{{ $qualificatiCount }}</div>
                <div class="text-muted small">Qualificati ISO</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#fef3c7;color:#b45309;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-warning lh-1">{{ $inValutazioneCount }}</div>
                <div class="text-muted small">In Valutazione</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#f1f5f9;color:#475569;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-dark lh-1">{{ $conNonConformita }}</div>
                <div class="text-muted small">Con Reclami o RNC</div>
            </div>
        </div>
    </div>

    {{-- ── BARRA FILTRI E RICERCA ── --}}
    <div class="filter-panel">
        <form method="GET" action="{{ route('fornitori.index') }}" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 550px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Cerca ragione sociale, codice, P.IVA, prodotti..." value="{{ request('q') }}">
                <button type="submit" class="btn btn-dark fw-semibold px-3">Cerca</button>
            </div>
            @if(request()->hasAny(['q', 'stato']))
                <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary" title="Azzera filtri">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-semibold">Stato:</span>
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('fornitori.index', array_merge(request()->except('stato'), [])) }}"
                   class="btn {{ !request('stato') ? 'btn-dark' : 'btn-outline-secondary' }}">Tutti</a>
                <a href="{{ route('fornitori.index', array_merge(request()->except('stato'), ['stato'=>'qualificato'])) }}"
                   class="btn {{ request('stato')==='qualificato' ? 'btn-success text-white' : 'btn-outline-secondary' }}">Qualificati</a>
                <a href="{{ route('fornitori.index', array_merge(request()->except('stato'), ['stato'=>'in_valutazione'])) }}"
                   class="btn {{ request('stato')==='in_valutazione' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">In Valutaz.</a>
                <a href="{{ route('fornitori.index', array_merge(request()->except('stato'), ['stato'=>'sospeso'])) }}"
                   class="btn {{ request('stato')==='sospeso' ? 'btn-danger text-white' : 'btn-outline-secondary' }}">Sospesi</a>
            </div>
        </div>
    </div>

    {{-- ── TABELLA FORNITORI ── --}}
    <div class="forn-table-card">
        <div class="table-responsive">
            <table class="table table-forn">
                <thead>
                    <tr>
                        <th style="width: 80px;">Cod.</th>
                        <th>Fornitore / Ragione Sociale</th>
                        <th>Contatti</th>
                        <th>Prodotti / Servizi Acquistati</th>
                        <th>Criteri di Qualifica</th>
                        <th>Stato Qualifica</th>
                        <th style="width: 160px;" class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fornitori as $f)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border mono fw-bold px-2 py-1">
                                    {{ $f->codice ?: str_pad($f->id, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $f->ragione_sociale }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $f->indirizzo_completo }}
                                    @if($f->partita_iva) &bull; P.I.: <span class="mono">{{ $f->partita_iva }}</span> @endif
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    @if($f->telefono)
                                        <div><i class="bi bi-telephone me-1 text-muted"></i><a href="tel:{{ $f->telefono }}" class="text-decoration-none text-dark">{{ $f->telefono }}</a></div>
                                    @endif
                                    @if($f->email)
                                        <div class="text-truncate" style="max-width: 180px;"><i class="bi bi-envelope me-1 text-muted"></i><a href="mailto:{{ $f->email }}" class="text-decoration-none text-muted" title="{{ $f->email }}">{{ $f->email }}</a></div>
                                    @endif
                                    @if($f->pec)
                                        <div class="text-truncate" style="max-width: 180px;"><i class="bi bi-shield-check me-1 text-primary"></i><span class="text-muted" title="{{ $f->pec }}">{{ $f->pec }}</span></div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-dark">{{ Str::limit($f->prodotti_acquistati ?: '—', 45) }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($f->criteri_qualifica ?: '—', 40) }}</span>
                            </td>
                            <td>
                                @php
                                    $lblStato = [
                                        'qualificato'    => 'Qualificato',
                                        'in_valutazione' => 'In Valutazione',
                                        'sospeso'        => 'Sospeso',
                                        'non_qualificato'=> 'Non Qualificato'
                                    ][$f->stato_qualifica] ?? ucfirst($f->stato_qualifica);
                                @endphp
                                <span class="badge-qual {{ $f->stato_qualifica }}">
                                    @if($f->stato_qualifica === 'qualificato') <i class="bi bi-check-circle-fill"></i>
                                    @elseif($f->stato_qualifica === 'in_valutazione') <i class="bi bi-hourglass-split"></i>
                                    @else <i class="bi bi-x-circle-fill"></i>
                                    @endif
                                    {{ $lblStato }}
                                </span>
                            </td>
                            <td class="text-end" style="white-space: nowrap;">
                                <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                                    <a href="{{ route('fornitori.show', $f) }}" class="btn-action" title="Scheda Fornitore (MD PG04/04-00)">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('fornitori.downloadWord', $f) }}" class="btn-action word" title="Scarica Documento Word">
                                        <i class="bi bi-file-earmark-word"></i>
                                    </a>
                                    <a href="{{ route('fornitori.edit', $f) }}" class="btn-action edit" title="Modifica Fornitore">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('fornitori.destroy', $f) }}" method="POST" class="d-inline m-0 p-0" data-confirm="Vuoi davvero eliminare il fornitore {{ $f->ragione_sociale }} e tutto il suo storico forniture?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action del" title="Elimina Fornitore">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-truck fs-1 d-block mb-2 opacity-50"></i>
                                <h6 class="fw-bold">Nessun fornitore trovato</h6>
                                <p class="small mb-3">Non ci sono fornitori che corrispondono ai criteri di ricerca impostati.</p>
                                <a href="{{ route('fornitori.create') }}" class="btn btn-sm btn-danger fw-semibold">
                                    + Aggiungi il primo Fornitore
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fornitori->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $fornitori->links() }}
            </div>
        @endif
    </div>
@endsection
