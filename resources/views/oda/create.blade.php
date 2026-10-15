@extends('layouts.app')

@section('content')
    <style>
        .form-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .form-card-header {
            padding: 1.1rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .form-card-body { padding: 1.5rem; }
        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.35rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.88rem;
            padding: 0.5rem 0.8rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #d90429;
            box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.12);
        }
        .table-righe-oda {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-righe-oda thead th {
            background: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.75rem 0.85rem;
            border-bottom: 1px solid #cbd5e1;
        }
        .table-righe-oda tbody td {
            padding: 0.75rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #dc2626;
            font-size: 0.9rem;
            transition: all 0.15s ease;
            padding: 0;
        }
        .btn-action:hover {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #f87171;
            transform: translateY(-1px);
        }
        .totali-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">MD PG 04/02-00</span>
                <span class="text-muted small fw-bold text-uppercase">&bull; Emissione Ordine a Fornitore</span>
            </div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-cart-plus text-danger me-2"></i>Nuovo Ordine di Acquisto (ODA)
            </h2>
        </div>
        <a href="{{ route('oda.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Torna all'Elenco ODA
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Correggi i seguenti errori:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('oda.store') }}" id="odaForm">
        @csrf

        @if($rda)
            <input type="hidden" name="rda_id" value="{{ $rda->id }}">
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-link-45deg fs-4"></i>
                <div>
                    Questo ordine è generato a partire dalla <strong>RDA {{ $rda->numero }}</strong> di {{ $rda->richiedente }}.
                </div>
            </div>
        @endif

        {{-- ── CARD 1: DATI TESTATA E FORNITORE ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div>
                    <i class="bi bi-building text-danger me-1"></i> 1. Intestazione &amp; Fornitore (MD PG 04/02-00)
                </div>
                <div class="text-muted small fw-normal">
                    Formato ISO: <span class="fw-bold text-dark">{{ $prossimoNumero }}</span>
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">N° Ordine ODA <span class="text-danger">*</span></label>
                        <input type="text" name="numero" class="form-control fw-bold text-danger" 
                               value="{{ old('numero', $prossimoNumero) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data Ordine <span class="text-danger">*</span></label>
                        <input type="date" name="data_ordine" class="form-control" 
                               value="{{ old('data_ordine', now()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Fornitore <span class="text-danger">*</span></label>
                        <select name="fornitore_id" class="form-select" required id="selectFornitore">
                            <option value="">-- Seleziona Fornitore --</option>
                            @foreach($fornitori as $f)
                                <option value="{{ $f->id }}" 
                                    {{ old('fornitore_id', $selectedFornitoreId) == $f->id ? 'selected' : '' }}>
                                    {{ $f->ragione_sociale }} {{ $f->codice ? "({$f->codice})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Destinazione Merci <span class="text-danger">*</span></label>
                        <input type="text" name="destinazione" class="form-control" 
                               value="{{ old('destinazione', "VIA DELL'INFORMATICA 3  - S. PALOMBA POMEZIA  (RM)") }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data Consegna Prevista</label>
                        <input type="date" name="data_consegna_prevista" class="form-control" 
                               value="{{ old('data_consegna_prevista', $rda?->data_approvvigionamento?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Commessa Collegata</label>
                        <select name="commessa_id" class="form-select">
                            <option value="">-- Nessuna commessa --</option>
                            @foreach($commesse as $com)
                                <option value="{{ $com->id }}" {{ old('commessa_id') == $com->id ? 'selected' : '' }}>
                                    {{ $com->codice }} &bull; {{ Str::limit($com->titolo ?? $com->oggetto ?? '', 35) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Condizioni Pagamento <span class="text-danger">*</span></label>
                        <input type="text" name="condizioni_pagamento" class="form-control" 
                               value="{{ old('condizioni_pagamento', 'BB   30GG FM') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Resa <span class="text-danger">*</span></label>
                        <input type="text" name="resa" class="form-control" 
                               value="{{ old('resa', 'VS CURA') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Trasporto <span class="text-danger">*</span></label>
                        <input type="text" name="trasporto" class="form-control" 
                               value="{{ old('trasporto', 'VS CURA') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Imballo <span class="text-danger">*</span></label>
                        <input type="text" name="imballo" class="form-control" 
                               value="{{ old('imballo', 'VS CURA') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Vs Rif. Offerta N°</label>
                        <input type="text" name="riferimento_offerta" class="form-control" 
                               placeholder="es. e-mail del 18/05/2026" 
                               value="{{ old('riferimento_offerta') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Vs Rif. Interno</label>
                        <input type="text" name="riferimento_interno" class="form-control" 
                               value="{{ old('riferimento_interno', $defaultInterno) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stato ODA <span class="text-danger">*</span></label>
                        <select name="stato" class="form-select" required>
                            <option value="inviato" {{ old('stato', 'inviato') == 'inviato' ? 'selected' : '' }}>Inviato al Fornitore</option>
                            <option value="confermato" {{ old('stato') == 'confermato' ? 'selected' : '' }}>Confermato dal Fornitore</option>
                            <option value="consegnato" {{ old('stato') == 'consegnato' ? 'selected' : '' }}>Consegnato / Chiuso</option>
                            <option value="bozza" {{ old('stato') == 'bozza' ? 'selected' : '' }}>Bozza</option>
                            <option value="annullato" {{ old('stato') == 'annullato' ? 'selected' : '' }}>Annullato</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: BENI E SERVIZI ORDINATI (TABELLA MD PG 04/02-00) ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div>
                    <i class="bi bi-box-seam text-danger me-1"></i> 2. Beni / Servizi Ordinati
                    <span class="badge bg-secondary ms-2" id="righeCountBadge">1 riga</span>
                </div>
                <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1" id="btnAddRiga">
                    <i class="bi bi-plus-lg"></i> Aggiungi Riga
                </button>
            </div>

            <div class="p-0 table-responsive">
                <table class="table table-righe-oda align-middle" id="tabellaRighe">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">#</th>
                            <th style="min-width: 280px;">Descrizione Beni <span class="text-danger">*</span></th>
                            <th style="width: 140px;">Codice Articolo</th>
                            <th style="width: 90px;" class="text-center">UM</th>
                            <th style="width: 110px;" class="text-center">Q.TÀ <span class="text-danger">*</span></th>
                            <th style="width: 140px;" class="text-end">Costo Unit. (€) <span class="text-danger">*</span></th>
                            <th style="width: 130px;" class="text-end">Totale (€)</th>
                            <th style="width: 50px;" class="text-center"><i class="bi bi-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="righeContainer">
                        {{-- Righe dinamiche create da Javascript --}}
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <i class="bi bi-calculator text-primary me-1"></i> I totali di ciascuna riga e il totale imponibile si aggiornano automaticamente.
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" id="btnAddRigaBottom">
                    <i class="bi bi-plus-circle"></i> Aggiungi un altro articolo
                </button>
            </div>
        </div>

        {{-- ── CARD 3: NOTE & RIEPILOGO TOTALI ── --}}
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="form-card h-100 mb-0">
                    <div class="form-card-header">
                        <i class="bi bi-chat-left-dots text-danger me-1"></i> Note Aggiuntive per il Fornitore
                    </div>
                    <div class="form-card-body">
                        <textarea name="note_aggiuntive" rows="4" class="form-control" 
                                  placeholder="Es. Cauzione bombole, orari consegna particolari, certificati di conformità richiesti...">{{ old('note_aggiuntive') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="totali-box">
                    <h6 class="fw-bold text-dark text-uppercase mb-3">Riepilogo Importi Ordine</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Totale Imponibile:</span>
                        <span class="fw-bold text-dark fs-6" id="txtImponibile">€ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Aliquota IVA:</span>
                        <div class="input-group input-group-sm" style="width: 90px;">
                            <input type="number" name="aliquota_iva" id="inputAliquotaIva" 
                                   class="form-control text-end fw-bold" value="{{ old('aliquota_iva', 22) }}" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Importo IVA:</span>
                        <span class="text-dark" id="txtIva">€ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span class="fw-bold fs-5 text-dark">TOTALE ORDINE:</span>
                        <span class="fw-black fs-5 text-danger" id="txtTotaleOrdine">€ 0,00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── BOTTONI AZIONE ── --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('oda.index') }}" class="btn btn-light border px-4">
                Annulla
            </a>
            <button type="submit" class="btn btn-danger px-4 fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-check2-circle fs-5"></i> Emetti Ordine di Acquisto
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('righeContainer');
            const countBadge = document.getElementById('righeCountBadge');
            const btnAdd = document.getElementById('btnAddRiga');
            const btnAddBottom = document.getElementById('btnAddRigaBottom');
            const txtImponibile = document.getElementById('txtImponibile');
            const txtIva = document.getElementById('txtIva');
            const txtTotaleOrdine = document.getElementById('txtTotaleOrdine');
            const inputAliquota = document.getElementById('inputAliquotaIva');

            const prefilledRighe = {!! json_encode(old('righe', $prefilledRighe ?? [])) !!};

            let rowIndex = 0;

            function addRow(data = {}) {
                const tr = document.createElement('tr');
                tr.className = 'riga-item';
                tr.dataset.index = rowIndex;

                const descVal = data.descrizione_bene || '';
                const codVal  = data.codice_articolo || '';
                const umVal   = data.unita_misura || 'PZ';
                const qtaVal  = data.quantita !== undefined ? data.quantita : '1';
                const costoVal= data.costo_unitario !== undefined ? data.costo_unitario : '0';
                const rdaRigaId = data.rda_riga_id || '';

                tr.innerHTML = `
                    <td class="text-center align-middle">
                        <span class="badge bg-light text-muted border">${rowIndex + 1}</span>
                        ${rdaRigaId ? `<input type="hidden" name="righe[${rowIndex}][rda_riga_id]" value="${rdaRigaId}">` : ''}
                    </td>
                    <td>
                        <input type="text" name="righe[${rowIndex}][descrizione_bene]" 
                               class="form-control" placeholder="es. Argon 5.5 - Bombola 40L" 
                               value="${escapeHtml(descVal)}" required>
                    </td>
                    <td>
                        <input type="text" name="righe[${rowIndex}][codice_articolo]" 
                               class="form-control" placeholder="es. ART-001" 
                               value="${escapeHtml(codVal)}">
                    </td>
                    <td>
                        <select name="righe[${rowIndex}][unita_misura]" class="form-select form-select-sm text-center">
                            <option value="PZ" ${umVal === 'PZ' ? 'selected' : ''}>PZ</option>
                            <option value="KG" ${umVal === 'KG' ? 'selected' : ''}>KG</option>
                            <option value="M" ${umVal === 'M' ? 'selected' : ''}>M</option>
                            <option value="L" ${umVal === 'L' ? 'selected' : ''}>L</option>
                            <option value="CONF" ${umVal === 'CONF' ? 'selected' : ''}>CONF</option>
                            <option value="KIT" ${umVal === 'KIT' ? 'selected' : ''}>KIT</option>
                            <option value="ORE" ${umVal === 'ORE' ? 'selected' : ''}>ORE</option>
                            <option value="CORPO" ${umVal === 'CORPO' ? 'selected' : ''}>A CORPO</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" name="righe[${rowIndex}][quantita]" 
                               class="form-control text-end fw-bold input-qta" placeholder="1" 
                               value="${escapeHtml(qtaVal)}" required>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" min="0" name="righe[${rowIndex}][costo_unitario]" 
                                   class="form-control text-end fw-bold input-costo" placeholder="0.00" 
                                   value="${escapeHtml(costoVal)}" required>
                        </div>
                    </td>
                    <td class="text-end">
                        <span class="fw-bold text-dark riga-totale-label">€ 0,00</span>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-action btn-remove-riga" title="Elimina riga">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;

                container.appendChild(tr);
                rowIndex++;
                updateTotals();
            }

            function updateTotals() {
                const rows = container.querySelectorAll('.riga-item');
                let imponibile = 0;

                rows.forEach((row, idx) => {
                    const badge = row.querySelector('.badge');
                    if (badge) badge.textContent = idx + 1;

                    const qtaInput = row.querySelector('.input-qta');
                    const costoInput = row.querySelector('.input-costo');
                    const qta = parseFloat(qtaInput?.value) || 0;
                    const costo = parseFloat(costoInput?.value) || 0;
                    const tot = qta * costo;

                    const label = row.querySelector('.riga-totale-label');
                    if (label) {
                        label.textContent = '€ ' + tot.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }

                    imponibile += tot;
                });

                countBadge.textContent = `${rows.length} ${rows.length === 1 ? 'riga' : 'righe'}`;

                const aliquota = parseFloat(inputAliquota?.value) || 0;
                const iva = imponibile * (aliquota / 100);
                const totaleOrdine = imponibile + iva;

                txtImponibile.textContent = '€ ' + imponibile.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                txtIva.textContent = '€ ' + iva.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                txtTotaleOrdine.textContent = '€ ' + totaleOrdine.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                // Disabilita cancellazione se c'è solo 1 riga
                const removeBtns = container.querySelectorAll('.btn-remove-riga');
                removeBtns.forEach(btn => {
                    btn.disabled = (rows.length <= 1);
                });
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            container.addEventListener('input', function (e) {
                if (e.target.classList.contains('input-qta') || e.target.classList.contains('input-costo')) {
                    updateTotals();
                }
            });

            inputAliquota.addEventListener('input', updateTotals);

            container.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-remove-riga');
                if (btn && !btn.disabled) {
                    btn.closest('.riga-item').remove();
                    updateTotals();
                }
            });

            btnAdd.addEventListener('click', () => addRow());
            btnAddBottom.addEventListener('click', () => addRow());

            if (prefilledRighe && prefilledRighe.length > 0) {
                prefilledRighe.forEach(r => addRow(r));
            } else {
                addRow();
            }
        });
    </script>
@endsection
