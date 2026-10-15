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

        /* Griglia dati estremi */
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
            width: 25%;
            background: #fafbfc;
            color: #475569;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-right: 1px solid #f1f5f9;
        }

        /* Tabella Storico Forniture (Tabella 2 Word) */
        .table-forniture {
            width: 100%;
            margin-bottom: 0;
            font-size: 0.88rem;
        }
        .table-forniture thead th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.9rem 1.1rem;
            border-bottom: 2px solid #cbd5e1;
            text-align: center;
        }
        .table-forniture thead th:nth-child(2) {
            text-align: left;
        }
        .table-forniture tbody td {
            padding: 0.9rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            text-align: center;
        }
        .table-forniture tbody td:nth-child(2) {
            text-align: left;
        }
        .table-forniture tbody tr:hover {
            background: #fbfcfd;
        }

        .badge-status-qual {
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .badge-status-qual.qualificato { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-status-qual.in_valutazione { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-status-qual.sospeso { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-status-qual.non_qualificato { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

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

    {{-- ── HEADER SCHEDA FORNITORE ── --}}
    <div class="doc-header-card">
        <div>
            <div class="doc-proc-code">
                <i class="bi bi-file-earmark-text me-1"></i> PROCEDURA MD PG04/04-00 &bull; SCHEDA FORNITORE N° {{ $fornitore->codice ?? str_pad($fornitore->id, 3, '0', STR_PAD_LEFT) }}
            </div>
            <h1 class="doc-title">{{ $fornitore->ragione_sociale }}</h1>
            <p class="text-white-50 small mb-0 mt-1">
                <i class="bi bi-geo-alt me-1"></i> {{ $fornitore->indirizzo_completo }}
                @if($fornitore->partita_iva) &bull; P.IVA: <span class="mono">{{ $fornitore->partita_iva }}</span> @endif
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @php
                $lblStato = [
                    'qualificato'    => 'Fornitore Qualificato',
                    'in_valutazione' => 'In Valutazione',
                    'sospeso'        => 'Qualifica Sospesa',
                    'non_qualificato'=> 'Non Qualificato'
                ][$fornitore->stato_qualifica] ?? ucfirst($fornitore->stato_qualifica);
            @endphp
            <div class="badge-status-qual {{ $fornitore->stato_qualifica }} me-2">
                <i class="bi bi-patch-check-fill fs-6"></i> {{ $lblStato }}
            </div>

            <button type="button" class="btn btn-outline-light fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCambiaQualifica">
                <i class="bi bi-sliders me-1"></i> Stato Qualifica
            </button>

            <a href="{{ route('fornitori.downloadWord', $fornitore) }}" class="btn btn-primary fw-semibold shadow-sm" title="Scarica Scheda Word ufficiale MD PG04/04-00">
                <i class="bi bi-file-earmark-word me-1"></i> Esporta Word (.doc)
            </a>

            <a href="{{ route('fornitori.edit', $fornitore) }}" class="btn btn-outline-light fw-semibold">
                <i class="bi bi-pencil me-1"></i> Modifica
            </a>

            <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary text-white fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Elenco
            </a>
        </div>
    </div>

    {{-- Alert se fornitore non qualificato o inattivo --}}
    @if($fornitore->stato_qualifica !== 'qualificato')
        <div class="alert alert-warning border-warning d-flex align-items-center justify-content-between p-3 rounded-3 mb-4 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
                <div>
                    <h6 class="mb-1 fw-bold text-dark">Attenzione: Fornitore {{ $lblStato }}</h6>
                    <div class="small text-muted">
                        @if($ultimaDataAcquisto)
                            Ultimo acquisto/ordine registrato: <strong>{{ $ultimaDataAcquisto->format('d/m/Y') }}</strong> ({{ $ultimaDataAcquisto->diffForHumans() }}).
                        @else
                            Nessun acquisto registrato a sistema. Data ammissione: {{ $fornitore->data_qualifica ? $fornitore->data_qualifica->format('d/m/Y') : 'non specificata' }}.
                        @endif
                        @php
                            $ultimoStorico = $fornitore->storicoQualifica->first();
                        @endphp
                        @if($ultimoStorico && $ultimoStorico->motivo)
                            <div class="mt-1 text-dark"><strong>Motivazione registrata:</strong> {{ $ultimoStorico->motivo }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-dark fw-semibold text-nowrap ms-3" data-bs-toggle="modal" data-bs-target="#modalCambiaQualifica">
                <i class="bi bi-patch-check me-1"></i> Riqualifica / Cambia Stato
            </button>
        </div>
    @endif

    {{-- ── 1. ESTREMI DEL FORNITORE (TABELLA 1) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-building text-danger"></i> 1. Estremi del Fornitore
            </h3>
            <span class="badge bg-light text-muted border mono">Codice: {{ $fornitore->codice ?: 'N/D' }}</span>
        </div>

        <div class="table-responsive">
            <table class="grid-data-table">
                <tbody>
                    <tr>
                        <th>Fornitore / Ragione Sociale</th>
                        <td class="fw-bold fs-6 text-dark">{{ $fornitore->ragione_sociale }}</td>
                        <th>Telefono / Cellulare</th>
                        <td>
                            @if($fornitore->telefono)
                                <a href="tel:{{ $fornitore->telefono }}" class="text-decoration-none fw-semibold text-dark"><i class="bi bi-telephone me-1 text-muted"></i>{{ $fornitore->telefono }}</a>
                            @endif
                            @if($fornitore->cellulare)
                                <span class="ms-2 text-muted">&bull; Cell: {{ $fornitore->cellulare }}</span>
                            @endif
                            @if(!$fornitore->telefono && !$fornitore->cellulare) — @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Indirizzo Sede</th>
                        <td>{{ $fornitore->indirizzo_completo }}</td>
                        <th>PEC</th>
                        <td>
                            @if($fornitore->pec)
                                <a href="mailto:{{ $fornitore->pec }}" class="text-decoration-none text-primary fw-semibold"><i class="bi bi-shield-check me-1"></i>{{ $fornitore->pec }}</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>C.F. / Partita IVA</th>
                        <td class="mono fw-bold">
                            {{ $fornitore->partita_iva ?? $fornitore->codice_fiscale ?? '—' }}
                        </td>
                        <th>Email Commerciale / Info</th>
                        <td>
                            @if($fornitore->email)
                                <a href="mailto:{{ $fornitore->email }}" class="text-decoration-none text-dark"><i class="bi bi-envelope me-1 text-muted"></i>{{ $fornitore->email }}</a>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Sito Internet</th>
                        <td>
                            @if($fornitore->sito_web)
                                @php
                                    $url = Str::startsWith($fornitore->sito_web, ['http://', 'https://']) ? $fornitore->sito_web : 'https://' . $fornitore->sito_web;
                                @endphp
                                <a href="{{ $url }}" target="_blank" class="text-decoration-none text-primary fw-semibold">
                                    <i class="bi bi-globe me-1"></i>{{ $fornitore->sito_web }} <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            @else
                                —
                            @endif
                        </td>
                        <th>Referente Tecnico/Commerciale</th>
                        <td>{{ $fornitore->referente ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th>Prodotti Acquistati</th>
                        <td colspan="3" class="fw-semibold text-dark">
                            {{ $fornitore->prodotti_acquistati ?: '—' }}
                        </td>
                    </tr>
                    <tr>
                        <th>Criteri di Qualifica</th>
                        <td colspan="3">
                            <span class="badge bg-light text-dark border px-2 py-1 fs-6">
                                <i class="bi bi-check2-circle text-success me-1"></i>
                                {{ $fornitore->criteri_qualifica ?: 'Requisiti tecnici del Metodo di prova' }}
                            </span>
                        </td>
                    </tr>
                    @if($fornitore->data_qualifica || $fornitore->data_scadenza_qualifica || $fornitore->note)
                        <tr>
                            <th>Data Qualifica &amp; Validità</th>
                            <td>
                                Data ammissione: <strong>{{ $fornitore->data_qualifica ? $fornitore->data_qualifica->format('d/m/Y') : '—' }}</strong>
                                @if($fornitore->data_scadenza_qualifica)
                                    &bull; Scadenza: <strong class="text-warning mono">{{ $fornitore->data_scadenza_qualifica->format('d/m/Y') }}</strong>
                                @endif
                            </td>
                            <th>Note Generali</th>
                            <td>{{ $fornitore->note ?: '—' }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 2. TABELLA STORICO FORNITURE & MONITORAGGIO QUALITÀ (TABELLA 2 WORD) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <div>
                <h3 class="section-card-title">
                    <i class="bi bi-card-checklist text-primary"></i> 2. Storico Forniture e Monitoraggio Qualità (Reclami / RNC / RAC)
                </h3>
                <p class="text-muted small mb-0">Tracciamento delle forniture erogate, eventuali non conformità e azioni correttive (Tabella 2 MD PG04/04-00)</p>
            </div>

            <button type="button" class="btn btn-sm btn-danger fw-semibold px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalNuovaFornitura">
                <i class="bi bi-plus-lg me-1"></i> Registra Fornitura / Reclamo
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-forniture">
                <thead>
                    <tr>
                        <th style="width: 140px;">Data Inizio Fornitura</th>
                        <th>Descrizione Prodotto / Servizio</th>
                        <th style="width: 120px;">N° Reclamo</th>
                        <th style="width: 120px;">N° RNC</th>
                        <th style="width: 120px;">N° RAC</th>
                        <th style="width: 130px;">Esito</th>
                        <th style="width: 100px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fornitore->forniture as $item)
                        <tr>
                            <td class="mono fw-semibold">{{ $item->data_inizio_fornitura ?: '—' }}</td>
                            <td>
                                <div class="fw-bold text-dark d-flex align-items-center gap-2 flex-wrap">
                                    <span>{{ $item->descrizione_prodotto_servizio }}</span>
                                    @if($item->oda)
                                        <a href="{{ route('oda.show', $item->oda) }}" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none">
                                            <i class="bi bi-bag-check me-0.5"></i>ODA {{ $item->oda->numero }}
                                        </a>
                                    @endif
                                </div>
                                @if($item->note)
                                    <div class="text-muted small italic">{{ $item->note }}</div>
                                @endif
                            </td>
                            <td>
                                @if($item->numero_reclamo && $item->numero_reclamo !== '-')
                                    <span class="badge bg-danger text-white mono fw-bold px-2 py-1">{{ $item->numero_reclamo }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->numero_rnc && $item->numero_rnc !== '-')
                                    <span class="badge bg-warning text-dark mono fw-bold px-2 py-1">{{ $item->numero_rnc }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->numero_rac && $item->numero_rac !== '-')
                                    <span class="badge bg-primary text-white mono fw-bold px-2 py-1">{{ $item->numero_rac }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $esitoClass = [
                                        'positivo'     => 'bg-success-subtle text-success border border-success-subtle',
                                        'reclamo'      => 'bg-danger text-white',
                                        'non_conforme' => 'bg-warning text-dark'
                                    ][$item->esito] ?? 'bg-light text-muted';
                                @endphp
                                <span class="badge {{ $esitoClass }} px-2 py-1">
                                    {{ ucfirst(str_replace('_', ' ', $item->esito)) }}
                                </span>
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div class="d-inline-flex align-items-center gap-1 justify-content-center">
                                    <button type="button" class="btn-action edit" data-bs-toggle="modal" data-bs-target="#modalEditFornitura{{ $item->id }}" title="Modifica Riga">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('fornitori.forniture.destroy', [$fornitore, $item->id]) }}" method="POST" class="d-inline m-0 p-0" data-confirm="Vuoi eliminare questa riga di fornitura?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action del" title="Elimina Riga">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL MODIFICA FORNITURA --}}
                        <div class="modal fade" id="modalEditFornitura{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('fornitori.forniture.update', [$fornitore, $item->id]) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Modifica Riga Fornitura / Reclamo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Data Inizio Fornitura</label>
                                                    <input type="text" name="data_inizio_fornitura" class="form-control" value="{{ $item->data_inizio_fornitura }}" required>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Esito Valutazione</label>
                                                    <select name="esito" class="form-select">
                                                        <option value="positivo" {{ $item->esito === 'positivo' ? 'selected' : '' }}>Positivo / Conforme</option>
                                                        <option value="reclamo" {{ $item->esito === 'reclamo' ? 'selected' : '' }}>Reclamo Fornitore</option>
                                                        <option value="non_conforme" {{ $item->esito === 'non_conforme' ? 'selected' : '' }}>Non Conforme (RNC aperta)</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Descrizione Prodotto / Servizio</label>
                                                    <textarea name="descrizione_prodotto_servizio" class="form-control" rows="2" required>{{ $item->descrizione_prodotto_servizio }}</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">N° Reclamo</label>
                                                    <input type="text" name="numero_reclamo" class="form-control" value="{{ $item->numero_reclamo }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">N° RNC (Non Conformità)</label>
                                                    <input type="text" name="numero_rnc" class="form-control" value="{{ $item->numero_rnc }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">N° RAC (Azione Correttiva)</label>
                                                    <input type="text" name="numero_rac" class="form-control" value="{{ $item->numero_rac }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Note</label>
                                                    <input type="text" name="note" class="form-control" value="{{ $item->note }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                            <button type="submit" class="btn btn-danger fw-semibold">Salva Modifiche</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-box-seam fs-2 d-block mb-1 opacity-50"></i>
                                <span>Nessuna fornitura o reclamo ancora registrato per questo fornitore.</span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalNuovaFornitura">
                                        + Aggiungi la prima fornitura
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 3. ORDINI DI ACQUISTO (ODA) EMESSI VERSO QUESTO FORNITORE ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <div>
                <h3 class="section-card-title">
                    <i class="bi bi-bag-check text-danger"></i> 3. Ordini di Acquisto Emessi (MD PG 04/02-00)
                </h3>
                <p class="text-muted small mb-0">Riepilogo ordini ODA intestati a questo fornitore</p>
            </div>
            <a href="{{ route('oda.create', ['fornitore_id' => $fornitore->id]) }}" class="btn btn-sm btn-danger fw-semibold px-3 py-1">
                <i class="bi bi-plus-lg me-1"></i> Nuovo ODA per questo Fornitore
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-forniture">
                <thead>
                    <tr>
                        <th style="width: 120px;">N° ODA</th>
                        <th style="width: 110px;">Data</th>
                        <th>Articoli Ordinati</th>
                        <th style="width: 130px;" class="text-end">Imponibile</th>
                        <th style="width: 140px;">Stato</th>
                        <th style="width: 80px;" class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fornitore->ordiniAcquisto as $odaItem)
                        <tr>
                            <td>
                                <a href="{{ route('oda.show', $odaItem) }}" class="fw-bold text-danger text-decoration-none">
                                    {{ $odaItem->numero }}
                                </a>
                            </td>
                            <td>{{ $odaItem->data_ordine ? $odaItem->data_ordine->format('d/m/Y') : '—' }}</td>
                            <td class="text-start">
                                {{ Str::limit($odaItem->righe->pluck('descrizione_bene')->implode(', '), 60) }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                € {{ number_format($odaItem->totale_imponibile, 2, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $odaItem->stato_label }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('oda.show', $odaItem) }}" class="btn-action view" title="Visualizza ODA">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Nessun Ordine di Acquisto (ODA) ancora emesso per questo fornitore.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 4. DOCUMENTI ALLEGATI (CERTIFICATI ISO 9001 / ACCREDITAMENTI / CONTRATTI) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <h3 class="section-card-title">
                <i class="bi bi-paperclip text-info"></i> 4. Documenti &amp; Certificati di Qualità Allegati
            </h3>
            <span class="badge bg-light text-muted border">{{ $documenti->count() }} file</span>
        </div>

        <div class="p-3">
            @if($documenti->isNotEmpty())
                <div class="row g-2 mb-3">
                    @foreach($documenti as $doc)
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark text-truncate small" title="{{ $doc->name }}">{{ $doc->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $doc->human_readable_size }} &bull; {{ $doc->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ $doc->getUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Scarica">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('fornitori.media.destroy', [$fornitore, $doc->id]) }}" method="POST" class="d-inline" data-confirm="Eliminare il documento {{ $doc->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Form Upload Rapido File --}}
            <form method="POST" action="{{ route('fornitori.update', $fornitore) }}" enctype="multipart/form-data" class="d-flex align-items-center gap-2 mt-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="ragione_sociale" value="{{ $fornitore->ragione_sociale }}">
                <input type="hidden" name="stato_qualifica" value="{{ $fornitore->stato_qualifica }}">
                <input type="file" name="documenti[]" class="form-control form-control-sm" multiple required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="max-width: 400px;">
                <button type="submit" class="btn btn-sm btn-dark fw-semibold">
                    <i class="bi bi-upload me-1"></i> Carica Documento
                </button>
            </form>
        </div>
    </div>

    {{-- ── 5. STORICO QUALIFICA & VARIAZIONI DI STATO (TRACCIABILITÀ MD PG04) ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <div>
                <h3 class="section-card-title">
                    <i class="bi bi-clock-history text-danger"></i> 5. Storico Qualifica &amp; Variazioni di Stato (Tracciabilità MD PG04)
                </h3>
                <p class="text-muted small mb-0">Registro cronologico di ogni modifica dello stato di qualifica (automatico per inattività o manuale dagli operatori).</p>
            </div>

            <button type="button" class="btn btn-sm btn-outline-dark fw-semibold px-3 py-1" data-bs-toggle="modal" data-bs-target="#modalCambiaQualifica">
                <i class="bi bi-pencil me-1"></i> Registra Variazione Stato
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 160px;">Data &amp; Ora</th>
                        <th style="width: 140px;">Stato Prec.</th>
                        <th style="width: 150px;">Nuovo Stato</th>
                        <th style="width: 170px;">Tipo Evento</th>
                        <th style="width: 160px;">Operatore</th>
                        <th>Motivazione / Dettagli Procedura</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fornitore->storicoQualifica as $st)
                        <tr>
                            <td class="mono fw-semibold">{{ $st->data_variazione ? $st->data_variazione->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                @if($st->stato_precedente)
                                    <span class="badge {{ $st->getBadgeClass($st->stato_precedente) }}">
                                        {{ $st->stato_precedente_label }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $st->getBadgeClass($st->stato_nuovo) }}">
                                    {{ $st->stato_nuovo_label }}
                                </span>
                            </td>
                            <td>
                                @if($st->tipo_variazione === 'automatica')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-robot me-1"></i> Automatica (Inattività)
                                    </span>
                                @elseif($st->tipo_variazione === 'creazione')
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="bi bi-plus-circle me-1"></i> Creazione
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis border">
                                        <i class="bi bi-person me-1"></i> Manuale
                                    </span>
                                @endif
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $st->user?->name ?? ($st->tipo_variazione === 'automatica' ? 'Sistema Automatico' : '—') }}
                            </td>
                            <td>
                                <div class="text-dark">{{ $st->motivo ?: '—' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Nessuna variazione di qualifica ancora registrata.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── MODAL CAMBIA STATO QUALIFICA ── --}}
    <div class="modal fade" id="modalCambiaQualifica" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('fornitori.cambiaStatoQualifica', $fornitore) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-sliders text-danger me-1"></i> Variazione Stato Qualifica Fornitore
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Stato Qualifica Attuale</label>
                            <div>
                                <span class="badge {{ $fornitore->stato_qualifica === 'qualificato' ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $lblStato }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nuovo Stato di Qualifica <span class="text-danger">*</span></label>
                            <select name="stato_qualifica" class="form-select" required>
                                <option value="qualificato" {{ $fornitore->stato_qualifica === 'qualificato' ? 'selected' : '' }}>Qualificato</option>
                                <option value="in_valutazione" {{ $fornitore->stato_qualifica === 'in_valutazione' ? 'selected' : '' }}>In Valutazione</option>
                                <option value="sospeso" {{ $fornitore->stato_qualifica === 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                                <option value="non_qualificato" {{ $fornitore->stato_qualifica === 'non_qualificato' ? 'selected' : '' }}>Non Qualificato</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Motivazione della Variazione (Tracciata a Registro) <span class="text-danger">*</span></label>
                            <textarea name="motivo_variazione" class="form-control" rows="3" placeholder="Es. Riqualificazione a seguito di riesame fornitore / Nuova fornitura conforme / Provvedimento qualità..." required></textarea>
                            <div class="form-text">
                                Questa motivazione verrà registrata in modo permanente nello storico della procedura MD PG04 con data, ora e operatore.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-danger fw-semibold">Conferma e Registra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ── MODAL AGGIUNGI FORNITURA ── --}}
    <div class="modal fade" id="modalNuovaFornitura" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('fornitori.forniture.store', $fornitore) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Registra Fornitura / Reclamo / RNC</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Data Inizio Fornitura <span class="text-danger">*</span></label>
                                <input type="text" name="data_inizio_fornitura" class="form-control" placeholder="es. 09/2024 o 15/09/2024" value="{{ date('m/Y') }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Esito Valutazione</label>
                                <select name="esito" class="form-select">
                                    <option value="positivo" selected>Positivo / Conforme</option>
                                    <option value="reclamo">Reclamo Fornitore</option>
                                    <option value="non_conforme">Non Conforme (Apertura RNC)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Descrizione Prodotto / Servizio <span class="text-danger">*</span></label>
                                <textarea name="descrizione_prodotto_servizio" class="form-control" rows="2" placeholder="es. accessori ausiliari per prove, materiale di consumo..." required></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">N° Reclamo</label>
                                <input type="text" name="numero_reclamo" class="form-control" placeholder="es. -" value="-">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">N° RNC (Non Conformità)</label>
                                <input type="text" name="numero_rnc" class="form-control" placeholder="es. -" value="-">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">N° RAC (Azione Correttiva)</label>
                                <input type="text" name="numero_rac" class="form-control" placeholder="es. -" value="-">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Note / Risoluzione</label>
                                <input type="text" name="note" class="form-control" placeholder="Eventuali annotazioni...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-danger fw-semibold">Salva Fornitura</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
