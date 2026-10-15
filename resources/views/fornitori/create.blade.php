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
            gap: 0.5rem;
        }
        .form-card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.35rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            padding: 0.55rem 0.85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #d90429;
            box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.12);
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="small text-muted fw-bold text-uppercase">Qualità &bull; MD PG04/04-00</div>
            <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle text-danger me-2"></i>Nuovo Fornitore</h2>
        </div>
        <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Annulla e Torna all'Elenco
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

    <form method="POST" action="{{ route('fornitori.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ── 1. ESTREMI DEL FORNITORE ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-building text-danger"></i> 1. Estremi del Fornitore
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Codice Fornitore</label>
                        <input type="text" name="codice" class="form-control" placeholder="es. 001" value="{{ old('codice', $prossimoCodice ?? '') }}">
                    </div>

                    <div class="col-md-9">
                        <label class="form-label">Ragione Sociale / Fornitore <span class="text-danger">*</span></label>
                        <input type="text" name="ragione_sociale" class="form-control" placeholder="es. CERMAC S.r.l" value="{{ old('ragione_sociale') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Indirizzo (Via, Numero civico)</label>
                        <input type="text" name="indirizzo" class="form-control" placeholder="es. Via del Lavoro, 8" value="{{ old('indirizzo') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">CAP</label>
                        <input type="text" name="cap" class="form-control" placeholder="es. 20060" value="{{ old('cap') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Città</label>
                        <input type="text" name="citta" class="form-control" placeholder="es. Pozzo d'Adda" value="{{ old('citta') }}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Prov.</label>
                        <input type="text" name="provincia" class="form-control text-uppercase" maxlength="5" placeholder="MI" value="{{ old('provincia') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Partita IVA</label>
                        <input type="text" name="partita_iva" class="form-control mono" placeholder="es. 05035660967" value="{{ old('partita_iva') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" class="form-control mono" placeholder="Codice Fiscale" value="{{ old('codice_fiscale') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="es. 02-90981113" value="{{ old('telefono') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cellulare</label>
                        <input type="text" name="cellulare" class="form-control" placeholder="Cellulare" value="{{ old('cellulare') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="info@fornitore.com" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">PEC</label>
                        <input type="text" name="pec" class="form-control" placeholder="fornitore@pec.it" value="{{ old('pec') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sito Internet</label>
                        <input type="text" name="sito_web" class="form-control" placeholder="es. www.cermacsrl.com" value="{{ old('sito_web') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Referente di Contatto</label>
                        <input type="text" name="referente" class="form-control" placeholder="Nome referente commerciale/tecnico" value="{{ old('referente') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. CRITERI DI QUALIFICA & MONITORAGGIO ISO ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-patch-check-fill text-success"></i> 2. Qualifica Fornitore &amp; Criteri Tecnici (ISO 9001 / ISO 17025)
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Prodotti Acquistati / Categoria Fornitura</label>
                        <textarea name="prodotti_acquistati" class="form-control" rows="3" placeholder="es. Apparecchiature/accessori ausiliari per prove">{{ old('prodotti_acquistati') }}</textarea>
                        <small class="text-muted">Descrizione delle tipologie di prodotti o servizi forniti a Tecnolab.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Criteri di Qualifica Adottati</label>
                        <textarea name="criteri_qualifica" class="form-control" rows="3" placeholder="es. Requisiti tecnici richiesti dal Metodo di prova, Certificazione ISO 9001, Fornitore storico...">{{ old('criteri_qualifica') }}</textarea>
                        <small class="text-muted">Criteri in base ai quali il fornitore è stato ammesso all'albo.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stato Qualifica <span class="text-danger">*</span></label>
                        <select name="stato_qualifica" class="form-select" required>
                            <option value="qualificato" {{ old('stato_qualifica') === 'qualificato' ? 'selected' : '' }}>Qualificato (Idoneo)</option>
                            <option value="in_valutazione" {{ old('stato_qualifica') === 'in_valutazione' ? 'selected' : '' }}>In Valutazione / Audit</option>
                            <option value="sospeso" {{ old('stato_qualifica') === 'sospeso' ? 'selected' : '' }}>Sospeso (Non Conformità aperte)</option>
                            <option value="non_qualificato" {{ old('stato_qualifica') === 'non_qualificato' ? 'selected' : '' }}>Non Qualificato</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Data Qualifica / Ammissione</label>
                        <input type="date" name="data_qualifica" class="form-control" value="{{ old('data_qualifica', date('Y-m-d')) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Scadenza Qualifica / Riesame (opzionale)</label>
                        <input type="date" name="data_scadenza_qualifica" class="form-control" value="{{ old('data_scadenza_qualifica') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note Generali</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Annotazioni generali sul fornitore...">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. PRIMA FORNITURA / STORICO (OPZIONALE) ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-clock-history text-primary"></i> 3. Prima Fornitura &amp; Monitoraggio (Opzionale)
            </div>
            <div class="form-card-body">
                <p class="text-muted small mb-3">Se desideri, puoi registrare subito la prima fornitura o commessa affidata a questo fornitore (Tabella 2 della scheda).</p>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Data Inizio Fornitura</label>
                        <input type="text" name="data_inizio_fornitura" class="form-control" placeholder="es. 09/2024 o 15/09/2024" value="{{ old('data_inizio_fornitura') }}">
                    </div>

                    <div class="col-md-9">
                        <label class="form-label">Descrizione Prodotto / Servizio</label>
                        <input type="text" name="descrizione_prodotto_servizio" class="form-control" placeholder="es. accessori ausiliari per prove" value="{{ old('descrizione_prodotto_servizio') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">N° Reclamo</label>
                        <input type="text" name="numero_reclamo" class="form-control" placeholder="es. -" value="{{ old('numero_reclamo', '-') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">N° RNC (Non Conformità)</label>
                        <input type="text" name="numero_rnc" class="form-control" placeholder="es. -" value="{{ old('numero_rnc', '-') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">N° RAC (Azione Correttiva)</label>
                        <input type="text" name="numero_rac" class="form-control" placeholder="es. -" value="{{ old('numero_rac', '-') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Esito Fornitura</label>
                        <select name="esito" class="form-select">
                            <option value="positivo" selected>Positivo / Conforme</option>
                            <option value="reclamo">Reclamo Emesso</option>
                            <option value="non_conforme">Non Conforme</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note Fornitura</label>
                        <input type="text" name="note_fornitura" class="form-control" placeholder="Note sulla fornitura...">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 4. DOCUMENTI & CERTIFICATI ALLEGATI ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-paperclip text-info"></i> 4. Allegati &amp; Certificati di Qualità
            </div>
            <div class="form-card-body">
                <label class="form-label">Carica Documenti (Certificati ISO 9001, Autocertificazioni, Schede Tecniche, Visura)</label>
                <input type="file" name="documenti[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                <small class="text-muted">Puoi selezionare più file contemporaneamente (PDF, DOC, DOCX, immagini - max 20MB per file).</small>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 mb-5">
            <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary px-4 py-2">Annulla</a>
            <button type="submit" class="btn btn-danger fw-semibold px-4 py-2">
                <i class="bi bi-check-circle me-1"></i> Salva Scheda Fornitore
            </button>
        </div>
    </form>
@endsection
