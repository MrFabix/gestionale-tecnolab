@extends('layouts.app')

@section('content')
    <style>
        .doc-header-card {
            background: linear-gradient(135deg, #111422 0%, #1a1e30 60%, #0d0f18 100%);
            border-radius: 16px;
            padding: 1.6rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .doc-proc-code {
            display: inline-block;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 0.25rem 0.65rem;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }

        .doc-title {
            font-size: 1.7rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .section-card-header {
            padding: 1.1rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .section-card-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Griglia dati estremi (identica a Scheda Fornitore / Commessa) */
        .grid-data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grid-data-table th, .grid-data-table td {
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }
        .grid-data-table th {
            width: 22%;
            background: #fafbfc;
            color: #475569;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-right: 1px solid #f1f5f9;
        }

        /* Tabella Articoli Richiesti */
        .table-righe-elenco {
            width: 100%;
            margin-bottom: 0;
            font-size: 0.88rem;
        }
        .table-righe-elenco thead th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.9rem 1.1rem;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-righe-elenco tbody td {
            padding: 0.9rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .table-righe-elenco tbody tr:hover {
            background: #fbfcfd;
        }

        .badge-status-pill {
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }
        .badge-status-pill.in_attesa { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-status-pill.approvata { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-status-pill.ordinata  { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-status-pill.rifiutata { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-status-pill.bozza     { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .row-num-badge {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 800;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    {{-- ── HEADER SCHEDA RDA (COMPLETO E MODERNO) ── --}}
    <div class="doc-header-card">
        <div>
            <div class="doc-proc-code">
                <i class="bi bi-file-earmark-text me-1"></i> PROCEDURA MD PG04/01-00 &bull; RICHIESTA DI ACQUISTO N° {{ $rda->numero }}
            </div>
            <h1 class="doc-title">RDA {{ $rda->numero }}</h1>
            <p class="text-white-50 small mb-0 mt-1">
                <i class="bi bi-person-circle me-1"></i> Richiedente: <span class="text-white fw-bold">{{ $rda->richiedente }}</span>
                &bull; Data: <span class="text-white fw-bold">{{ $rda->data_richiesta ? $rda->data_richiesta->format('d/m/Y') : '—' }}</span>
                @if($rda->data_approvvigionamento)
                    &bull; Consegna richiesta entro: <span class="text-warning fw-bold">{{ $rda->data_approvvigionamento->format('d/m/Y') }}</span>
                @endif
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Badge Stato --}}
            <div class="badge-status-pill {{ $rda->stato }} me-2">
                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                {{ $rda->stato_label }}
            </div>

            {{-- Bottone Trasforma in ODA --}}
            @if($rda->stato !== 'rifiutata')
                <a href="{{ route('rda.generaOda', $rda) }}" class="btn btn-danger fw-bold shadow-sm d-inline-flex align-items-center gap-2 px-3 py-2" title="Genera uno o più Ordini di Acquisto da questa RDA">
                    <i class="bi bi-cart-plus-fill fs-5"></i> Trasforma in ODA
                </a>
            @endif

            {{-- Esporta Word --}}
            <a href="{{ route('rda.downloadWord', $rda) }}" class="btn btn-primary fw-semibold shadow-sm" title="Scarica modello Word ufficiale MD PG04/01-00">
                <i class="bi bi-file-earmark-word me-1"></i> Esporta Word (.docx)
            </a>

            {{-- Modifica --}}
            <a href="{{ route('rda.edit', $rda) }}" class="btn btn-outline-light fw-semibold">
                <i class="bi bi-pencil me-1"></i> Modifica
            </a>

            {{-- Torna a Elenco --}}
            <a href="{{ route('rda.index') }}" class="btn btn-outline-secondary text-white fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Elenco
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── 1. ESTREMI DELLA RICHIESTA (TABELLA GRIGLIA) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-card-checklist text-danger"></i> 1. Estremi della Richiesta di Acquisto
            </h3>
            <div class="d-flex align-items-center gap-2">
                {{-- Dropdown Cambio Stato Rapido --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-dark dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-arrow-left-right me-1"></i> Aggiorna Stato
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <form method="POST" action="{{ route('rda.cambiaStato', $rda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="in_attesa">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $rda->stato == 'in_attesa' ? 'active' : '' }}">
                                    <span class="badge bg-warning text-dark">&bull;</span> In Attesa
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('rda.cambiaStato', $rda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="approvata">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $rda->stato == 'approvata' ? 'active' : '' }}">
                                    <span class="badge bg-success">&bull;</span> Approvata
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('rda.cambiaStato', $rda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="ordinata">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $rda->stato == 'ordinata' ? 'active' : '' }}">
                                    <span class="badge bg-info text-dark">&bull;</span> Ordinata (ODA Emesso)
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('rda.cambiaStato', $rda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="rifiutata">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $rda->stato == 'rifiutata' ? 'active' : '' }}">
                                    <span class="badge bg-danger">&bull;</span> Rifiutata
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('rda.cambiaStato', $rda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="bozza">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $rda->stato == 'bozza' ? 'active' : '' }}">
                                    <span class="badge bg-secondary">&bull;</span> Riporta a Bozza
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                {{-- Elimina --}}
                <form method="POST" action="{{ route('rda.destroy', $rda) }}" onsubmit="return confirm('Sei sicuro di voler eliminare la RDA {{ $rda->numero }}? Questa operazione è irreversibile.');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina RDA">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="grid-data-table">
                <tbody>
                    <tr>
                        <th>N° Richiesta (RDA)</th>
                        <td class="fw-bold fs-6 text-danger">{{ $rda->numero }}</td>
                        <th>Stato Corrente</th>
                        <td>
                            <span class="badge-status-pill {{ $rda->stato }}">
                                <i class="bi bi-circle-fill" style="font-size: 0.45rem;"></i>
                                {{ $rda->stato_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Richiedente</th>
                        <td class="fw-bold text-dark">
                            <i class="bi bi-person me-1 text-muted"></i>{{ $rda->richiedente }}
                        </td>
                        <th>Data Richiesta</th>
                        <td>
                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                            {{ $rda->data_richiesta ? $rda->data_richiesta->format('d/m/Y') : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Data Approvvigionamento</th>
                        <td>
                            @if($rda->data_approvvigionamento)
                                <span class="fw-semibold text-danger">
                                    <i class="bi bi-clock-history me-1"></i>{{ $rda->data_approvvigionamento->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">— Nessuna data prefissata —</span>
                            @endif
                        </td>
                        <th>Operatore Sistema</th>
                        <td>
                            @if($rda->user)
                                <span><i class="bi bi-person-badge me-1 text-muted"></i>{{ $rda->user->name }}</span>
                                <span class="text-muted small">({{ $rda->user->email }})</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Creazione Scheda</th>
                        <td class="text-muted small">
                            {{ $rda->created_at->format('d/m/Y H:i') }}
                        </td>
                        <th>Ultimo Aggiornamento</th>
                        <td class="text-muted small">
                            {{ $rda->updated_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 2. PRODOTTI / SERVIZI RICHIESTI (TABELLA MODERNA) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-boxes text-danger"></i> 2. Prodotti / Servizi Richiesti
            </h3>
            <span class="badge bg-light text-muted border fw-bold">{{ $rda->righe->count() }} {{ $rda->righe->count() === 1 ? 'articolo' : 'articoli' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table-righe-elenco">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Descrizione Prodotto / Servizio</th>
                        <th style="width: 170px;">Codice Prodotto</th>
                        <th style="width: 140px;" class="text-center">Quantità</th>
                        <th style="min-width: 260px;">Fornitore Consigliato</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rda->righe as $idx => $riga)
                        <tr>
                            <td class="text-center">
                                <span class="row-num-badge">{{ $idx + 1 }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $riga->descrizione_prodotto }}</div>
                                @if($riga->note)
                                    <div class="small text-muted mt-1 fst-italic">
                                        <i class="bi bi-info-circle me-1 text-primary"></i>{{ $riga->note }}
                                    </div>
                                @endif
                            </td>
                            <td class="font-monospace text-secondary">
                                {{ $riga->codice_prodotto ?: '—' }}
                            </td>
                            <td class="text-center">
                                <span class="fw-bold fs-6 text-dark">{{ number_format($riga->quantita, 2, ',', '.') }}</span>
                                <span class="badge bg-light text-muted border ms-1">{{ $riga->unita_misura ?: 'pz' }}</span>
                            </td>
                            <td>
                                @if($riga->fornitore)
                                    <div>
                                        <a href="{{ route('fornitori.show', $riga->fornitore) }}" class="fw-bold text-dark text-decoration-none d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-building text-danger"></i> {{ $riga->fornitore->ragione_sociale }}
                                        </a>
                                    </div>
                                    <div class="small text-muted mt-0.5">
                                        Cod. {{ $riga->fornitore->codice ?? 'N/D' }}
                                        @if($riga->fornitore->stato_qualifica === 'qualificato')
                                            <span class="badge bg-success-subtle text-success ms-1">Qualificato</span>
                                        @elseif($riga->fornitore->stato_qualifica === 'da_qualificare')
                                            <span class="badge bg-warning-subtle text-warning ms-1">Da Qualificare</span>
                                        @endif
                                    </div>
                                @elseif($riga->fornitore_consigliato_testo)
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-shop text-muted me-1"></i>{{ $riga->fornitore_consigliato_testo }}
                                    </div>
                                    <span class="badge bg-light text-muted border mt-0.5">Non censito</span>
                                @else
                                    <span class="text-muted fst-italic">— Nessuno specificato —</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Nessun articolo o servizio presente in questa richiesta.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 3. NOTE GENERALI ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-chat-left-dots text-danger"></i> 3. Note e Indicazioni
            </h3>
        </div>
        <div class="p-3">
            @if($rda->note)
                <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line; font-size: 0.92rem;">
                    {{ $rda->note }}
                </div>
            @else
                <p class="text-muted fst-italic mb-0 small">
                    Nessuna nota aggiuntiva allegata alla Richiesta di Acquisto.
                </p>
            @endif
        </div>
    </div>

    {{-- ── 4. STATO APPROVAZIONE & AVANZAMENTO VERSO ODA ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-diagram-3 text-danger"></i> 4. Approvazione & Flusso Ordine di Acquisto (ODA)
            </h3>
        </div>
        <div class="p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    @if($rda->stato === 'in_attesa')
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                                <i class="bi bi-hourglass-split fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">In attesa di approvazione da parte della Direzione / Responsabile Acquisti</h6>
                                <p class="text-muted small mb-0">La RDA deve essere approvata prima di poter emettere l'Ordine di Acquisto a fornitore.</p>
                            </div>
                        </div>
                    @elseif($rda->stato === 'approvata')
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                                <i class="bi bi-check2-circle fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-success">Richiesta di Acquisto Approvata</h6>
                                <p class="text-muted small mb-0">La richiesta è pronta per essere convertita in Ordine di Acquisto (ODA).</p>
                            </div>
                        </div>
                    @elseif($rda->stato === 'ordinata')
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                                <i class="bi bi-cart-check fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Ordine di Acquisto (ODA) Emesso</h6>
                                <p class="text-muted small mb-0">Il materiale è stato ordinato al fornitore.</p>
                            </div>
                        </div>
                    @elseif($rda->stato === 'rifiutata')
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger">
                                <i class="bi bi-x-circle fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-danger">Richiesta di Acquisto Respinta</h6>
                                <p class="text-muted small mb-0">Questa RDA è stata respinta e non può essere ordinata.</p>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-secondary bg-opacity-10 p-3 text-secondary">
                                <i class="bi bi-pencil-square fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">RDA in stato di Bozza</h6>
                                <p class="text-muted small mb-0">Inoltra la richiesta portandola nello stato "In Attesa" per sottoporla ad approvazione.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4 text-md-end">
                    @if($rda->stato === 'in_attesa')
                        <form method="POST" action="{{ route('rda.cambiaStato', $rda) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="stato" value="approvata">
                            <button type="submit" class="btn btn-success fw-bold px-3 py-2">
                                <i class="bi bi-check-lg me-1"></i> Approva Richiesta
                            </button>
                        </form>
                    @endif

                    @if($rda->stato !== 'rifiutata')
                        <a href="{{ route('rda.generaOda', $rda) }}" class="btn btn-danger fw-bold px-3 py-2 d-inline-flex align-items-center gap-2">
                            <i class="bi bi-cart-plus fs-5"></i> Trasforma in ODA
                        </a>
                    @endif
                </div>
            </div>

            {{-- Ordini ODA già emessi per questa RDA --}}
            @if($rda->ordiniAcquisto && $rda->ordiniAcquisto->count() > 0)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-bag-check-fill text-danger me-1"></i> Ordini di Acquisto (ODA) emessi da questa RDA:
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($rda->ordiniAcquisto as $linkedOda)
                            <a href="{{ route('oda.show', $linkedOda) }}" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-2">
                                <span class="fw-bold text-danger">{{ $linkedOda->numero }}</span>
                                <span>&bull; {{ $linkedOda->fornitore->ragione_sociale ?? 'Fornitore' }}</span>
                                <span class="badge bg-secondary">{{ $linkedOda->stato_label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
