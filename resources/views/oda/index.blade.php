@extends('layouts.app')

@section('content')
    <style>
        .oda-header {
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
        .oda-header h1 { font-size: 1.75rem; font-weight: 800; margin: 0; letter-spacing: -.5px; }
        .oda-header p  { margin: 0.25rem 0 0; opacity: .7; font-size: .88rem; }

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

        .oda-table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .table-oda {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
        }
        .table-oda thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-oda tbody td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
        }
        .table-oda tbody tr:last-child td { border-bottom: none; }
        .table-oda tbody tr:hover { background: #fbfcfd; }

        .badge-oda-stato {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .badge-stato-inviato    { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-stato-confermato { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-stato-consegnato { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-stato-bozza      { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-stato-annullato  { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

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
        .btn-action.view:hover { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }
        .btn-action.word:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .btn-action.edit:hover { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .btn-action.del:hover  { background: #fef2f2; color: #dc2626; border-color: #fca5a5; }
    </style>

    {{-- Header --}}
    <div class="oda-header">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">MD PG 04/02-00</span>
                <span class="text-white-50 small fw-bold text-uppercase">&bull; Approvvigionamenti Ufficiali</span>
            </div>
            <h1>Ordini di Acquisto (ODA)</h1>
            <p>Emissione ordini fornitori e aggiornamento automatico scheda fornitore Tecnolab</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('oda.create') }}" class="btn btn-danger fw-bold d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm">
                <i class="bi bi-plus-circle fs-5"></i> Nuovo ODA Manuale
            </a>
            <a href="{{ route('rda.index') }}" class="btn btn-outline-light fw-semibold d-inline-flex align-items-center gap-1 px-3 py-2">
                <i class="bi bi-cart-check"></i> Elenco RDA
            </a>
        </div>
    </div>

    {{-- Barra Statistiche Rapide --}}
    <div class="stat-mini-bar">
        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background: #f1f5f9; color: #334155;">
                <i class="bi bi-bag-check"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Totale ODA</div>
                <div class="fs-4 fw-bolder text-dark">{{ $totaleOda }}</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-send-check"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Inviati</div>
                <div class="fs-4 fw-bolder text-info">{{ $inviatiCount }}</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background: #dcfce7; color: #16a34a;">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Consegnati</div>
                <div class="fs-4 fw-bolder text-success">{{ $consegnatiCount }}</div>
            </div>
        </div>

        <div class="stat-mini-box">
            <div class="stat-mini-icon" style="background: #fee2e2; color: #dc2626;">
                <i class="bi bi-currency-euro"></i>
            </div>
            <div>
                <div class="text-muted small fw-bold text-uppercase">Totale Imponibile</div>
                <div class="fs-4 fw-bolder text-danger">€ {{ number_format($totaleSpesa, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Filtri e Ricerca --}}
    <form method="GET" action="{{ route('oda.index') }}" class="filter-panel">
        <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 260px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0 ps-0" 
                       placeholder="Cerca per N° ODA, fornitore, commessa, descrizione bene..." 
                       value="{{ request('q') }}">
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <select name="stato" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">-- Tutti gli stati --</option>
                <option value="inviato" {{ request('stato') == 'inviato' ? 'selected' : '' }}>Inviato al Fornitore</option>
                <option value="confermato" {{ request('stato') == 'confermato' ? 'selected' : '' }}>Confermato</option>
                <option value="consegnato" {{ request('stato') == 'consegnato' ? 'selected' : '' }}>Consegnato / Chiuso</option>
                <option value="bozza" {{ request('stato') == 'bozza' ? 'selected' : '' }}>Bozza</option>
                <option value="annullato" {{ request('stato') == 'annullato' ? 'selected' : '' }}>Annullato</option>
            </select>

            <select name="fornitore_id" class="form-select form-select-sm" style="max-width: 220px;" onchange="this.form.submit()">
                <option value="">-- Tutti i fornitori --</option>
                @foreach($fornitori as $f)
                    <option value="{{ $f->id }}" {{ request('fornitore_id') == $f->id ? 'selected' : '' }}>
                        {{ $f->ragione_sociale }}
                    </option>
                @endforeach
            </select>

            @if(request()->hasAny(['q', 'stato', 'fornitore_id', 'rda_id']))
                <a href="{{ route('oda.index') }}" class="btn btn-outline-secondary btn-sm" title="Reimposta filtri">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabella ODA --}}
    <div class="oda-table-card">
        <div class="table-responsive">
            <table class="table-oda">
                <thead>
                    <tr>
                        <th style="width: 110px;">N° ODA</th>
                        <th style="width: 105px;">Data Ordine</th>
                        <th>Fornitore</th>
                        <th>Beni / Servizi Richiesti</th>
                        <th style="width: 120px;" class="text-end">Imponibile</th>
                        <th style="width: 120px;" class="text-center">Origine RDA</th>
                        <th style="width: 140px;" class="text-center">Stato</th>
                        <th style="width: 140px;" class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($odaList as $o)
                        <tr>
                            <td>
                                <a href="{{ route('oda.show', $o) }}" class="fw-bold text-danger text-decoration-none fs-6">
                                    {{ $o->numero }}
                                </a>
                                @if($o->commessa)
                                    <div class="small text-muted font-monospace">Com: {{ $o->commessa->codice }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="text-dark fw-semibold">
                                    {{ $o->data_ordine ? $o->data_ordine->format('d/m/Y') : '—' }}
                                </span>
                                @if($o->data_consegna_prevista)
                                    <div class="small text-muted" title="Consegna prevista">
                                        <i class="bi bi-truck me-0.5"></i>{{ $o->data_consegna_prevista->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($o->fornitore)
                                    <a href="{{ route('fornitori.show', $o->fornitore) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $o->fornitore->ragione_sociale }}
                                    </a>
                                    <div class="small text-muted">
                                        Cod. {{ $o->fornitore->codice ?: 'N/D' }}
                                        @if($o->fornitore->stato_qualifica === 'qualificato')
                                            <span class="badge bg-success-subtle text-success ms-1">Qualificato</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">— Fornitore eliminato —</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $primaRiga = $o->righe->first();
                                    $altreCount = $o->righe->count() - 1;
                                @endphp
                                @if($primaRiga)
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 280px;" title="{{ $primaRiga->descrizione_bene }}">
                                        {{ $primaRiga->descrizione_bene }}
                                    </div>
                                    @if($altreCount > 0)
                                        <span class="badge bg-light text-secondary border mt-0.5">
                                            + altri {{ $altreCount }} articoli
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">— Nessun articolo —</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark fs-6">
                                    € {{ number_format($o->totale_imponibile, 2, ',', '.') }}
                                </span>
                                <div class="small text-muted">IVA {{ number_format($o->aliquota_iva, 0) }}%</div>
                            </td>
                            <td class="text-center">
                                @if($o->rda)
                                    <a href="{{ route('rda.show', $o->rda) }}" class="badge bg-light text-dark border text-decoration-none fw-bold" title="Visualizza RDA di origine">
                                        <i class="bi bi-link-45deg me-0.5"></i>RDA {{ $o->rda->numero }}
                                    </a>
                                @else
                                    <span class="text-muted small">Manuale</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge-oda-stato badge-stato-{{ $o->stato }}">
                                    <i class="bi bi-circle-fill" style="font-size: 0.45rem;"></i>
                                    {{ $o->stato_label }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1 justify-content-end" style="white-space: nowrap;">
                                    {{-- Visualizza --}}
                                    <a href="{{ route('oda.show', $o) }}" class="btn-action view" title="Visualizza ODA">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Esporta Word --}}
                                    <a href="{{ route('oda.downloadDocx', $o) }}" class="btn-action word" title="Scarica Documento Ufficiale MD PG 04/02-00">
                                        <i class="bi bi-file-earmark-word"></i>
                                    </a>

                                    {{-- Modifica --}}
                                    <a href="{{ route('oda.edit', $o) }}" class="btn-action edit" title="Modifica ODA">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Elimina --}}
                                    <form method="POST" action="{{ route('oda.destroy', $o) }}" 
                                          onsubmit="return confirm('Sei sicuro di voler eliminare l\'ODA {{ $o->numero }}? Questa operazione cancellerà anche la fornitura registrata.');" 
                                          style="display: inline-block; margin: 0; padding: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action del" title="Elimina ODA">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-bag-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <strong>Nessun Ordine di Acquisto trovato</strong>
                                <p class="small mb-3">Non ci sono ordini corrispondenti ai filtri impostati.</p>
                                <a href="{{ route('oda.create') }}" class="btn btn-sm btn-danger">
                                    <i class="bi bi-plus-circle me-1"></i> Crea il Primo ODA
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($odaList->hasPages())
            <div class="p-3 border-top">
                {{ $odaList->links() }}
            </div>
        @endif
    </div>
@endsection
