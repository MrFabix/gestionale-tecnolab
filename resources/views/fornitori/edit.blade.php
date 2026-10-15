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
            <div class="small text-muted fw-bold text-uppercase">Modifica Scheda Fornitore</div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-pencil-square text-primary me-2"></i>{{ $fornitore->ragione_sociale }}
                <span class="badge bg-light text-dark border mono ms-2">{{ $fornitore->codice ?? 'ID '.$fornitore->id }}</span>
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fornitori.show', $fornitore) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i> Visualizza Scheda
            </a>
            <a href="{{ route('fornitori.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Elenco
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

    <form method="POST" action="{{ route('fornitori.update', $fornitore) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ── 1. ESTREMI DEL FORNITORE ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-building text-danger"></i> 1. Estremi del Fornitore
            </div>
            <div class="form-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Codice Fornitore</label>
                        <input type="text" name="codice" class="form-control" placeholder="es. 001" value="{{ old('codice', $fornitore->codice) }}">
                    </div>

                    <div class="col-md-9">
                        <label class="form-label">Ragione Sociale / Fornitore <span class="text-danger">*</span></label>
                        <input type="text" name="ragione_sociale" class="form-control" value="{{ old('ragione_sociale', $fornitore->ragione_sociale) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Indirizzo (Via, Numero civico)</label>
                        <input type="text" name="indirizzo" class="form-control" value="{{ old('indirizzo', $fornitore->indirizzo) }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">CAP</label>
                        <input type="text" name="cap" class="form-control" value="{{ old('cap', $fornitore->cap) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Città</label>
                        <input type="text" name="citta" class="form-control" value="{{ old('citta', $fornitore->citta) }}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Prov.</label>
                        <input type="text" name="provincia" class="form-control text-uppercase" maxlength="5" value="{{ old('provincia', $fornitore->provincia) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Partita IVA</label>
                        <input type="text" name="partita_iva" class="form-control mono" value="{{ old('partita_iva', $fornitore->partita_iva) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Codice Fiscale</label>
                        <input type="text" name="codice_fiscale" class="form-control mono" value="{{ old('codice_fiscale', $fornitore->codice_fiscale) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $fornitore->telefono) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cellulare</label>
                        <input type="text" name="cellulare" class="form-control" value="{{ old('cellulare', $fornitore->cellulare) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" value="{{ old('email', $fornitore->email) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">PEC</label>
                        <input type="text" name="pec" class="form-control" value="{{ old('pec', $fornitore->pec) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sito Internet</label>
                        <input type="text" name="sito_web" class="form-control" value="{{ old('sito_web', $fornitore->sito_web) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Referente di Contatto</label>
                        <input type="text" name="referente" class="form-control" value="{{ old('referente', $fornitore->referente) }}">
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
                        <textarea name="prodotti_acquistati" class="form-control" rows="3">{{ old('prodotti_acquistati', $fornitore->prodotti_acquistati) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Criteri di Qualifica Adottati</label>
                        <textarea name="criteri_qualifica" class="form-control" rows="3">{{ old('criteri_qualifica', $fornitore->criteri_qualifica) }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stato Qualifica <span class="text-danger">*</span></label>
                        <select name="stato_qualifica" class="form-select" required>
                            <option value="qualificato" {{ old('stato_qualifica', $fornitore->stato_qualifica) === 'qualificato' ? 'selected' : '' }}>Qualificato (Idoneo)</option>
                            <option value="in_valutazione" {{ old('stato_qualifica', $fornitore->stato_qualifica) === 'in_valutazione' ? 'selected' : '' }}>In Valutazione / Audit</option>
                            <option value="sospeso" {{ old('stato_qualifica', $fornitore->stato_qualifica) === 'sospeso' ? 'selected' : '' }}>Sospeso (Non Conformità aperte)</option>
                            <option value="non_qualificato" {{ old('stato_qualifica', $fornitore->stato_qualifica) === 'non_qualificato' ? 'selected' : '' }}>Non Qualificato</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Data Qualifica / Ammissione</label>
                        <input type="date" name="data_qualifica" class="form-control" value="{{ old('data_qualifica', $fornitore->data_qualifica ? $fornitore->data_qualifica->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Scadenza Qualifica / Riesame</label>
                        <input type="date" name="data_scadenza_qualifica" class="form-control" value="{{ old('data_scadenza_qualifica', $fornitore->data_scadenza_qualifica ? $fornitore->data_scadenza_qualifica->format('Y-m-d') : '') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Motivazione Cambio Qualifica <span class="text-muted small">(se lo stato viene modificato, verrà memorizzata nello storico PG04)</span></label>
                        <input type="text" name="motivo_variazione" class="form-control" placeholder="Es. Riqualificazione annuale / Riesame documentazione / Cambio stato operativo...">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Note Generali</label>
                        <textarea name="note" class="form-control" rows="2">{{ old('note', $fornitore->note) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. DOCUMENTI ALLEGATI & UPLOAD NUOVI ── --}}
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-paperclip text-info"></i> 3. Documenti &amp; Certificati di Qualità
            </div>
            <div class="form-card-body">
                @if($documenti->isNotEmpty())
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">File Attualmente Allegati:</h6>
                    <div class="list-group mb-4">
                        @foreach($documenti as $doc)
                            <div class="list-group-item d-flex align-items-center justify-content-between p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $doc->name }}</div>
                                        <div class="text-muted small">
                                            {{ $doc->human_readable_size }} &bull; Caricato il {{ $doc->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ $doc->getUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download me-1"></i> Scarica
                                    </a>
                                    <form action="{{ route('fornitori.media.destroy', [$fornitore, $doc->id]) }}" method="POST" class="d-inline" data-confirm="Vuoi davvero eliminare l'allegato {{ $doc->name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <label class="form-label">Carica Altri Documenti / Certificati</label>
                <input type="file" name="documenti[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                <small class="text-muted">Puoi selezionare nuovi file da allegare a questa scheda fornitore.</small>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-2 mb-5">
            <a href="{{ route('fornitori.show', $fornitore) }}" class="btn btn-outline-secondary px-4 py-2">Annulla</a>
            <button type="submit" class="btn btn-danger fw-semibold px-4 py-2">
                <i class="bi bi-check-circle me-1"></i> Salva Modifiche
            </button>
        </div>
    </form>
@endsection
