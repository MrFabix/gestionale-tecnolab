@extends('layouts.app')

@section('content')
    @php
        $nAlert = $taratureScadute->count() + $taratureInScadenza->count()
                + $manutenzioniScadute->count() + $manutenzioniInScadenza->count()
                + $formazioniScadute->count() + $formazioniInScadenza->count();
    @endphp

    <style>
        /* ── Font ────────────────────────────────────────────────── */
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        .dash-root { font-family: 'DM Sans', sans-serif; }

        /* ── Hero header ─────────────────────────────────────────── */
        .dash-hero {
            background: #0d0d0d;
            border-radius: 20px;
            padding: 2rem 2.5rem 1.75rem;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
        }
        .dash-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 80% at 90% -20%, rgba(192,0,0,.35) 0%, transparent 60%),
                radial-gradient(ellipse 40% 60% at 10% 110%, rgba(192,0,0,.15) 0%, transparent 55%);
            pointer-events: none;
        }
        .dash-hero h1 { font-size: 1.9rem; font-weight: 700; color: #fff; margin: 0; letter-spacing: -.5px; }
        .dash-hero p  { color: rgba(255,255,255,.5); margin: .25rem 0 0; font-size: .9rem; }

        /* ── Filtro periodo ──────────────────────────────────────── */
        .period-bar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: .75rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .period-bar .pill-p {
            padding: .3rem .85rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1.5px solid #e5e7eb;
            background: #f9fafb;
            color: #374151;
            text-decoration: none;
            transition: all .15s;
        }
        .period-bar .pill-p:hover,
        .period-bar .pill-p.active { border-color: #c00000; background: #c00000; color: #fff; }
        .period-bar .sep { color: #d1d5db; margin: 0 .25rem; }

        /* ── KPI cards ───────────────────────────────────────────── */
        .kpi-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.4rem 1.5rem 1.2rem;
            position: relative;
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
            height: 100%;
        }
        .kpi-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); transform: translateY(-2px); }
        .kpi-card .kpi-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .kpi-card .kpi-value {
            font-size: 2.4rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -1px;
            font-family: 'DM Mono', monospace;
            margin-bottom: .3rem;
        }
        .kpi-card .kpi-label { font-size: .8rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .5px; }
        .kpi-card .kpi-sub   { font-size: .82rem; color: #6b7280; margin-top: .4rem; }
        .kpi-card .kpi-accent {
            position: absolute;
            bottom: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 999px;
            opacity: .07;
            transform: translate(20px, 20px);
        }

        /* colori icone */
        .ic-red    { background: #fee2e2; color: #c00000; }
        .ic-blue   { background: #dbeafe; color: #1d4ed8; }
        .ic-green  { background: #dcfce7; color: #15803d; }
        .ic-amber  { background: #fef9c3; color: #92400e; }
        .ic-purple { background: #ede9fe; color: #5b21b6; }
        .ic-teal   { background: #ccfbf1; color: #0f766e; }
        .ic-slate  { background: #f1f5f9; color: #475569; }

        /* ── Section title ───────────────────────────────────────── */
        .section-title {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #9ca3af;
            margin-bottom: .85rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .section-title::after { content: ''; flex: 1; height: 1px; background: #f0f0f0; }

        /* ── Chart card ──────────────────────────────────────────── */
        .chart-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            height: 100%;
        }
        .chart-card h6 { font-size: .85rem; font-weight: 700; color: #374151; margin-bottom: 1rem; }

        /* ── Alert list ──────────────────────────────────────────── */
        .alert-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .alert-card .alert-head {
            padding: .75rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f5f5f5;
        }
        .alert-card .alert-head h6 { margin: 0; font-size: .88rem; font-weight: 700; }
        .alert-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1.25rem;
            border-bottom: 1px solid #f9f9f9;
            font-size: .83rem;
            transition: background .1s;
        }
        .alert-item:hover { background: #fafafa; }
        .alert-item:last-child { border-bottom: none; }
        .alert-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .dot-red  { background: #ef4444; }
        .dot-warn { background: #f59e0b; }

        /* ── Feed attività ───────────────────────────────────────── */
        .feed-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }
        .feed-head { padding: .85rem 1.25rem; border-bottom: 1px solid #f5f5f5; }
        .feed-head h6 { margin: 0; font-size: .88rem; font-weight: 700; }
        .feed-item {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .7rem 1.25rem;
            border-bottom: 1px solid #f9f9f9;
            font-size: .83rem;
        }
        .feed-item:last-child { border-bottom: none; }
        .feed-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem; flex-shrink: 0;
        }
        .feed-meta { color: #9ca3af; font-size: .75rem; }

        /* ── Donut mini ──────────────────────────────────────────── */
        .donut-wrap { position: relative; display: inline-flex; align-items: center; justify-content: center; }
        .donut-center { position: absolute; text-align: center; }
        .donut-center .dv { font-size: 1.3rem; font-weight: 700; font-family: 'DM Mono', monospace; line-height: 1; }
        .donut-center .dl { font-size: .6rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; }

        /* ── Animazioni ──────────────────────────────────────────── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
        .kpi-card { animation: fadeUp .4s ease both; }
        .kpi-card:nth-child(1) { animation-delay: .05s; }
        .kpi-card:nth-child(2) { animation-delay: .10s; }
        .kpi-card:nth-child(3) { animation-delay: .15s; }
        .kpi-card:nth-child(4) { animation-delay: .20s; }
        .kpi-card:nth-child(5) { animation-delay: .25s; }
        .kpi-card:nth-child(6) { animation-delay: .30s; }

        /* ── Badge alert globale ─────────────────────────────────── */
        .badge-alert-glob {
            background: #fee2e2; color: #b91c1c;
            padding: .35rem .85rem; border-radius: 999px;
            font-size: .78rem; font-weight: 700;
        }
        .empty-alert { padding: 1rem 1.25rem; color: #9ca3af; font-size: .83rem; }
    </style>

    <div class="dash-root">

        {{-- ── HERO ──────────────────────────────────────────────── --}}
        <div class="dash-hero">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Dashboard</h1>
                    <p>Ciao, {{ Auth::user()->name }} — {{ now()->translatedFormat('l d F Y') }}</p>
                </div>
                @if($nAlert > 0)
                    <span class="badge-alert-glob">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ $nAlert }} alert attivi
        </span>
                @endif
            </div>
        </div>

        {{-- ── FILTRO PERIODO ───────────────────────────────────── --}}
        <div class="period-bar">
            <span style="font-size:.8rem;font-weight:600;color:#6b7280;">Periodo:</span>
            @foreach(['mese'=>'Questo mese','anno'=>'Quest\'anno','tutto'=>'Tutto'] as $p=>$l)
                <a href="{{ route('home', array_merge(request()->except('periodo','dal','al'), ['periodo'=>$p])) }}"
                   class="pill-p {{ $periodo===$p ? 'active' : '' }}">{{ $l }}</a>
            @endforeach
            <span class="sep">|</span>
            <form method="GET" action="{{ route('home') }}" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="periodo" value="custom">
                <input type="date" name="dal" class="form-control form-control-sm" style="border-radius:8px;font-size:.8rem;width:140px;"
                       value="{{ $periodo==='custom' && $dal ? $dal->format('Y-m-d') : '' }}">
                <span style="color:#9ca3af;font-size:.8rem;">→</span>
                <input type="date" name="al" class="form-control form-control-sm" style="border-radius:8px;font-size:.8rem;width:140px;"
                       value="{{ $periodo==='custom' && $al ? $al->format('Y-m-d') : '' }}">
                <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;">
                    <i class="bi bi-filter"></i> Applica
                </button>
            </form>
        </div>

        {{-- ── KPI CARDS ────────────────────────────────────────── --}}
        <p class="section-title"><i class="bi bi-grid-3x3-gap"></i> Panoramica</p>

        <div class="row g-3 mb-4">
            {{-- Clienti --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="kpi-card">
                    <div class="kpi-icon ic-blue"><i class="bi bi-people-fill"></i></div>
                    <div class="kpi-value text-primary" style="color:#1d4ed8!important;">{{ $clientiTotali }}</div>
                    <div class="kpi-label">Clienti</div>
                    <div class="kpi-sub">+{{ $clientiNuovi }} nel periodo</div>
                    <div class="kpi-accent" style="background:#1d4ed8;"></div>
                </div>
            </div>

            {{-- Commesse aperte --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="kpi-card">
                    <div class="kpi-icon ic-green"><i class="bi bi-briefcase-fill"></i></div>
                    <div class="kpi-value" style="color:#15803d;">{{ $commesseAperte }}</div>
                    <div class="kpi-label">Commesse aperte</div>
                    <div class="kpi-sub">{{ $commesseInLavoraz }} in lavorazione · {{ $commesseChiuse }} chiuse</div>
                    <div class="kpi-accent" style="background:#15803d;"></div>
                </div>
            </div>

            {{-- Report emessi --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="kpi-card">
                    <div class="kpi-icon ic-teal"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div class="kpi-value" style="color:#0f766e;">{{ $reportPeriodo }}</div>
                    <div class="kpi-label">Report emessi</div>
                    <div class="kpi-sub">{{ $reportTotali }} totali</div>
                    <div class="kpi-accent" style="background:#0f766e;"></div>
                </div>
            </div>

            {{-- Offerte accettate --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="kpi-card">
                    <div class="kpi-icon ic-purple"><i class="bi bi-file-earmark-check-fill"></i></div>
                    <div class="kpi-value" style="color:#5b21b6;">{{ $offerteAccettate }}</div>
                    <div class="kpi-label">Offerte accettate</div>
                    <div class="kpi-sub">{{ $offerteBozza }} bozze · {{ $offerteInviate }} inviate</div>
                    <div class="kpi-accent" style="background:#5b21b6;"></div>
                </div>
            </div>

            {{-- Valore offerte --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="kpi-card">
                    <div class="kpi-icon ic-amber"><i class="bi bi-currency-euro"></i></div>
                    <div class="kpi-value" style="color:#92400e;font-size:1.6rem;">{{ number_format($valoreAccettate,0,',','.') }}</div>
                    <div class="kpi-label">Valore offerte (€)</div>
                    <div class="kpi-sub">Offerte accettate nel periodo</div>
                    <div class="kpi-accent" style="background:#92400e;"></div>
                </div>
            </div>

            {{-- Alert totali --}}
            <div class="col-6 col-md-4 col-xl-2">
                <div class="kpi-card" style="{{ $nAlert > 0 ? 'border-color:#fca5a5;' : '' }}">
                    <div class="kpi-icon ic-red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div class="kpi-value" style="color:#c00000;">{{ $nAlert }}</div>
                    <div class="kpi-label">Alert attivi</div>
                    <div class="kpi-sub">
                        {{ $taratureScadute->count() + $taratureInScadenza->count() }} tarature ·
                        {{ $formazioniScadute->count() + $formazioniInScadenza->count() }} formaz.
                    </div>
                    <div class="kpi-accent" style="background:#c00000;"></div>
                </div>
            </div>
        </div>

        {{-- ── GRAFICI ──────────────────────────────────────────── --}}
        <p class="section-title"><i class="bi bi-bar-chart-fill"></i> Andamento</p>

        <div class="row g-3 mb-4">
            {{-- Grafico report per mese --}}
            <div class="col-md-7">
                <div class="chart-card">
                    <h6><i class="bi bi-file-earmark-text me-1 text-muted"></i>Report emessi — ultimi 6 mesi</h6>
                    <canvas id="chartReport" height="160"></canvas>
                </div>
            </div>

            {{-- Donut commesse --}}
            <div class="col-md-2">
                <div class="chart-card text-center">
                    <h6><i class="bi bi-briefcase me-1 text-muted"></i>Commesse</h6>
                    <div class="donut-wrap" style="width:120px;height:120px;margin:0 auto;">
                        <canvas id="chartCommesse"></canvas>
                        <div class="donut-center">
                            <div class="dv" style="font-size:1.1rem;">{{ $commesseAperte + $commesseInLavoraz + $commesseChiuse }}</div>
                            <div class="dl">totali</div>
                        </div>
                    </div>
                    <div class="mt-2" style="font-size:.72rem;line-height:1.8;">
                        <span style="color:#15803d;">●</span> Aperte {{ $commesseAperte }}&nbsp;
                        <span style="color:#f59e0b;">●</span> In lav. {{ $commesseInLavoraz }}<br>
                        <span style="color:#9ca3af;">●</span> Chiuse {{ $commesseChiuse }}
                    </div>
                </div>
            </div>

            {{-- Donut offerte --}}
            <div class="col-md-3">
                <div class="chart-card text-center">
                    <h6><i class="bi bi-file-earmark-text me-1 text-muted"></i>Offerte periodo</h6>
                    <div class="donut-wrap" style="width:120px;height:120px;margin:0 auto;">
                        <canvas id="chartOfferte"></canvas>
                        <div class="donut-center">
                            <div class="dv" style="font-size:1.1rem;">{{ $offerteBozza+$offerteInviate+$offerteAccettate+$offerteRifiutate }}</div>
                            <div class="dl">totali</div>
                        </div>
                    </div>
                    <div class="mt-2" style="font-size:.72rem;line-height:1.8;">
                        <span style="color:#6b7280;">●</span> Bozza {{ $offerteBozza }}&nbsp;
                        <span style="color:#1d4ed8;">●</span> Inv. {{ $offerteInviate }}<br>
                        <span style="color:#15803d;">●</span> Acc. {{ $offerteAccettate }}&nbsp;
                        <span style="color:#b91c1c;">●</span> Rif. {{ $offerteRifiutate }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── ALERT SEZIONE ────────────────────────────────────── --}}
        <p class="section-title"><i class="bi bi-exclamation-triangle"></i> Alert & Scadenze</p>

        <div class="row g-3 mb-4">

            {{-- Tarature --}}
            <div class="col-md-4">
                <div class="alert-card">
                    <div class="alert-head">
                        <h6><i class="bi bi-patch-check me-1 text-muted"></i>Tarature attrezzature</h6>
                        @if($taratureScadute->count() + $taratureInScadenza->count() > 0)
                            <span class="badge bg-danger">{{ $taratureScadute->count() + $taratureInScadenza->count() }}</span>
                        @endif
                    </div>
                    @foreach($taratureScadute as $t)
                        <div class="alert-item">
                            <span class="alert-dot dot-red"></span>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $t->attrezzatura->nome }}</div>
                                <div class="text-muted" style="font-size:.75rem;">Scaduta il {{ $t->data_scadenza->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('attrezzature.show', $t->attrezzatura) }}" class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" style="font-size:.72rem;">Vedi</a>
                        </div>
                    @endforeach
                    @foreach($taratureInScadenza as $t)
                        <div class="alert-item">
                            <span class="alert-dot dot-warn"></span>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $t->attrezzatura->nome }}</div>
                                <div class="text-muted" style="font-size:.75rem;">Scade il {{ $t->data_scadenza->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('attrezzature.show', $t->attrezzatura) }}" class="btn btn-xs btn-outline-warning btn-sm py-0 px-2" style="font-size:.72rem;">Vedi</a>
                        </div>
                    @endforeach
                    @if($taratureScadute->isEmpty() && $taratureInScadenza->isEmpty())
                        <div class="empty-alert"><i class="bi bi-check-circle text-success me-1"></i>Tutto in regola</div>
                    @endif
                </div>
            </div>

            {{-- Manutenzioni --}}
            <div class="col-md-4">
                <div class="alert-card">
                    <div class="alert-head">
                        <h6><i class="bi bi-wrench-adjustable me-1 text-muted"></i>Manutenzioni</h6>
                        @if($manutenzioniScadute->count() + $manutenzioniInScadenza->count() > 0)
                            <span class="badge bg-danger">{{ $manutenzioniScadute->count() + $manutenzioniInScadenza->count() }}</span>
                        @endif
                    </div>
                    @foreach($manutenzioniScadute as $m)
                        <div class="alert-item">
                            <span class="alert-dot dot-red"></span>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $m->attrezzatura->nome }}</div>
                                <div class="text-muted" style="font-size:.75rem;">Scaduta il {{ $m->prossima_scadenza->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('attrezzature.show', $m->attrezzatura) }}" class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" style="font-size:.72rem;">Vedi</a>
                        </div>
                    @endforeach
                    @foreach($manutenzioniInScadenza as $m)
                        <div class="alert-item">
                            <span class="alert-dot dot-warn"></span>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $m->attrezzatura->nome }}</div>
                                <div class="text-muted" style="font-size:.75rem;">Scade il {{ $m->prossima_scadenza->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('attrezzature.show', $m->attrezzatura) }}" class="btn btn-xs btn-outline-warning btn-sm py-0 px-2" style="font-size:.72rem;">Vedi</a>
                        </div>
                    @endforeach
                    @if($manutenzioniScadute->isEmpty() && $manutenzioniInScadenza->isEmpty())
                        <div class="empty-alert"><i class="bi bi-check-circle text-success me-1"></i>Tutto in regola</div>
                    @endif
                </div>
            </div>

            {{-- Formazione personale --}}
            <div class="col-md-4">
                <div class="alert-card">
                    <div class="alert-head">
                        <h6><i class="bi bi-mortarboard me-1 text-muted"></i>Formazione personale</h6>
                        @if($formazioniScadute->count() + $formazioniInScadenza->count() > 0)
                            <span class="badge bg-danger">{{ $formazioniScadute->count() + $formazioniInScadenza->count() }}</span>
                        @endif
                    </div>
                    @foreach($formazioniScadute as $f)
                        <div class="alert-item">
                            <span class="alert-dot dot-red"></span>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $f->personale->cognome }} {{ $f->personale->nome }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ Str::limit($f->titolo,35) }} — scaduta {{ $f->data_scadenza->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('personale.show', $f->personale) }}" class="btn btn-xs btn-outline-danger btn-sm py-0 px-2" style="font-size:.72rem;">Vedi</a>
                        </div>
                    @endforeach
                    @foreach($formazioniInScadenza as $f)
                        <div class="alert-item">
                            <span class="alert-dot dot-warn"></span>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $f->personale->cognome }} {{ $f->personale->nome }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ Str::limit($f->titolo,35) }} — scade {{ $f->data_scadenza->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('personale.show', $f->personale) }}" class="btn btn-xs btn-outline-warning btn-sm py-0 px-2" style="font-size:.72rem;">Vedi</a>
                        </div>
                    @endforeach
                    @if($formazioniScadute->isEmpty() && $formazioniInScadenza->isEmpty())
                        <div class="empty-alert"><i class="bi bi-check-circle text-success me-1"></i>Tutto in regola</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── FEED ATTIVITÀ ────────────────────────────────────── --}}
        <p class="section-title"><i class="bi bi-activity"></i> Attività recenti</p>

        <div class="row g-3 mb-2">
            {{-- Ultimi report --}}
            <div class="col-md-6">
                <div class="feed-card">
                    <div class="feed-head d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-1 text-muted"></i>Ultimi report</h6>
                        <a href="{{ route('reports.index') }}" style="font-size:.78rem;color:#c00000;text-decoration:none;">Vedi tutti →</a>
                    </div>
                    @forelse($ultimiReport as $r)
                        <div class="feed-item">
                            <div class="feed-icon ic-teal"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $r->rapporto_numero ?? 'N/A' }}
                                    <span class="badge bg-light text-muted ms-1" style="font-size:.68rem;">{{ ucfirst($r->tipo_prova) }}</span>
                                </div>
                                <div class="feed-meta">{{ $r->commessa?->cliente?->ragione_sociale ?? '—' }} · {{ $r->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('reports.show', $r) }}" style="font-size:.75rem;color:#9ca3af;text-decoration:none;">Apri →</a>
                        </div>
                    @empty
                        <div class="empty-alert">Nessun report recente.</div>
                    @endforelse
                </div>
            </div>

            {{-- Ultime offerte --}}
            <div class="col-md-6">
                <div class="feed-card">
                    <div class="feed-head d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-1 text-muted"></i>Ultime offerte</h6>
                        <a href="{{ route('offerte.index') }}" style="font-size:.78rem;color:#c00000;text-decoration:none;">Vedi tutte →</a>
                    </div>
                    @forelse($ultimiOfferte as $o)
                        @php
                            $bc = ['bozza'=>'bg-secondary','inviata'=>'bg-primary','accettata'=>'bg-success','rifiutata'=>'bg-danger'][$o->stato] ?? 'bg-secondary';
                        @endphp
                        <div class="feed-item">
                            <div class="feed-icon ic-purple"><i class="bi bi-file-earmark-check"></i></div>
                            <div class="flex-fill">
                                <div class="fw-semibold">{{ $o->numero }}
                                    <span class="badge {{ $bc }} ms-1" style="font-size:.68rem;">{{ ucfirst($o->stato) }}</span>
                                </div>
                                <div class="feed-meta">{{ $o->cliente->ragione_sociale }} · {{ $o->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('offerte.show', $o) }}" style="font-size:.75rem;color:#9ca3af;text-decoration:none;">Apri →</a>
                        </div>
                    @empty
                        <div class="empty-alert">Nessuna offerta recente.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>{{-- /dash-root --}}
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── Palette ──────────────────────────────────────────────────
        const RED    = '#c00000';
        const RED15  = 'rgba(192,0,0,.15)';
        const BLUE   = '#1d4ed8';
        const GREEN  = '#15803d';
        const AMBER  = '#f59e0b';
        const GRAY   = '#9ca3af';
        const PURPLE = '#5b21b6';

        Chart.defaults.font.family = "'DM Sans', sans-serif";
        Chart.defaults.color = '#9ca3af';

        // ── Report per mese (bar chart) ───────────────────────────────
        const ctxR = document.getElementById('chartReport').getContext('2d');
        const gradR = ctxR.createLinearGradient(0,0,0,200);
        gradR.addColorStop(0, 'rgba(192,0,0,.25)');
        gradR.addColorStop(1, 'rgba(192,0,0,.02)');

        new Chart(ctxR, {
            type: 'bar',
            data: {
                labels: {!! json_encode($reportPerMese->pluck('mese')) !!},
                datasets: [{
                    label: 'Report emessi',
                    data: {!! json_encode($reportPerMese->pluck('count')) !!},
                    backgroundColor: gradR,
                    borderColor: RED,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { grid: { color: '#f5f5f5' }, border: { display: false }, ticks: { precision: 0 } }
                }
            }
        });

        // ── Donut options comune ──────────────────────────────────────
        const donutOpts = (data, labels, colors) => ({
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors, borderWidth: 0, hoverOffset: 4 }]
            },
            options: {
                cutout: '74%',
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed } }
                }
            }
        });

        // ── Commesse donut ────────────────────────────────────────────
        new Chart(document.getElementById('chartCommesse'),
            donutOpts(
                [{{ $commesseAperte }}, {{ $commesseInLavoraz }}, {{ $commesseChiuse }}],
                ['Aperte', 'In lavorazione', 'Chiuse'],
                [GREEN, AMBER, GRAY]
            )
        );

        // ── Offerte donut ─────────────────────────────────────────────
        new Chart(document.getElementById('chartOfferte'),
            donutOpts(
                [{{ $offerteBozza }}, {{ $offerteInviate }}, {{ $offerteAccettate }}, {{ $offerteRifiutate }}],
                ['Bozza', 'Inviate', 'Accettate', 'Rifiutate'],
                [GRAY, BLUE, GREEN, RED]
            )
        );
    </script>
@endsection
