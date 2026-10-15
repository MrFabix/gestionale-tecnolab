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
        .form-card-body {
            padding: 1.5rem;
        }
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
        .table-righe-rda {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-righe-rda thead th {
            background: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.75rem 0.85rem;
            border-bottom: 1px solid #cbd5e1;
        }
        .table-righe-rda tbody td {
            padding: 0.75rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .table-righe-rda tbody tr:last-child td {
            border-bottom: none;
        }
        .table-righe-rda tbody tr:hover {
            background: #fafbfc;
        }
        .row-num {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
    </style>

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="doc-badge-pill"><i class="bi bi-file-earmark-text"></i> MD PG04/01-00</span>
                <span class="text-muted small fw-bold text-uppercase">&bull; Approvvigionamenti</span>
            </div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-pencil-square text-danger me-2"></i>Modifica RDA {{ $rda->numero }}
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rda.show', $rda) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i> Visualizza
            </a>
            <a href="{{ route('rda.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Torna all'Elenco
            </a>
        </div>
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

    <form method="POST" action="{{ route('rda.update', $rda) }}" id="rdaForm">
        @csrf
        @method('PUT')

        {{-- ── CARD 1: DATI GENERALI RDA ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div>
                    <i class="bi bi-card-checklist text-danger me-1"></i> 1. Estremi Richiesta di Acquisto
                </div>
                <div class="text-muted small fw-normal">
                    ID Interno: #{{ $rda->id }}
                </div>
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">N° RDA <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">N°</span>
                            <input type="text" name="numero" class="form-control fw-bold text-danger" 
                                   value="{{ old('numero', $rda->numero) }}" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Richiedente <span class="text-danger">*</span></label>
                        <input type="text" name="richiedente" class="form-control" 
                               placeholder="Nome e cognome" 
                               value="{{ old('richiedente', $rda->richiedente) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data Richiesta <span class="text-danger">*</span></label>
                        <input type="date" name="data_richiesta" class="form-control" 
                               value="{{ old('data_richiesta', $rda->data_richiesta ? $rda->data_richiesta->format('Y-m-d') : '') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data Approvvigionamento</label>
                        <input type="date" name="data_approvvigionamento" class="form-control" 
                               value="{{ old('data_approvvigionamento', $rda->data_approvvigionamento ? $rda->data_approvvigionamento->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stato RDA <span class="text-danger">*</span></label>
                        <select name="stato" class="form-select" required>
                            <option value="in_attesa" {{ old('stato', $rda->stato) == 'in_attesa' ? 'selected' : '' }}>⏳ In Attesa di Approvazione</option>
                            <option value="bozza" {{ old('stato', $rda->stato) == 'bozza' ? 'selected' : '' }}>📝 Bozza (Non inoltrata)</option>
                            <option value="approvata" {{ old('stato', $rda->stato) == 'approvata' ? 'selected' : '' }}>✅ Approvata</option>
                            <option value="ordinata" {{ old('stato', $rda->stato) == 'ordinata' ? 'selected' : '' }}>📦 Ordinata (ODA Emesso)</option>
                            <option value="rifiutata" {{ old('stato', $rda->stato) == 'rifiutata' ? 'selected' : '' }}>❌ Rifiutata</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Note Generali RDA</label>
                        <input type="text" name="note" class="form-control" 
                               placeholder="Es. Commessa speciale cliente X, materiale urgente per laboratorio, ecc." 
                               value="{{ old('note', $rda->note) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CARD 2: TABELLA PRODOTTI / SERVIZI RICHIESTI (MD PG04/01-00) ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <div>
                    <i class="bi bi-box-seam text-danger me-1"></i> 2. Prodotti / Servizi Richiesti
                    <span class="badge bg-secondary ms-2" id="righeCountBadge">{{ $rda->righe->count() }} righe</span>
                </div>
                <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1" id="btnAddRiga">
                    <i class="bi bi-plus-lg"></i> Aggiungi Riga
                </button>
            </div>
            <div class="p-0 table-responsive">
                <table class="table table-righe-rda align-middle" id="tabellaRighe">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">#</th>
                            <th style="min-width: 280px;">Descrizione Prodotto / Servizio <span class="text-danger">*</span></th>
                            <th style="width: 140px;">Codice Prodotto</th>
                            <th style="width: 120px;">Quantità <span class="text-danger">*</span></th>
                            <th style="width: 100px;">U.M.</th>
                            <th style="min-width: 250px;">Fornitore Consigliato</th>
                            <th style="width: 50px;" class="text-center"><i class="bi bi-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="righeContainer">
                        {{-- Righe dinamiche gestite da script --}}
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <i class="bi bi-info-circle text-primary me-1"></i> Modifica o aggiungi articoli per questa Richiesta di Acquisto.
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" id="btnAddRigaBottom">
                    <i class="bi bi-plus-circle"></i> Aggiungi un altro articolo
                </button>
            </div>
        </div>

        {{-- ── AZIONI SALVATAGGIO ── --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('rda.show', $rda) }}" class="btn btn-light border px-4">
                Annulla
            </a>
            <button type="submit" class="btn btn-danger px-4 fw-bold d-inline-flex align-items-center gap-2">
                <i class="bi bi-check2-circle fs-5"></i> Salva Modifiche
            </button>
        </div>
    </form>

    {{-- Script Righe Dinamiche --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('righeContainer');
            const countBadge = document.getElementById('righeCountBadge');
            const btnAdd = document.getElementById('btnAddRiga');
            const btnAddBottom = document.getElementById('btnAddRigaBottom');

            // Fornitori anagrafica per il select
            const fornitori = {!! json_encode($fornitoriList ?? []) !!};

            // Righe esistenti dal DB o da old()
            const existingRighe = {!! json_encode(old('righe', $existingRighe ?? [])) !!};

            let rowIndex = 0;

            function renderFornitoriOptions(selectedId) {
                let options = '<option value="">-- Seleziona da Anagrafica --</option>';
                fornitori.forEach(f => {
                    const sel = (selectedId && selectedId == f.id) ? 'selected' : '';
                    options += `<option value="${f.id}" ${sel}>${f.nome}</option>`;
                });
                return options;
            }

            function addRow(data = {}) {
                const tr = document.createElement('tr');
                tr.className = 'riga-item';
                tr.dataset.index = rowIndex;

                const descVal    = data.descrizione_prodotto || '';
                const codVal     = data.codice_prodotto || '';
                const qtaVal     = data.quantita !== undefined ? data.quantita : '1';
                const umVal      = data.unita_misura || 'pz';
                const fornitoreId = data.fornitore_id || '';
                const fornitoreTxt= data.fornitore_consigliato_testo || '';

                tr.innerHTML = `
                    <td class="text-center align-middle">
                        <span class="row-num text-muted">${rowIndex + 1}</span>
                    </td>
                    <td>
                        <input type="text" name="righe[${rowIndex}][descrizione_prodotto]" 
                               class="form-control" placeholder="es. Carta abrasiva P120, Cavi di rete Cat 6..." 
                               value="${escapeHtml(descVal)}" required>
                    </td>
                    <td>
                        <input type="text" name="righe[${rowIndex}][codice_prodotto]" 
                               class="form-control" placeholder="es. ART-102" 
                               value="${escapeHtml(codVal)}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" name="righe[${rowIndex}][quantita]" 
                               class="form-control text-end fw-bold" placeholder="1" 
                               value="${escapeHtml(qtaVal)}" required>
                    </td>
                    <td>
                        <select name="righe[${rowIndex}][unita_misura]" class="form-select">
                            <option value="pz" ${umVal === 'pz' ? 'selected' : ''}>pz</option>
                            <option value="kg" ${umVal === 'kg' ? 'selected' : ''}>kg</option>
                            <option value="m" ${umVal === 'm' ? 'selected' : ''}>m</option>
                            <option value="l" ${umVal === 'l' ? 'selected' : ''}>l</option>
                            <option value="conf" ${umVal === 'conf' ? 'selected' : ''}>conf</option>
                            <option value="kit" ${umVal === 'kit' ? 'selected' : ''}>kit</option>
                            <option value="ore" ${umVal === 'ore' ? 'selected' : ''}>ore</option>
                            <option value="corpo" ${umVal === 'corpo' ? 'selected' : ''}>a corpo</option>
                        </select>
                    </td>
                    <td>
                        <select name="righe[${rowIndex}][fornitore_id]" class="form-select form-select-sm mb-1 fornitore-select">
                            ${renderFornitoriOptions(fornitoreId)}
                        </select>
                        <input type="text" name="righe[${rowIndex}][fornitore_consigliato_testo]" 
                               class="form-control form-control-sm" 
                               placeholder="O scrivi fornitore manuale..." 
                               value="${escapeHtml(fornitoreTxt)}">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-action btn-remove-riga" title="Elimina riga">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;

                container.appendChild(tr);
                rowIndex++;
                updateRowNumbers();
            }

            function updateRowNumbers() {
                const rows = container.querySelectorAll('.riga-item');
                rows.forEach((row, idx) => {
                    const badge = row.querySelector('.row-num');
                    if (badge) badge.textContent = idx + 1;
                });
                countBadge.textContent = `${rows.length} ${rows.length === 1 ? 'riga' : 'righe'}`;

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

            container.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-remove-riga');
                if (btn && !btn.disabled) {
                    const row = btn.closest('.riga-item');
                    row.remove();
                    updateRowNumbers();
                }
            });

            btnAdd.addEventListener('click', () => addRow());
            btnAddBottom.addEventListener('click', () => addRow());

            if (existingRighe && existingRighe.length > 0) {
                existingRighe.forEach(r => addRow(r));
            } else {
                addRow();
            }
        });
    </script>
@endsection
