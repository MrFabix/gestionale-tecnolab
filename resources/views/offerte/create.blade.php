@extends('layouts.app')

@section('content')
    <style>
        .form-section { background:#fff; border:1px solid #e5e7eb; border-radius:12px; margin-bottom:1.5rem; overflow:hidden; }
        .form-section-head { padding:.85rem 1.25rem; border-bottom:1px solid #f0f0f0; background:#fafafa; }
        .form-section-head h5 { margin:0; font-size:.95rem; font-weight:700; }
        .form-section-body { padding:1.25rem; }

        /* Righe voci */
        .righe-table { width:100%; border-collapse:collapse; font-size:.875rem; }
        .righe-table thead th { background:#f8fafc; border-bottom:2px solid #e5e7eb; padding:.6rem .75rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; }
        .righe-table tbody tr { border-bottom:1px solid #f0f0f0; }
        .righe-table td { padding:.4rem .5rem; vertical-align:middle; }
        .righe-table tfoot td { padding:.75rem; background:#fafafa; border-top:2px solid #e5e7eb; }

        .totale-preview { font-size:1.3rem; font-weight:700; color:#c00000; }

        .btn-remove-row { width:28px; height:28px; border-radius:6px; border:1px solid #fca5a5; background:#fff; color:#b91c1c; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:.8rem; }
        .btn-remove-row:hover { background:#fee2e2; }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>
            <i class="bi bi-file-earmark-text me-1"></i>
            @isset($offerta) Modifica offerta — {{ $offerta->numero }}
            @else Nuova Offerta
            @endisset
        </h1>
        <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna all'elenco
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ isset($offerta) ? route('offerte.update',$offerta) : route('offerte.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @isset($offerta) @method('PUT') @endisset

        {{-- ── INTESTAZIONE ───────────────────────── --}}
        <div class="form-section">
            <div class="form-section-head"><h5><i class="bi bi-info-circle text-muted me-2"></i>Intestazione</h5></div>
            <div class="form-section-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">N° Offerta <span class="text-danger">*</span></label>
                        <input type="text" name="numero" class="form-control fw-bold"
                               value="{{ old('numero', $offerta->numero ?? $numero ?? '') }}"
                            {{ isset($offerta) ? 'readonly' : 'required' }}>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" name="data" class="form-control" required
                               value="{{ old('data', isset($offerta) ? $offerta->data->format('Y-m-d') : now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Validità fino al</label>
                        <input type="date" name="validita_fino" class="form-control"
                               value="{{ old('validita_fino', isset($offerta) && $offerta->validita_fino ? $offerta->validita_fino->format('Y-m-d') : now()->addDays(60)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stato</label>
                        <select name="stato" class="form-select">
                            @foreach(['bozza'=>'Bozza','inviata'=>'Inviata','accettata'=>'Accettata','rifiutata'=>'Rifiutata'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('stato',$offerta->stato??'bozza')===$v?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rif. Commessa</label>
                        <input type="text" name="commessa_rif" class="form-control"
                               value="{{ old('commessa_rif', $offerta->commessa_rif ?? '') }}" placeholder="es: C-2025-001">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">— Seleziona cliente —</option>
                            @foreach($clienti as $c)
                                <option value="{{ $c->id }}" {{ old('cliente_id',$offerta->cliente_id??null)==$c->id?'selected':'' }}>
                                    {{ $c->ragione_sociale }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alla cortese attenzione di</label>
                        <input type="text" name="attenzione" class="form-control"
                               value="{{ old('attenzione', $offerta->attenzione ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Vs. Riferimento</label>
                        <input type="text" name="riferimento" class="form-control"
                               value="{{ old('riferimento', $offerta->riferimento ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Consegna</label>
                        <input type="text" name="consegna" class="form-control"
                               value="{{ old('consegna', $offerta->consegna ?? 'Entro 3 settimane dalla ricezione dei campioni') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pagamento</label>
                        <input type="text" name="pagamento" class="form-control"
                               value="{{ old('pagamento', $offerta->pagamento ?? 'BB 30gg FM') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Note (stampate)</label>
                        <textarea name="note" class="form-control" rows="2">{{ old('note', $offerta->note ?? '') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Note interne <small class="text-muted">(non stampate)</small></label>
                        <textarea name="note_interne" class="form-control" rows="2">{{ old('note_interne', $offerta->note_interne ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── VOCI / RIGHE ───────────────────────── --}}
        <div class="form-section">
            <div class="form-section-head d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-list-ul text-muted me-2"></i>Voci / Servizi</h5>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnAddRiga">
                    <i class="bi bi-plus-lg me-1"></i> Aggiungi riga
                </button>
            </div>
            <div class="form-section-body p-0">
                <div style="overflow-x:auto;">
                    <table class="righe-table">
                        <thead>
                        <tr>
                            <th style="width:50%">Descrizione beni / servizi</th>
                            <th style="width:8%">UM</th>
                            <th style="width:10%">Q.tà</th>
                            <th style="width:14%">Prezzo (€)</th>
                            <th style="width:14%">Totale (€)</th>
                            <th style="width:4%"></th>
                        </tr>
                        </thead>
                        <tbody id="righeBody">
                        @php
                            $righeOld = old('righe', isset($offerta) ? $offerta->righe->toArray() : [['descrizione'=>'','um'=>'PZ','quantita'=>1,'prezzo_unitario'=>0]]);
                        @endphp
                        @foreach($righeOld as $i => $riga)
                            <tr class="riga-row">
                                <td>
                                    <input type="text" name="righe[{{ $i }}][descrizione]"
                                           class="form-control form-control-sm"
                                           value="{{ $riga['descrizione'] ?? '' }}"
                                           placeholder="es: Prova di trazione su provino…" required>
                                </td>
                                <td>
                                    <select name="righe[{{ $i }}][um]" class="form-select form-select-sm">
                                        @foreach(['PZ','NR','H','GG','KG','MT','SET'] as $um)
                                            <option value="{{ $um }}" {{ ($riga['um']??'PZ')===$um?'selected':'' }}>{{ $um }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="righe[{{ $i }}][quantita]"
                                           class="form-control form-control-sm qta"
                                           value="{{ $riga['quantita'] ?? 1 }}"
                                           min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" name="righe[{{ $i }}][prezzo_unitario]"
                                           class="form-control form-control-sm prezzo"
                                           value="{{ $riga['prezzo_unitario'] ?? 0 }}"
                                           min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm totale-riga bg-light"
                                           value="{{ number_format(($riga['quantita']??1)*($riga['prezzo_unitario']??0),2,',','.') }}"
                                           readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn-remove-row" title="Rimuovi riga">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold text-muted" style="font-size:.85rem;">
                                TOTALE IMPONIBILE (escluso IVA)
                            </td>
                            <td colspan="2">
                                <span class="totale-preview" id="totalePreview">0,00</span>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── ALLEGATI ────────────────────────────── --}}
        <div class="form-section">
            <div class="form-section-head"><h5><i class="bi bi-paperclip text-muted me-2"></i>Allegati (offerta firmata, ordine cliente...)</h5></div>
            <div class="form-section-body">
                <input type="file" name="allegati[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">PDF o immagini, max 20MB ciascuno</small>

                @isset($allegati)
                    @if($allegati->count())
                        <ul class="list-group mt-3">
                            @foreach($allegati as $all)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <a href="{{ $all->getUrl() }}" target="_blank" class="small text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $all->name }}
                                    </a>
                                    <form action="{{ route('offerte.media.destroy', [$offerta, $all->id]) }}" method="POST"
                                          data-confirm="Eliminare?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endisset
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger px-5 fw-semibold">
                <i class="bi bi-save me-1"></i>
                @isset($offerta) Salva modifiche @else Crea offerta @endisset
            </button>
            <a href="{{ route('offerte.index') }}" class="btn btn-outline-secondary">Annulla</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        (function(){
            let rowIdx = {{ count(old('righe', isset($offerta) ? $offerta->righe->toArray() : [[]])) }};

            // ── Calcola totali ─────────────────────────────────────────
            function ricalcola() {
                let grand = 0;
                document.querySelectorAll('.riga-row').forEach(row => {
                    const q = parseFloat(row.querySelector('.qta').value) || 0;
                    const p = parseFloat(row.querySelector('.prezzo').value) || 0;
                    const t = q * p;
                    grand += t;
                    row.querySelector('.totale-riga').value = t.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
                });
                document.getElementById('totalePreview').textContent =
                    grand.toLocaleString('it-IT', {minimumFractionDigits:2, maximumFractionDigits:2});
            }

            // ── Template nuova riga ────────────────────────────────────
            const umOptions = ['PZ','NR','H','GG','KG','MT','SET']
                .map(u => `<option value="${u}">${u}</option>`).join('');

            function nuovaRiga() {
                const tr = document.createElement('tr');
                tr.className = 'riga-row';
                tr.innerHTML = `
            <td><input type="text" name="righe[${rowIdx}][descrizione]" class="form-control form-control-sm" placeholder="Descrizione servizio…" required></td>
            <td><select name="righe[${rowIdx}][um]" class="form-select form-select-sm">${umOptions}</select></td>
            <td><input type="number" name="righe[${rowIdx}][quantita]" class="form-control form-control-sm qta" value="1" min="0" step="0.01"></td>
            <td><input type="number" name="righe[${rowIdx}][prezzo_unitario]" class="form-control form-control-sm prezzo" value="0" min="0" step="0.01"></td>
            <td><input type="text" class="form-control form-control-sm totale-riga bg-light" value="0,00" readonly></td>
            <td><button type="button" class="btn-remove-row"><i class="bi bi-trash"></i></button></td>
        `;
                rowIdx++;
                document.getElementById('righeBody').appendChild(tr);
                bindRiga(tr);
                ricalcola();
                tr.querySelector('input[name*=descrizione]').focus();
            }

            function bindRiga(row) {
                row.querySelector('.qta').addEventListener('input', ricalcola);
                row.querySelector('.prezzo').addEventListener('input', ricalcola);
                row.querySelector('.btn-remove-row').addEventListener('click', () => {
                    if (document.querySelectorAll('.riga-row').length <= 1) return alert('Deve esserci almeno una riga.');
                    row.remove();
                    ricalcola();
                });
            }

            // Bind righe già presenti
            document.querySelectorAll('.riga-row').forEach(bindRiga);
            ricalcola();

            document.getElementById('btnAddRiga').addEventListener('click', nuovaRiga);
        })();
    </script>
@endsection
