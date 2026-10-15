@extends('layouts.app')

@section('content')
    <style>
        .rda-header {
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
        .rda-header h1 { font-size: 1.75rem; font-weight: 800; margin: 0; letter-spacing: -.5px; }
        .rda-header p  { margin: 0.25rem 0 0; opacity: .7; font-size: .88rem; }

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

        .rda-table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .table-rda {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .table-rda thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-rda tbody td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
        }
        .table-rda tbody tr:last-child td { border-bottom: none; }
        .table-rda tbody tr:hover { background: #fbfcfd; }

        .badge-rda-stato {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .badge-rda-stato.in_attesa  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-rda-stato.approvata  { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-rda-stato.ordinata   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-rda-stato.bozza      { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-rda-stato.rifiutata  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

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

    <div class="rda-header">
        <div>
            <h1><i class="bi bi-cart-check-fill me-2"></i> Richieste di Acquisto (RDA)</h1>
            <p>Modulo MD PG04/01-00 per approvvigionamento materiali, accessori e servizi di laboratorio</p>
        </div>
        <div>
            <a href="{{ route('rda.create') }}" class="btn btn-danger fw-semibold px-3 py-2 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Nuova RDA
            </a>
        </div>
    </div>

    {{-- ── 4 STATISTICHE RAPIDE ── --}}
    <div class="stat-mini-bar">
        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#fee2e2;color:#d90429;">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-dark lh-1">{{ $totaliRda }}</div>
                <div class="text-muted small">Totale RDA Emesse</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#fef3c7;color:#b45309;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-warning lh-1">{{ $inAttesaCount }}</div>
                <div class="text-muted small">In Attesa Approvazione</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#dcfce7;color:#15803d;">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-success lh-1">{{ $approvateCount }}</div>
                <div class="text-muted small">RDA Approvate</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background:#e0f2fe;color:#0369a1;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="fw-bold fs-4 text-primary lh-1">{{ $ordinateCount }}</div>
                <div class="text-muted small">Ordinate (ODA)</div>
            </div>
        </div>
    </div>

    {{-- ── BARRA FILTRI E RICERCA ── --}}
    <div class="filter-panel">
        <form method="GET" action="{{ route('rda.index') }}" class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 550px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Cerca per n° RDA (es. 001/26), richiedente, prodotto..." value="{{ request('q') }}">
                <button type="submit" class="btn btn-dark fw-semibold px-3">Cerca</button>
            </div>
            @if(request()->hasAny(['q', 'stato', 'fornitore_id']))
                <a href="{{ route('rda.index') }}" class="btn btn-outline-secondary" title="Azzera filtri">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted small fw-semibold">Stato:</span>
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('rda.index', array_merge(request()->except('stato'), [])) }}"
                   class="btn {{ !request('stato') ? 'btn-dark' : 'btn-outline-secondary' }}">Tutte</a>
                <a href="{{ route('rda.index', array_merge(request()->except('stato'), ['stato'=>'in_attesa'])) }}"
                   class="btn {{ request('stato')==='in_attesa' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">In Attesa</a>
                <a href="{{ route('rda.index', array_merge(request()->except('stato'), ['stato'=>'approvata'])) }}"
                   class="btn {{ request('stato')==='approvata' ? 'btn-success text-white' : 'btn-outline-secondary' }}">Approvate</a>
                <a href="{{ route('rda.index', array_merge(request()->except('stato'), ['stato'=>'ordinata'])) }}"
                   class="btn {{ request('stato')==='ordinata' ? 'btn-primary text-white' : 'btn-outline-secondary' }}">Ordinate</a>
            </div>
        </div>
    </div>

    {{-- ── TABELLA RDA ── --}}
    <div class="rda-table-card">
        <div class="table-responsive">
            <table class="table table-rda">
                <thead>
                    <tr>
                        <th style="width: 110px;">N° RDA</th>
                        <th>Richiedente</th>
                        <th style="width: 130px;">Data Richiesta</th>
                        <th style="width: 140px;">Data Consegna</th>
                        <th>Articoli / Prodotti Richiesti</th>
                        <th>Fornitore Consigliato</th>
                        <th style="width: 130px;">Stato</th>
                        <th style="width: 160px;" class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rdaList as $rda)
                        <tr>
                            <td>
                                <a href="{{ route('rda.show', $rda) }}" class="badge bg-light text-dark border mono fw-bold px-2 py-1 text-decoration-none fs-6">
                                    {{ $rda->numero }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rda->richiedente }}</div>
                                @if($rda->user)
                                    <div class="text-muted small"><i class="bi bi-person me-1"></i>{{ $rda->user->username }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="mono">{{ $rda->data_richiesta ? $rda->data_richiesta->format('d/m/Y') : '—' }}</span>
                            </td>
                            <td>
                                @if($rda->data_approvvigionamento)
                                    <span class="mono text-dark fw-semibold">{{ $rda->data_approvvigionamento->format('d/m/Y') }}</span>
                                    @if($rda->data_approvvigionamento->isPast() && !in_array($rda->stato, ['ordinata', 'rifiutata']))
                                        <span class="badge bg-danger-subtle text-danger ms-1" style="font-size:0.65rem;">Scaduta</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-secondary-subtle text-dark border me-1">{{ $rda->righe->count() }} art.</span>
                                    <span class="text-dark small">
                                        {{ Str::limit($rda->righe->pluck('descrizione_prodotto')->join(', '), 50) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $nomiFornitori = $rda->righe->map(fn($r) => $r->fornitore_nome)->unique()->filter(fn($n) => $n !== '—');
                                @endphp
                                @if($nomiFornitori->isNotEmpty())
                                    <span class="text-dark fw-semibold small">
                                        {{ Str::limit($nomiFornitori->join('; '), 40) }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $lblStato = [
                                        'in_attesa'  => 'In Attesa',
                                        'approvata'  => 'Approvata',
                                        'ordinata'   => 'Ordinata (ODA)',
                                        'bozza'      => 'Bozza',
                                        'rifiutata'  => 'Rifiutata'
                                    ][$rda->stato] ?? ucfirst($rda->stato);
                                @endphp
                                <span class="badge-rda-stato {{ $rda->stato }}">
                                    @if($rda->stato === 'approvata') <i class="bi bi-check-circle-fill"></i>
                                    @elseif($rda->stato === 'in_attesa') <i class="bi bi-hourglass-split"></i>
                                    @elseif($rda->stato === 'ordinata') <i class="bi bi-box-seam"></i>
                                    @elseif($rda->stato === 'rifiutata') <i class="bi bi-x-circle-fill"></i>
                                    @else <i class="bi bi-pencil"></i>
                                    @endif
                                    {{ $lblStato }}
                                </span>
                            </td>
                            <td class="text-end" style="white-space: nowrap;">
                                <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                                    {{-- Trasforma in ODA --}}
                                    @if($rda->stato !== 'rifiutata')
                                        <a href="{{ route('rda.generaOda', $rda) }}" class="btn-action order" style="color: #dc2626; border-color: #fecaca; background: #fff5f5;" title="Trasforma in ODA (Ordine di Acquisto)">
                                            <i class="bi bi-cart-plus-fill"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('rda.show', $rda) }}" class="btn-action" title="Visualizza Scheda RDA (MD PG04/01-00)">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('rda.downloadWord', $rda) }}" class="btn-action word" title="Esporta Word (.doc)">
                                        <i class="bi bi-file-earmark-word"></i>
                                    </a>
                                    <a href="{{ route('rda.edit', $rda) }}" class="btn-action edit" title="Modifica RDA">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('rda.destroy', $rda) }}" method="POST" class="d-inline m-0 p-0" data-confirm="Vuoi davvero eliminare la Richiesta di Acquisto {{ $rda->numero }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action del" title="Elimina RDA">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-50"></i>
                                <h6 class="fw-bold">Nessuna Richiesta di Acquisto presente</h6>
                                <p class="small mb-3">Non risultano RDA per i filtri selezionati.</p>
                                <a href="{{ route('rda.create') }}" class="btn btn-sm btn-danger fw-semibold">
                                    + Compila la prima RDA
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rdaList->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $rdaList->links() }}
            </div>
        @endif
    </div>
@endsection
