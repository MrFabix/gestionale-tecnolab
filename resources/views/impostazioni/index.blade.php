@extends('layouts.app')

@section('content')
<style>
    .doc-header-card {
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
    .section-card-body {
        padding: 1.5rem;
    }
    .btn-action {
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 0.65rem 1.3rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
    }
    .btn-action-primary {
        background: #d90429;
        color: #fff;
    }
    .btn-action-primary:hover {
        background: #b70322;
        color: #fff;
    }
    .btn-action-secondary {
        background: #2b2d42;
        color: #fff;
    }
    .btn-action-secondary:hover {
        background: #1e202f;
        color: #fff;
    }
</style>

<div class="container-fluid px-0">
    {{-- Header --}}
    <div class="doc-header-card">
        <div>
            <span class="doc-proc-code">SISTEMA & QUALITÀ</span>
            <h1 class="doc-title">Impostazioni Globali Gestionale</h1>
            <div class="text-white-50 mt-1" style="font-size: 0.9rem;">
                Configurazione dei parametri operativi, soglie temporali e regole automatiche del sistema.
            </div>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('impostazioni.verificaInattivita') }}" method="POST">
                @csrf
                <button type="submit" class="btn-action btn-action-secondary shadow-sm">
                    <i class="bi bi-arrow-repeat"></i> Verifica Fornitori Ora
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Card Opzioni Fornitori --}}
        <div class="col-lg-8">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="section-card-title">
                        <i class="bi bi-truck text-danger"></i> Regole di Qualifica Fornitori (MD PG04)
                    </h5>
                    <span class="badge bg-primary">Globale</span>
                </div>
                <div class="section-card-body">
                    <form action="{{ route('impostazioni.update') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Soglia Temporale di Inattività (in Mesi) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group" style="max-width: 320px;">
                                <input type="number" 
                                       name="fornitori_inattivita_mesi" 
                                       class="form-control @error('fornitori_inattivita_mesi') is-invalid @enderror" 
                                       value="{{ old('fornitori_inattivita_mesi', $mesiInattivita) }}" 
                                       min="1" 
                                       max="120" 
                                       required>
                                <span class="input-group-text">Mesi</span>
                            </div>
                            <div class="form-text mt-1">
                                Indicare dopo quanti mesi senza nuovi ordini di acquisto (ODA) o forniture registrate un fornitore perde la qualifica.
                                <br><strong>Esempi comuni:</strong> <code>12 mesi</code> (1 anno - raccomandato procedura qualità), <code>24 mesi</code> (2 anni), <code>6 mesi</code> (semestrale).
                            </div>
                            @error('fornitori_inattivita_mesi')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="squalificaSwitch" 
                                       name="fornitori_squalifica_automatica_attiva" 
                                       value="1" 
                                       {{ $squalificaAttiva ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 fw-bold" for="squalificaSwitch">
                                    Abilita squalifica automatica per inattività
                                </label>
                            </div>
                            <div class="form-text ms-1">
                                Se abilitato, ogni fornitore privo di acquisti entro il periodo specificato viene automaticamente retrocesso dallo stato <em>Qualificato</em> registrando l'evento nello storico.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Stato applicato in caso di inattività <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="fornitori_stato_dopo_inattivita" 
                                           id="statoNonQualificato" 
                                           value="non_qualificato" 
                                           {{ $statoDopoInattivita === 'non_qualificato' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statoNonQualificato">
                                        <span class="badge bg-danger me-1">Non Qualificato</span> (Predefinito procedura)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="fornitori_stato_dopo_inattivita" 
                                           id="statoSospeso" 
                                           value="sospeso" 
                                           {{ $statoDopoInattivita === 'sospeso' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statoSospeso">
                                        <span class="badge bg-secondary me-1">Sospeso</span> (In attesa di riesame)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-action btn-action-primary shadow-sm">
                                <i class="bi bi-check-lg"></i> Salva Impostazioni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Box Informativo e Tracciabilità --}}
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-card-header">
                    <h5 class="section-card-title">
                        <i class="bi bi-shield-check text-success"></i> Tracciabilità & Audit (MD PG04)
                    </h5>
                </div>
                <div class="section-card-body">
                    <p class="small text-muted mb-3">
                        In conformità ai requisiti di qualità ISO / PG04, ogni variazione di qualifica viene permanentemente memorizzata nella scheda del fornitore:
                    </p>
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-clock-history text-primary mt-1"></i>
                            <div><strong>Timestamp esatto</strong> e stato precedente/nuovo.</div>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-person text-primary mt-1"></i>
                            <div><strong>Autore</strong>: utente operatore oppure <em>Sistema Automatico</em>.</div>
                        </li>
                        <li class="mb-2 d-flex align-items-start gap-2">
                            <i class="bi bi-card-text text-primary mt-1"></i>
                            <div><strong>Motivo dettagliato</strong> contenente la data dell'ultimo acquisto registrato e la soglia applicata.</div>
                        </li>
                    </ul>

                    <div class="alert alert-light border small text-muted mb-0">
                        <i class="bi bi-info-circle text-info me-1"></i>
                        La verifica programmata viene eseguita giornalmente tramite task di background (<code>fornitori:verifica-inattivita</code>). Puoi avviare un controllo immediato con il pulsante <strong>"Verifica Fornitori Ora"</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
