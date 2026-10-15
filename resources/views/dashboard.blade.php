@extends('layouts.app')

@section('content')
    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --tl-red: #d90429;
            --tl-red-hover: #b71c1c;
            --tl-dark: #111422;
            --tl-card: #ffffff;
            --tl-border: #e2e8f0;
            --tl-text: #0f172a;
            --tl-muted: #64748b;
        }

        .dash-wrap {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--tl-text);
            max-width: 1650px;
            margin: 0 auto;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* ── HERO COMPATTA DI BENVENUTO ─────────────────────────── */
        .dash-welcome {
            background: linear-gradient(135deg, #111422 0%, #1a1e30 60%, #0d0f18 100%);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.75rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .dash-welcome h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .dash-welcome h1 span {
            color: #ff4d6d;
        }

        .dash-welcome p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
        }

        .hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 1.2rem;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
        }

        .hero-status-pill.danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
        }

        .hero-status-pill.success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #6ee7b7;
        }

        /* ── COLLEGAMENTI VELOCI (QUICK ACTIONS GRID) ────────────── */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-btn-card {
            background: #ffffff;
            border: 1.5px solid var(--tl-border);
            border-radius: 14px;
            padding: 1.15rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.95rem;
            text-decoration: none;
            color: var(--tl-text);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .quick-btn-card:hover {
            transform: translateY(-3px);
            border-color: #cbd5e1;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            color: var(--tl-text);
        }

        .quick-btn-card.highlight {
            background: linear-gradient(135deg, #d90429 0%, #b71c1c 100%);
            border-color: #d90429;
            color: #ffffff;
        }

        .quick-btn-card.highlight:hover {
            box-shadow: 0 8px 24px rgba(217, 4, 41, 0.35);
            color: #ffffff;
        }

        .quick-btn-card.highlight .quick-icon {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .quick-btn-card.highlight .quick-subtitle {
            color: rgba(255, 255, 255, 0.8);
        }

        .quick-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .quick-title {
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        .quick-subtitle {
            font-size: 0.75rem;
            color: var(--tl-muted);
            margin-top: 0.2rem;
            line-height: 1.2;
        }

        /* ── SEZIONE ALERT (TUTTO VISIBILE SENZA TABS!) ─────────── */
        .alert-section-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .alert-column-card {
            background: #ffffff;
            border: 1px solid var(--tl-border);
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .alert-column-card.has-danger {
            border-top: 4px solid #ef4444;
        }

        .alert-column-card.has-warning {
            border-top: 4px solid #f59e0b;
        }

        .alert-column-card.is-clean {
            border-top: 4px solid #10b981;
        }

        .alert-col-header {
            padding: 1.1rem 1.3rem;
            border-bottom: 1px solid var(--tl-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fbfcfd;
        }

        .alert-col-title {
            font-size: 0.98rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-items-list {
            padding: 0;
            margin: 0;
            list-style: none;
            flex: 1;
        }

        .alert-entry {
            padding: 1rem 1.3rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: background 0.15s ease;
        }

        .alert-entry:hover {
            background: #f8fafc;
        }

        .alert-entry:last-child {
            border-bottom: none;
        }

        .alert-entry.expired-entry {
            background: #fffafa;
        }

        .alert-entry.expired-entry:hover {
            background: #fff5f5;
        }

        .alert-pill {
            padding: 0.28rem 0.65rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }

        .alert-pill.red {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .alert-pill.amber {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .clean-state-box {
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #64748b;
        }

        .clean-state-box i {
            font-size: 2.4rem;
            color: #10b981;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* ── AGENDA E IMPEGNI RECENTI ───────────────────────────── */
        .agenda-card {
            background: #ffffff;
            border: 1px solid var(--tl-border);
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 1.25rem 1.4rem;
            margin-top: 1.5rem;
        }

        .agenda-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border-radius: 10px;
            border-left: 4px solid var(--tl-red);
            margin-bottom: 0.5rem;
        }

        .agenda-item:last-child {
            margin-bottom: 0;
        }
    </style>

    <div class="dash-wrap">

        {{-- ══════════════════════════════════════════════════════════
             1. BENVENUTO E STATO GENERALE
        ══════════════════════════════════════════════════════════ --}}
        @php
            $ora = (int) now()->format('H');
            $saluto = ($ora >= 5 && $ora < 13) ? 'Buongiorno' : (($ora >= 13 && $ora < 19) ? 'Buon pomeriggio' : 'Buonasera');
        @endphp

        <div class="dash-welcome">
            <div>
                <h1>{{ $saluto }}, <span>{{ Auth::user()->name }}</span></h1>
                <p>
                    <i class="bi bi-calendar3 me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                    &bull; Accesso rapido alle operazioni e controllo scadenze Tecnolab
                </p>
            </div>

            <div>
                @if($nAlert > 0)
                    <div class="hero-status-pill danger">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <span>{{ $nAlert }} Alert Richiedono Attenzione ({{ $totaleScaduti }} Scadute &bull; {{ $totaleInScadenza }} In Scadenza)</span>
                    </div>
                @else
                    <div class="hero-status-pill success">
                        <i class="bi bi-patch-check-fill fs-5"></i>
                        <span>Tutte le Tarature e Corsi sono in Regola</span>
                    </div>
                @endif
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════
             2. COLLEGAMENTI VELOCI (QUICK ACTIONS)
        ══════════════════════════════════════════════════════════ --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-lightning-charge-fill text-danger me-1"></i> Collegamenti Veloci
            </h5>
            <span class="text-muted small">Accesso immediato alle funzioni di laboratorio</span>
        </div>

        <div class="quick-actions-grid">
            {{-- Nuovo Report (Highlight Principale) --}}
            <a href="{{ route('reports.wizard.step1') }}" class="quick-btn-card highlight">
                <div class="quick-icon">
                    <i class="bi bi-plus-circle-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Nuovo Report</div>
                    <div class="quick-subtitle">Crea rapporto di prova</div>
                </div>
            </a>

            {{-- Nuova Commessa --}}
            <a href="{{ route('commesse.create') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #ede9fe; color: #6d28d9;">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Nuova Commessa</div>
                    <div class="quick-subtitle">{{ $countCommesse }} attive</div>
                </div>
            </a>

            {{-- Nuova Offerta --}}
            <a href="{{ route('offerte.create') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #dcfce7; color: #15803d;">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Nuova Offerta</div>
                    <div class="quick-subtitle">{{ $countOfferte }} in corso</div>
                </div>
            </a>

            {{-- Calendario Prove --}}
            <a href="{{ route('eventi.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Calendario</div>
                    <div class="quick-subtitle">Prove &amp; appuntamenti</div>
                </div>
            </a>

            {{-- Attrezzature --}}
            <a href="{{ route('attrezzature.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #e0f2fe; color: #0369a1;">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div class="quick-title">Attrezzature</div>
                    <div class="quick-subtitle">{{ $countMacchine }} macchine</div>
                </div>
            </a>

            {{-- Personale --}}
            <a href="{{ route('personale.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #fef3c7; color: #b45309;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Personale</div>
                    <div class="quick-subtitle">{{ $countPersonale }} dipendenti</div>
                </div>
            </a>

            {{-- Clienti --}}
            <a href="{{ route('clienti.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #f1f5f9; color: #334155;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Clienti</div>
                    <div class="quick-subtitle">{{ $countClienti }} anagrafiche</div>
                </div>
            </a>

            {{-- Fornitori --}}
            <a href="{{ route('fornitori.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #fef2f2; color: #b91c1c;">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <div class="quick-title">Fornitori</div>
                    <div class="quick-subtitle">{{ $countFornitori ?? 0 }} qualificati</div>
                </div>
            </a>

            {{-- Richieste Acquisto (RDA) --}}
            <a href="{{ route('rda.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #fff1f2; color: #e11d48;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div>
                    <div class="quick-title">RDA Acquisti</div>
                    <div class="quick-subtitle">
                        @if(($countRdaInAttesa ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill">{{ $countRdaInAttesa }} in attesa</span>
                        @else
                            {{ $countRdaTotali ?? 0 }} richieste
                        @endif
                    </div>
                </div>
            </a>

            {{-- Ordini Acquisto (ODA) --}}
            <a href="{{ route('oda.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #f0fdf4; color: #16a34a;">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div>
                    <div class="quick-title">Ordini ODA</div>
                    <div class="quick-subtitle">{{ $countOda ?? 0 }} emessi</div>
                </div>
            </a>

            {{-- Documenti --}}
            <a href="{{ route('documenti.index') }}" class="quick-btn-card">
                <div class="quick-icon" style="background: #f3e8ff; color: #7e22ce;">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <div class="quick-title">Documenti</div>
                    <div class="quick-subtitle">Archivio e cartelle</div>
                </div>
            </a>
        </div>


        {{-- ══════════════════════════════════════════════════════════
             3. SEZIONE ALERT IN PRIMO PIANO (TUTTO APERTO, NIENTE TABS!)
        ══════════════════════════════════════════════════════════ --}}
        <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
            <div>
                <h5 class="alert-section-title">
                    <i class="bi bi-shield-exclamation text-danger"></i>
                    Centro Scadenze &amp; Conformità di Laboratorio
                </h5>
                <p class="text-muted small mb-0">Tutti gli avvisi di taratura macchine, manutenzioni e abilitazioni del personale visibili a colpo d'occhio</p>
            </div>

            <div>
                @if($totaleScaduti > 0)
                    <span class="badge bg-danger px-3 py-2 fw-bold fs-6 rounded-pill">
                        <i class="bi bi-exclamation-diamond-fill me-1"></i> {{ $totaleScaduti }} Fuori Norma
                    </span>
                @endif
                @if($totaleInScadenza > 0)
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold fs-6 rounded-pill ms-1">
                        <i class="bi bi-clock-history me-1"></i> {{ $totaleInScadenza }} In Scadenza nei 30gg
                    </span>
                @endif
            </div>
        </div>

        {{-- 3 COLONNE CHIARE E APERTE CONTEMPORANEAMENTE --}}
        <div class="row g-3">

            {{-- ── COLONNA 1: TARATURE ATTREZZATURE ── --}}
            <div class="col-lg-4">
                @php
                    $tarHasDanger = $taratureScadute->isNotEmpty();
                    $tarHasWarning = $taratureInScadenza->isNotEmpty();
                    $tarClass = $tarHasDanger ? 'has-danger' : ($tarHasWarning ? 'has-warning' : 'is-clean');
                @endphp
                <div class="alert-column-card {{ $tarClass }}">
                    <div class="alert-col-header">
                        <div class="alert-col-title">
                            <i class="bi bi-tools text-primary"></i>
                            <span>Tarature Strumenti</span>
                        </div>
                        <span class="badge {{ ($taratureScadute->count() + $taratureInScadenza->count()) > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-2 py-1">
                            {{ $taratureScadute->count() + $taratureInScadenza->count() }}
                        </span>
                    </div>

                    @if($taratureScadute->isNotEmpty() || $taratureInScadenza->isNotEmpty())
                        <ul class="alert-items-list">
                            {{-- Scadute --}}
                            @foreach($taratureScadute as $t)
                                <li class="alert-entry expired-entry">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $t->attrezzatura->nome }}</div>
                                        <div class="text-muted small">
                                            @if($t->attrezzatura->codice) <span class="badge bg-light text-muted border mono me-1">{{ $t->attrezzatura->codice }}</span> @endif
                                            Scaduta: <strong class="text-danger mono">{{ $t->data_scadenza->format('d/m/Y') }}</strong>
                                            ({{ $t->data_scadenza->diffForHumans() }})
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="alert-pill red"><i class="bi bi-x-circle-fill"></i> SCADUTA</span>
                                        <a href="{{ route('attrezzature.show', $t->attrezzatura_id) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                            Vedi scheda
                                        </a>
                                    </div>
                                </li>
                            @endforeach

                            {{-- In Scadenza --}}
                            @foreach($taratureInScadenza as $t)
                                <li class="alert-entry">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $t->attrezzatura->nome }}</div>
                                        <div class="text-muted small">
                                            @if($t->attrezzatura->codice) <span class="badge bg-light text-muted border mono me-1">{{ $t->attrezzatura->codice }}</span> @endif
                                            Scade: <strong class="text-warning mono">{{ $t->data_scadenza->format('d/m/Y') }}</strong>
                                            (tra {{ now()->diffInDays($t->data_scadenza) }} gg)
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="alert-pill amber"><i class="bi bi-clock-history"></i> IN SCADENZA</span>
                                        <a href="{{ route('attrezzature.show', $t->attrezzatura_id) }}" class="btn btn-sm btn-outline-warning text-dark py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                            Vedi scheda
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="clean-state-box">
                            <i class="bi bi-check-circle-fill"></i>
                            <h6 class="fw-bold text-dark mb-1">Tarature in Regola</h6>
                            <p class="small text-muted mb-0">Nessuna taratura scaduta o in scadenza nei prossimi 30 giorni.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── COLONNA 2: MANUTENZIONI MACCHINARI ── --}}
            <div class="col-lg-4">
                @php
                    $manHasDanger = $manutenzioniScadute->isNotEmpty();
                    $manHasWarning = $manutenzioniInScadenza->isNotEmpty();
                    $manClass = $manHasDanger ? 'has-danger' : ($manHasWarning ? 'has-warning' : 'is-clean');
                @endphp
                <div class="alert-column-card {{ $manClass }}">
                    <div class="alert-col-header">
                        <div class="alert-col-title">
                            <i class="bi bi-wrench-adjustable text-warning"></i>
                            <span>Manutenzioni Macchine</span>
                        </div>
                        <span class="badge {{ ($manutenzioniScadute->count() + $manutenzioniInScadenza->count()) > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-2 py-1">
                            {{ $manutenzioniScadute->count() + $manutenzioniInScadenza->count() }}
                        </span>
                    </div>

                    @if($manutenzioniScadute->isNotEmpty() || $manutenzioniInScadenza->isNotEmpty())
                        <ul class="alert-items-list">
                            {{-- Scadute --}}
                            @foreach($manutenzioniScadute as $m)
                                <li class="alert-entry expired-entry">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $m->attrezzatura->nome }}</div>
                                        <div class="text-muted small">
                                            Tipo: <strong>{{ ucfirst($m->tipo ?? 'Manutenzione') }}</strong> &bull;
                                            Scaduta: <strong class="text-danger mono">{{ $m->prossima_scadenza->format('d/m/Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="alert-pill red"><i class="bi bi-x-circle-fill"></i> SCADUTA</span>
                                        <a href="{{ route('attrezzature.show', $m->attrezzatura_id) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                            Vedi scheda
                                        </a>
                                    </div>
                                </li>
                            @endforeach

                            {{-- In Scadenza --}}
                            @foreach($manutenzioniInScadenza as $m)
                                <li class="alert-entry">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $m->attrezzatura->nome }}</div>
                                        <div class="text-muted small">
                                            Tipo: <strong>{{ ucfirst($m->tipo ?? 'Manutenzione') }}</strong> &bull;
                                            Prevista: <strong class="text-warning mono">{{ $m->prossima_scadenza->format('d/m/Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="alert-pill amber"><i class="bi bi-clock-history"></i> IN SCADENZA</span>
                                        <a href="{{ route('attrezzature.show', $m->attrezzatura_id) }}" class="btn btn-sm btn-outline-warning text-dark py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                            Vedi scheda
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="clean-state-box">
                            <i class="bi bi-check-circle-fill"></i>
                            <h6 class="fw-bold text-dark mb-1">Manutenzioni in Regola</h6>
                            <p class="small text-muted mb-0">Tutti i macchinari risultano regolarmente manutenuti.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── COLONNA 3: FORMAZIONE & ABILITAZIONI PERSONALE ── --}}
            <div class="col-lg-4">
                @php
                    $formHasDanger = $formazioniScadute->isNotEmpty();
                    $formHasWarning = $formazioniInScadenza->isNotEmpty();
                    $formClass = $formHasDanger ? 'has-danger' : ($formHasWarning ? 'has-warning' : 'is-clean');
                @endphp
                <div class="alert-column-card {{ $formClass }}">
                    <div class="alert-col-header">
                        <div class="alert-col-title">
                            <i class="bi bi-mortarboard text-info"></i>
                            <span>Abilitazioni &amp; Corsi Personale</span>
                        </div>
                        <span class="badge {{ ($formazioniScadute->count() + $formazioniInScadenza->count()) > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-2 py-1">
                            {{ $formazioniScadute->count() + $formazioniInScadenza->count() }}
                        </span>
                    </div>

                    @if($formazioniScadute->isNotEmpty() || $formazioniInScadenza->isNotEmpty())
                        <ul class="alert-items-list">
                            {{-- Scadute --}}
                            @foreach($formazioniScadute as $f)
                                <li class="alert-entry expired-entry">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $f->personale->cognome }} {{ $f->personale->nome }}</div>
                                        <div class="text-muted small">
                                            Corso: <strong>{{ Str::limit($f->titolo, 28) }}</strong> &bull;
                                            Scaduto: <strong class="text-danger mono">{{ $f->data_scadenza->format('d/m/Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="alert-pill red"><i class="bi bi-x-circle-fill"></i> SCADUTO</span>
                                        <a href="{{ route('personale.show', $f->personale_id) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                            Vedi scheda
                                        </a>
                                    </div>
                                </li>
                            @endforeach

                            {{-- In Scadenza --}}
                            @foreach($formazioniInScadenza as $f)
                                <li class="alert-entry">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $f->personale->cognome }} {{ $f->personale->nome }}</div>
                                        <div class="text-muted small">
                                            Corso: <strong>{{ Str::limit($f->titolo, 28) }}</strong> &bull;
                                            Scade: <strong class="text-warning mono">{{ $f->data_scadenza->format('d/m/Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="alert-pill amber"><i class="bi bi-clock-history"></i> IN SCADENZA</span>
                                        <a href="{{ route('personale.show', $f->personale_id) }}" class="btn btn-sm btn-outline-warning text-dark py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                            Vedi scheda
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="clean-state-box">
                            <i class="bi bi-check-circle-fill"></i>
                            <h6 class="fw-bold text-dark mb-1">Corsi del Personale in Regola</h6>
                            <p class="small text-muted mb-0">Nessuna abilitazione o corso scaduto nel team.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>


        {{-- ══════════════════════════════════════════════════════════
             4. OFFERTE IN SCADENZA (AVVISO RAPIDO SE PRESENTI)
        ══════════════════════════════════════════════════════════ --}}
        @if($offerteInScadenza->isNotEmpty())
            <div class="mt-4 p-3 bg-warning-subtle border border-warning rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                    <div>
                        <strong class="text-dark">Ci sono {{ $offerteInScadenza->count() }} offerte inviate con validità in scadenza nei prossimi 15 giorni:</strong>
                        <span class="text-muted ms-1">
                            @foreach($offerteInScadenza as $o)
                                Offerta <strong>{{ $o->numero }}</strong> ({{ $o->cliente->ragione_sociale }} - scade {{ $o->validita_fino->format('d/m/Y') }})@if(!$loop->last), @endif
                            @endforeach
                        </span>
                    </div>
                </div>
                <a href="{{ route('offerte.index') }}" class="btn btn-sm btn-dark fw-semibold px-3 py-1">
                    Gestisci Offerte &rarr;
                </a>
            </div>
        @endif


        {{-- ══════════════════════════════════════════════════════════
             5. PROSSIMI EVENTI & APPUNTAMENTI IN AGENDA
        ══════════════════════════════════════════════════════════ --}}
        @if($prossimiEventi->isNotEmpty())
            <div class="agenda-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-calendar-check text-danger me-1"></i> Prossimi Eventi &amp; Prove in Agenda
                    </h6>
                    <a href="{{ route('eventi.index') }}" class="text-decoration-none text-danger fw-semibold small">
                        Apri Calendario Completo &rarr;
                    </a>
                </div>

                <div class="row g-2">
                    @foreach($prossimiEventi as $ev)
                        <div class="col-md-6 col-lg-3">
                            <div class="agenda-item" style="border-left-color: {{ $ev->colore ?? 'var(--tl-red)' }};">
                                <div class="flex-grow-1 text-truncate">
                                    <div class="fw-bold text-dark small text-truncate">{{ $ev->titolo }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($ev->inizio)->translatedFormat('d M H:i') }}
                                    </div>
                                </div>
                                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">
                                    {{ \Carbon\Carbon::parse($ev->inizio)->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>{{-- /dash-wrap --}}
@endsection
