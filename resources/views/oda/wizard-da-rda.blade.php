@extends('layouts.app')

@section('content')
    <style>
        .wizard-header {
            background: linear-gradient(135deg, #111422 0%, #1e2238 60%, #0f121d 100%);
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
        }
        .wizard-header h1 { font-size: 1.7rem; font-weight: 800; margin: 0; }

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

        .table-wizard-righe {
            width: 100%;
            margin-bottom: 0;
            font-size: 0.88rem;
        }
        .table-wizard-righe thead th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-wizard-righe tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .table-wizard-righe tbody tr:hover {
            background: #fafbfc;
        }

        .sticky-footer-wizard {
            position: sticky;
            bottom: 15px;
            background: #111422;
            color: #fff;
            border-radius: 14px;
            padding: 1.25rem 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            z-index: 1000;
        }
    </style>

    {{-- Header --}}
    <div class="wizard-header">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">MD PG 04/02-00</span>
                <span class="text-white-50 small fw-bold text-uppercase">&bull; Emissione Ordine da RDA</span>
            </div>
            <h1>Genera Ordine di Acquisto da RDA {{ $rda->numero }}</h1>
            <p class="text-white-50 small mb-0 mt-1">
                Richiedente: <span class="text-white fw-bold">{{ $rda->richiedente }}</span> &bull; 
                Data Richiesta: <span class="text-white fw-bold">{{ $rda->data_richiesta ? $rda->data_richiesta->format('d/m/Y') : '—' }}</span>
                @if($rda->data_approvvigionamento)
                    &bull; Consegna richiesta entro: <span class="text-warning fw-bold">{{ $rda->data_approvvigionamento->format('d/m/Y') }}</span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('rda.show', $rda) }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i> Torna alla RDA
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Spiegazione Flessibilità --}}
    <div class="alert alert-primary d-flex align-items-center gap-3 mb-4 shadow-sm">
        <i class="bi bi-check2-circle fs-2 text-primary flex-shrink-0"></i>
        <div>
            <strong class="text-dark fs-6">Massima libertà di selezione:</strong>
            <p class="mb-0 small text-dark opacity-75">
                Scegli il <strong>Fornitore</strong> a cui intestare l'ordine e seleziona <strong>qualsiasi combinazione di articoli</strong> dalla RDA (anche due o più prodotti che avevano fornitori diversi).
            </p>
        </div>
    </div>

    {{-- FORM GENERAZIONE ODA SINGOLO / COMBINATO --}}
    <form method="POST" action="{{ route('rda.storeDaRdaSingolo', $rda) }}" id="formOdaSingolo">
        @csrf

        {{-- ── 1. SCELTA DEL FORNITORE DESTINATARIO ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <h3 class="section-card-title">
                    <i class="bi bi-building text-danger"></i> 1. A quale Fornitore vuoi inviare questo Ordine di Acquisto?
                </h3>
            </div>
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Fornitore Intestatario ODA <span class="text-danger">*</span></label>
                        <select name="fornitore_id" class="form-select form-select-lg fw-bold" required id="selectFornitore">
                            <option value="">-- Seleziona Fornitore dall'Anagrafica --</option>
                            @foreach($tuttiFornitori as $f)
                                @php
                                    // Seleziona come default il fornitore della prima riga RDA se c'è
                                    $firstFornitoreId = $rda->righe->firstWhere('fornitore_id', '!=', null)?->fornitore_id;
                                    $isSelected = ($firstFornitoreId && $firstFornitoreId == $f->id);
                                @endphp
                                <option value="{{ $f->id }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $f->ragione_sociale }} {{ $f->codice ? "({$f->codice})" : '' }} &bull; {{ $f->citta ?: $f->indirizzo }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted">
                            Puoi scegliere qualsiasi fornitore. L'ordine e lo storico acquisti verranno registrati nella sua scheda.
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-uppercase text-secondary">Commessa Collegata (Opzionale)</label>
                        <select name="commessa_id" class="form-select">
                            <option value="">-- Nessuna commessa specifica --</option>
                            @foreach(\App\Models\Commessa::where('stato', '!=', 'chiusa')->orderByDesc('id')->get() as $com)
                                <option value="{{ $com->id }}">{{ $com->codice }} &bull; {{ Str::limit($com->titolo ?? $com->oggetto ?? '', 35) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. SELEZIONE DEGLI ARTICOLI DELLA RDA (ANCHE CON FORNITORI DIVERSI) ── --}}
        <div class="section-card">
            <div class="section-card-header">
                <div>
                    <h3 class="section-card-title">
                        <i class="bi bi-boxes text-danger"></i> 2. Seleziona i Prodotti da Includere nell'Ordine
                    </h3>
                    <p class="text-muted small mb-0">Spunta i prodotti che vuoi ordinare a questo fornitore (1, 2 o tutti)</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAll">
                        <i class="bi bi-check-all"></i> Seleziona Tutti
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAll">
                        <i class="bi bi-dash"></i> Deseleziona
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-wizard-righe align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">Includi</th>
                            <th>Descrizione Prodotto / Servizio</th>
                            <th style="width: 130px;">Codice</th>
                            <th style="width: 120px;" class="text-center">Quantità</th>
                            <th style="min-width: 200px;">Fornitore Consigliato in RDA</th>
                            <th style="width: 140px;" class="text-end">Costo Unit. (€)</th>
                            <th style="width: 130px;" class="text-end">Totale (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rda->righe as $idx => $r)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="righe_ids[]" value="{{ $r->id }}" 
                                           checked class="form-check-input check-riga fs-5" id="riga_{{ $r->id }}">
                                </td>
                                <td>
                                    <label for="riga_{{ $r->id }}" class="fw-bold text-dark mb-0 d-block cursor-pointer">
                                        {{ $r->descrizione_prodotto }}
                                    </label>
                                    @if($r->note)
                                        <div class="text-muted small fst-italic">{{ $r->note }}</div>
                                    @endif
                                </td>
                                <td class="font-monospace text-secondary">{{ $r->codice_prodotto ?: '—' }}</td>
                                <td class="text-center">
                                    <span class="fw-bold fs-6">{{ number_format($r->quantita, 2, ',', '.') }}</span>
                                    <span class="badge bg-light text-muted border ms-1">{{ $r->unita_misura ?: 'PZ' }}</span>
                                </td>
                                <td>
                                    @if($r->fornitore)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-building text-danger me-1"></i>{{ $r->fornitore->ragione_sociale }}
                                        </span>
                                    @elseif($r->fornitore_consigliato_testo)
                                        <span class="badge bg-light text-muted border">{{ $r->fornitore_consigliato_testo }}</span>
                                    @else
                                        <span class="text-muted small fst-italic">— Nessuno —</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">€</span>
                                        <input type="number" step="0.01" min="0" 
                                               name="costi[{{ $r->id }}]" 
                                               class="form-control text-end fw-bold input-costo" 
                                               data-qta="{{ $r->quantita }}" 
                                               placeholder="0.00" value="0.00">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark subtotal-cell">€ 0,00</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Nessun articolo presente in questa RDA.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── BARRA AZIONE STICKY IN BASSO ── --}}
        <div class="sticky-footer-wizard">
            <div>
                <div class="fw-bold fs-6">
                    <i class="bi bi-cart-check text-danger me-1"></i> Crea Ordine di Acquisto (MD PG 04/02-00)
                </div>
                <div class="text-white-50 small">
                    Verrà generato l'ordine ODA con tutti i prodotti spuntati e registrato nella scheda del Fornitore scelto.
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('rda.show', $rda) }}" class="btn btn-outline-light">
                    Annulla
                </a>
                <button type="submit" class="btn btn-danger btn-lg fw-bold d-inline-flex align-items-center gap-2 px-4 shadow">
                    <i class="bi bi-check2-circle fs-4"></i> Emetti ODA con Articoli Selezionati
                </button>
            </div>
        </div>
    </form>

    {{-- Se ci sono più fornitori consigliati diversi, proponi anche l'opzione automatica --}}
    @if(count($fornitoriGruppi) > 1)
        <div class="section-card mt-5">
            <div class="section-card-header bg-light">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-layers text-primary me-1"></i> Alternativa: Genera {{ count($fornitoriGruppi) }} ODA separati in automatico
                    </h5>
                    <p class="text-muted small mb-0">
                        In questa RDA sono presenti {{ count($fornitoriGruppi) }} fornitori diversi. Se vuoi generare direttamente un ODA separato per ciascuno di essi:
                    </p>
                </div>
            </div>
            <div class="p-3">
                <form method="POST" action="{{ route('rda.storeDaRdaMultipli', $rda) }}">
                    @csrf
                    @foreach($fornitoriGruppi as $fId => $grp)
                        <input type="hidden" name="gruppi[{{ $fId }}][attivo]" value="1">
                        @foreach($grp['righe'] as $rg)
                            <input type="hidden" name="gruppi[{{ $fId }}][righe_ids][]" value="{{ $rg->id }}">
                            <input type="hidden" name="gruppi[{{ $fId }}][costi][{{ $rg->id }}]" value="0">
                        @endforeach
                    @endforeach
                    <button type="submit" class="btn btn-outline-primary fw-semibold" 
                            onclick="return confirm('Verranno creati {{ count($fornitoriGruppi) }} ordini separati (uno per ciascun fornitore consigliato). Confermi?');">
                        <i class="bi bi-diagram-3 me-1"></i> Genera {{ count($fornitoriGruppi) }} ODA distinti (uno per fornitore)
                    </button>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Calcolo subtotali live
            document.querySelectorAll('.input-costo').forEach(input => {
                input.addEventListener('input', function () {
                    const qta = parseFloat(this.dataset.qta) || 0;
                    const costo = parseFloat(this.value) || 0;
                    const tot = qta * costo;
                    const row = this.closest('tr');
                    const cell = row.querySelector('.subtotal-cell');
                    if (cell) {
                        cell.textContent = '€ ' + tot.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                });
            });

            // Seleziona / Deseleziona tutti
            const btnSelectAll = document.getElementById('btnSelectAll');
            const btnDeselectAll = document.getElementById('btnDeselectAll');
            const checks = document.querySelectorAll('.check-riga');

            btnSelectAll.addEventListener('click', () => {
                checks.forEach(c => c.checked = true);
            });
            btnDeselectAll.addEventListener('click', () => {
                checks.forEach(c => c.checked = false);
            });

            // Validazione invio: almeno una checkbox spuntata
            const form = document.getElementById('formOdaSingolo');
            form.addEventListener('submit', function (e) {
                const checked = Array.from(checks).filter(c => c.checked);
                if (checked.length === 0) {
                    e.preventDefault();
                    alert('Seleziona almeno un articolo da inserire nell\'ordine!');
                }
            });
        });
    </script>
@endsection
