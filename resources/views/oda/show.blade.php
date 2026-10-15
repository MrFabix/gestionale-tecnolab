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
        .badge-status-pill.inviato    { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-status-pill.confermato { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-status-pill.consegnato { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-status-pill.bozza      { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-status-pill.annullato  { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

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

    {{-- ── HEADER SCHEDA ODA ── --}}
    <div class="doc-header-card">
        <div>
            <div class="doc-proc-code">
                <i class="bi bi-file-earmark-text me-1"></i> PROCEDURA MD PG 04/02-00 &bull; ORDINE DI ACQUISTO N° {{ $oda->numero }}
            </div>
            <h1 class="doc-title">ODA {{ $oda->numero }}</h1>
            <p class="text-white-50 small mb-0 mt-1">
                <i class="bi bi-truck me-1"></i> Fornitore: <span class="text-white fw-bold">{{ $oda->fornitore->ragione_sociale ?? '—' }}</span>
                &bull; Data: <span class="text-white fw-bold">{{ $oda->data_ordine ? $oda->data_ordine->format('d/m/Y') : '—' }}</span>
                &bull; Totale: <span class="text-warning fw-bold">€ {{ number_format($oda->totale_imponibile, 2, ',', '.') }}</span>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Badge Stato --}}
            <div class="badge-status-pill {{ $oda->stato }} me-2">
                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                {{ $oda->stato_label }}
            </div>

            {{-- Esporta Word --}}
            <a href="{{ route('oda.downloadDocx', $oda) }}" class="btn btn-primary fw-semibold shadow-sm" title="Scarica Documento Ufficiale MD PG 04/02-00">
                <i class="bi bi-file-earmark-word me-1"></i> Esporta Word (.docx)
            </a>

            {{-- Modifica --}}
            <a href="{{ route('oda.edit', $oda) }}" class="btn btn-outline-light fw-semibold">
                <i class="bi bi-pencil me-1"></i> Modifica
            </a>

            {{-- Torna a Elenco --}}
            <a href="{{ route('oda.index') }}" class="btn btn-outline-secondary text-white fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Elenco ODA
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── 1. ESTREMI DELL'ORDINE & FORNITORE (TABELLA GRIGLIA) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-card-checklist text-danger"></i> 1. Estremi Ordine &amp; Destinazione (MD PG 04/02-00)
            </h3>

            <div class="d-flex align-items-center gap-2">
                {{-- Dropdown Cambio Stato Rapido --}}
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-dark dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-arrow-left-right me-1"></i> Aggiorna Stato
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <form method="POST" action="{{ route('oda.cambiaStato', $oda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="inviato">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $oda->stato == 'inviato' ? 'active' : '' }}">
                                    <span class="badge bg-info">&bull;</span> Inviato al Fornitore
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('oda.cambiaStato', $oda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="confermato">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $oda->stato == 'confermato' ? 'active' : '' }}">
                                    <span class="badge bg-success">&bull;</span> Confermato
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('oda.cambiaStato', $oda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="consegnato">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $oda->stato == 'consegnato' ? 'active' : '' }}">
                                    <span class="badge bg-primary">&bull;</span> Consegnato / Chiuso
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('oda.cambiaStato', $oda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="bozza">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $oda->stato == 'bozza' ? 'active' : '' }}">
                                    <span class="badge bg-secondary">&bull;</span> Bozza
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('oda.cambiaStato', $oda) }}">
                                @csrf
                                <input type="hidden" name="stato" value="annullato">
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 {{ $oda->stato == 'annullato' ? 'active' : '' }}">
                                    <span class="badge bg-danger">&bull;</span> Annulla Ordine
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                {{-- Elimina --}}
                <form method="POST" action="{{ route('oda.destroy', $oda) }}" onsubmit="return confirm('Sei sicuro di voler eliminare l\'ODA {{ $oda->numero }}? Questa operazione è irreversibile.');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina ODA">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="grid-data-table">
                <tbody>
                    <tr>
                        <th>N° Ordine (ODA)</th>
                        <td class="fw-bold fs-6 text-danger">{{ $oda->numero }}</td>
                        <th>Fornitore Intestatario</th>
                        <td>
                            @if($oda->fornitore)
                                <a href="{{ route('fornitori.show', $oda->fornitore) }}" class="fw-bold text-dark text-decoration-none">
                                    <i class="bi bi-building text-danger me-1"></i>{{ $oda->fornitore->ragione_sociale }}
                                </a>
                                <div class="small text-muted">
                                    Cod. {{ $oda->fornitore->codice ?: 'N/D' }}
                                    @if($oda->fornitore->stato_qualifica === 'qualificato')
                                        <span class="badge bg-success-subtle text-success ms-1">Qualificato</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Data Ordine</th>
                        <td class="fw-semibold text-dark">
                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                            {{ $oda->data_ordine ? $oda->data_ordine->format('d/m/Y') : '—' }}
                        </td>
                        <th>Data Consegna Prevista</th>
                        <td>
                            @if($oda->data_consegna_prevista)
                                <span class="fw-semibold text-danger">
                                    <i class="bi bi-truck me-1"></i>{{ $oda->data_consegna_prevista->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">— A vista / Da concordare —</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Destinazione Merci</th>
                        <td class="small">{{ $oda->destinazione }}</td>
                        <th>Condizioni Pagamento</th>
                        <td class="fw-semibold text-dark">{{ $oda->condizioni_pagamento }}</td>
                    </tr>
                    <tr>
                        <th>Resa / Trasporto / Imballo</th>
                        <td>
                            Resa: <strong>{{ $oda->resa }}</strong> &bull; Trasp: <strong>{{ $oda->trasporto }}</strong> &bull; Imb: <strong>{{ $oda->imballo }}</strong>
                        </td>
                        <th>Vs Rif. Offerta / Interno</th>
                        <td>
                            {{ $oda->riferimento_offerta ?: '—' }}
                            @if($oda->riferimento_interno)
                                <span class="text-muted small">({{ $oda->riferimento_interno }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>RDA di Origine</th>
                        <td>
                            @if($oda->rda)
                                <a href="{{ route('rda.show', $oda->rda) }}" class="badge bg-light text-dark border text-decoration-none fw-bold">
                                    <i class="bi bi-link-45deg me-0.5"></i>RDA {{ $oda->rda->numero }} ({{ $oda->rda->richiedente }})
                                </a>
                            @else
                                <span class="text-muted small">Ordine Diretto (Nessuna RDA)</span>
                            @endif
                        </td>
                        <th>Commessa Collegata</th>
                        <td>
                            @if($oda->commessa)
                                <a href="{{ route('commesse.show', $oda->commessa) }}" class="badge bg-light text-primary border text-decoration-none fw-bold">
                                    <i class="bi bi-briefcase me-0.5"></i>{{ $oda->commessa->codice }}
                                </a>
                            @else
                                <span class="text-muted">— Nessuna commessa —</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 2. BENI E SERVIZI ORDINATI (MD PG 04/02-00) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-boxes text-danger"></i> 2. Beni / Servizi Ordinati
            </h3>
            <span class="badge bg-light text-muted border fw-bold">{{ $oda->righe->count() }} {{ $oda->righe->count() === 1 ? 'articolo' : 'articoli' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table-righe-elenco">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Descrizione Beni</th>
                        <th style="width: 150px;">Codice Articolo</th>
                        <th style="width: 80px;" class="text-center">UM</th>
                        <th style="width: 100px;" class="text-center">Q.TÀ</th>
                        <th style="width: 140px;" class="text-end">Costo Unitario</th>
                        <th style="width: 140px;" class="text-end">Totale Riga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($oda->righe as $idx => $r)
                        <tr>
                            <td class="text-center">
                                <span class="row-num-badge">{{ $idx + 1 }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $r->descrizione_bene }}</div>
                                @if($r->note)
                                    <div class="small text-muted mt-1 fst-italic">
                                        <i class="bi bi-info-circle me-1 text-primary"></i>{{ $r->note }}
                                    </div>
                                @endif
                            </td>
                            <td class="font-monospace text-secondary">{{ $r->codice_articolo ?: '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-muted border">{{ $r->unita_misura }}</span>
                            </td>
                            <td class="text-center fw-bold fs-6">
                                {{ number_format($r->quantita, 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                € {{ number_format($r->costo_unitario, 2, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-dark fs-6">
                                € {{ number_format($r->totale_riga, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Nessun bene o servizio presente in questo ordine.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <td colspan="5" class="text-end fw-bold text-muted text-uppercase pt-3">Totale Imponibile:</td>
                        <td colspan="2" class="text-end fw-bold text-dark fs-5 pt-3">€ {{ number_format($oda->totale_imponibile, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-light">
                        <td colspan="5" class="text-end text-muted small">IVA ({{ number_format($oda->aliquota_iva, 0) }}%):</td>
                        <td colspan="2" class="text-end text-muted small">€ {{ number_format($oda->totale_iva, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-light border-top">
                        <td colspan="5" class="text-end fw-black text-danger fs-5 pb-3">TOTALE ORDINE:</td>
                        <td colspan="2" class="text-end fw-black text-danger fs-5 pb-3">€ {{ number_format($oda->totale_ordine, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── 3. NOTE AGGIUNTIVE ── --}}
    @if($oda->note_aggiuntive)
        <div class="section-card">
            <div class="section-card-header">
                <h3 class="section-card-title">
                    <i class="bi bi-chat-left-dots text-danger"></i> 3. Note Aggiuntive per il Fornitore
                </h3>
            </div>
            <div class="p-3">
                <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line; font-size: 0.92rem;">
                    {{ $oda->note_aggiuntive }}
                </div>
            </div>
        </div>
    @endif

    {{-- ── 4. CONDIZIONI GENERALI DI FORNITURA TECNOLAB (MD PG 04/02-00 PAG. 2) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-shield-check text-danger"></i> 4. Condizioni Generali di Fornitura Tecnolab (MD PG 04/02-00)
            </h3>
            <span class="badge bg-light text-muted border">Clausole Contrattuali Ufficiali</span>
        </div>
        <div class="p-4 small text-secondary">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-1">INTESTAZIONE E SPEDIZIONE FATTURA</h6>
                    <p class="mb-3">
                        Tecnolab srl - Sede Legale: Via Lucullo 8 - 00187 Roma - P.I. e C.F. 17160371005<br>
                        Sede Operativa: Via dell'Informatica 3, 00071 - Santa Palomba Pomezia (RM) - Tel 06-89279160 - Email info@tecnolabroma.it
                    </p>

                    <h6 class="fw-bold text-dark mb-1">LOGISTICA</h6>
                    <p class="mb-3">
                        Si prega di avvisare a merce pronta per organizzare ingresso merci.<br>
                        Orari di scarico merci: Lun. - Ven. 8:00-13:00 / 14:00-16:30.
                    </p>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-1">PAGAMENTI &amp; PENALI</h6>
                    <p class="mb-3">
                        Pagamento con le modalità concordate in testata ordine ({{ $oda->condizioni_pagamento }}).<br>
                        Penale applicabile a fronte di mancata osservanza dei tempi di consegna: 10% dell'ordine.
                    </p>

                    <h6 class="fw-bold text-dark mb-1">OBBLIGHI DEL FORNITORE &amp; FORO COMPETENTE</h6>
                    <p class="mb-0">
                        Riportare su ogni fattura il riferimento dell'Ordine, apporre la clausola di non cedibilità. Le certificazioni richieste sono da intendersi in accompagnamento alla fornitura (timbro C.Q.T.). Per qualsiasi controversia sarà competente il Foro di Roma.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 5. AGGIORNAMENTO SCHEDA FORNITORE ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-arrow-repeat text-danger"></i> 5. Tracciamento Automatico Scheda Fornitore (MD PG 04/04-00)
            </h3>
        </div>
        <div class="p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                        <i class="bi bi-patch-check-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">
                            Ordine registrato nello Storico Forniture di {{ $oda->fornitore->ragione_sociale ?? 'Fornitore' }}
                        </h6>
                        <p class="text-muted small mb-0">
                            La fornitura è tracciata in conformità alla procedura di Qualifica Fornitori (Tabella 2 MD PG04/04-00).
                        </p>
                    </div>
                </div>

                @if($oda->fornitore)
                    <a href="{{ route('fornitori.show', $oda->fornitore) }}" class="btn btn-outline-danger fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Apri Scheda Fornitore
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
