@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

        .cal-root { font-family: 'DM Sans', sans-serif; }

        /* ── Header ── */
        .cal-header {
            background: linear-gradient(135deg, #0d0d0d, #1a3a2a 200%);
            border-radius: 18px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .cal-header::before {
            content:'';position:absolute;inset:0;
            background: radial-gradient(ellipse 50% 80% at 95% -10%, rgba(16,185,129,.3) 0%, transparent 55%);
            pointer-events:none;
        }
        .cal-header h1 { font-size:1.8rem;font-weight:700;margin:0;letter-spacing:-.5px; }
        .cal-header p  { margin:.2rem 0 0;opacity:.6;font-size:.9rem; }

        /* ── Layout ── */
        .cal-layout { display:grid; grid-template-columns:1fr 320px; gap:1.25rem; align-items:start; }
        @media(max-width:900px){ .cal-layout{ grid-template-columns:1fr; } }

        /* ── Calendario FullCalendar ── */
        .cal-card {
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:16px;
            padding:1.25rem;
            min-height:600px;
        }

        /* Override FullCalendar */
        .fc .fc-toolbar-title { font-size:1.1rem!important;font-weight:700!important;font-family:'DM Sans',sans-serif!important; }
        .fc .fc-button { font-family:'DM Sans',sans-serif!important;font-size:.8rem!important;font-weight:600!important;border-radius:8px!important;padding:.3rem .75rem!important; }
        .fc .fc-button-primary { background:#0d0d0d!important;border-color:#0d0d0d!important; }
        .fc .fc-button-primary:hover { background:#333!important;border-color:#333!important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background:#10b981!important;border-color:#10b981!important; }
        .fc .fc-daygrid-day-number { font-size:.8rem;font-weight:600;color:#374151; }
        .fc .fc-day-today { background:rgba(16,185,129,.06)!important; }
        .fc .fc-day-today .fc-daygrid-day-number { color:#059669;background:#dcfce7;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center; }
        .fc-event { border-radius:6px!important;font-size:.75rem!important;font-weight:600!important;border:none!important;padding:2px 6px!important;cursor:pointer; }
        .fc .fc-col-header-cell-cushion { font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af; }
        .fc .fc-daygrid-dot-event .fc-event-title { font-size:.75rem; }

        /* ── Sidebar ── */
        .cal-sidebar { display:flex;flex-direction:column;gap:1rem; }

        /* Legenda */
        .legend-card {
            background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:1rem 1.25rem;
        }
        .legend-card h6 { font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:.75rem; }
        .legend-item { display:flex;align-items:center;gap:.6rem;font-size:.82rem;margin-bottom:.45rem; }
        .legend-dot { width:10px;height:10px;border-radius:50%;flex-shrink:0; }

        /* Lista prossime scadenze */
        .agenda-card {
            background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;
        }
        .agenda-head { padding:.85rem 1.25rem;border-bottom:1px solid #f5f5f5;display:flex;justify-content:space-between;align-items:center; }
        .agenda-head h6 { margin:0;font-size:.88rem;font-weight:700; }
        .agenda-item {
            display:flex;align-items:flex-start;gap:.75rem;
            padding:.65rem 1.25rem;border-bottom:1px solid #f9f9f9;
            font-size:.82rem;transition:background .1s;cursor:default;
        }
        .agenda-item:hover { background:#fafafa; }
        .agenda-item:last-child { border-bottom:none; }
        .agenda-date { font-family:'DM Mono',monospace;font-size:.72rem;color:#9ca3af;min-width:60px;margin-top:2px; }
        .agenda-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px; }

        /* Pulsante nuovo evento */
        .btn-new-event {
            background:#10b981;color:#fff;border:none;
            border-radius:10px;padding:.6rem 1.25rem;
            font-size:.85rem;font-weight:700;
            display:flex;align-items:center;gap:.4rem;
            cursor:pointer;transition:background .15s;width:100%;justify-content:center;
        }
        .btn-new-event:hover { background:#059669; }

        /* ── Modale evento ── */
        .ev-modal-overlay {
            display:none;position:fixed;inset:0;
            background:rgba(0,0,0,.45);z-index:1050;
            align-items:center;justify-content:center;
        }
        .ev-modal-overlay.open { display:flex; }
        .ev-modal {
            background:#fff;border-radius:18px;
            width:100%;max-width:480px;
            box-shadow:0 24px 60px rgba(0,0,0,.2);
            overflow:hidden;animation:slideUp .25s ease;
        }
        @keyframes slideUp { from{transform:translateY(20px);opacity:0} to{transform:none;opacity:1} }
        .ev-modal-head { padding:1.25rem 1.5rem;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center; }
        .ev-modal-head h5 { margin:0;font-size:1rem;font-weight:700; }
        .ev-modal-body { padding:1.25rem 1.5rem; }
        .ev-modal-foot { padding:.85rem 1.5rem;border-top:1px solid #f0f0f0;display:flex;gap:.5rem;justify-content:flex-end; }

        /* Colori pallino selezione */
        .color-picker { display:flex;gap:.5rem;flex-wrap:wrap; }
        .color-swatch {
            width:24px;height:24px;border-radius:50%;cursor:pointer;
            border:2px solid transparent;transition:border-color .15s;
        }
        .color-swatch.selected { border-color:#374151; }

        /* Popup dettaglio evento (click) */
        .ev-popup {
            position:absolute;z-index:1000;
            background:#fff;border:1px solid #e5e7eb;
            border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.12);
            padding:1rem 1.25rem;min-width:240px;max-width:300px;
            display:none;
        }
        .ev-popup h6 { font-size:.9rem;font-weight:700;margin-bottom:.5rem; }
        .ev-popup .ev-tipo { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem; }
        .ev-popup .ev-desc { font-size:.82rem;color:#6b7280;margin-bottom:.75rem; }
        .ev-popup-close { position:absolute;top:.6rem;right:.75rem;background:none;border:none;font-size:1rem;color:#9ca3af;cursor:pointer; }
    </style>

    <div class="cal-root">

        {{-- Header --}}
        <div class="cal-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-calendar3 me-2"></i>Calendario & Scadenze</h1>
                    <p>Scadenze automatiche dal sistema + eventi manuali</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="cal-layout">

            {{-- ── CALENDARIO ── --}}
            <div class="cal-card">
                <div id="calendar"></div>
            </div>

            {{-- ── SIDEBAR ── --}}
            <div class="cal-sidebar">

                {{-- Nuovo evento --}}
                <button class="btn-new-event" id="btnNuovoEvento">
                    <i class="bi bi-plus-lg"></i> Nuovo evento
                </button>

                {{-- Legenda --}}
                <div class="legend-card">
                    <h6>Legenda</h6>
                    <div class="legend-item"><span class="legend-dot" style="background:#3b82f6;"></span> Evento manuale</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f59e0b;"></span> Taratura in scadenza</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6;"></span> Manutenzione in scadenza</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#0891b2;"></span> Formazione personale</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#10b981;"></span> Validità offerta</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f97316;"></span> Fine commessa</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#dc2626;"></span> Scaduto / urgente</div>
                </div>

                {{-- Prossime scadenze --}}
                <div class="agenda-card">
                    <div class="agenda-head">
                        <h6><i class="bi bi-list-ul me-1 text-muted"></i>Prossime scadenze</h6>
                        <span class="badge bg-light text-muted" id="agendaCount" style="font-size:.72rem;">—</span>
                    </div>
                    <div id="agendaList">
                        <div style="padding:1rem;color:#9ca3af;font-size:.82rem;text-align:center;">
                            <i class="bi bi-hourglass-split me-1"></i> Caricamento...
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── POPUP DETTAGLIO (posizionato via JS) ── --}}
        <div class="ev-popup" id="evPopup">
            <button class="ev-popup-close" id="evPopupClose">×</button>
            <div class="ev-tipo" id="evPopupTipo"></div>
            <h6 id="evPopupTitle"></h6>
            <div class="ev-desc" id="evPopupDesc"></div>
            <div id="evPopupActions" class="d-flex gap-2"></div>
        </div>

        {{-- ── MODALE NUOVO/MODIFICA EVENTO ── --}}
        <div class="ev-modal-overlay" id="evModalOverlay">
            <div class="ev-modal">
                <div class="ev-modal-head">
                    <h5 id="evModalTitle">Nuovo evento</h5>
                    <button class="btn-close" id="evModalClose"></button>
                </div>
                <div class="ev-modal-body">
                    <form id="evForm">
                        @csrf
                        <input type="hidden" id="evId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.82rem;">Titolo *</label>
                            <input type="text" id="evTitolo" class="form-control" required placeholder="Nome evento o appuntamento">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.82rem;">Inizio *</label>
                                <input type="datetime-local" id="evInizio" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:.82rem;">Fine</label>
                                <input type="datetime-local" id="evFine" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.82rem;">Descrizione</label>
                            <textarea id="evDescrizione" class="form-control" rows="2" placeholder="Note opzionali..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:.82rem;">Colore</label>
                            <div class="color-picker" id="colorPicker">
                                @foreach([
                                    '#3b82f6','#10b981','#f59e0b','#ef4444',
                                    '#8b5cf6','#f97316','#0891b2','#6b7280'
                                ] as $c)
                                    <span class="color-swatch {{ $c==='#3b82f6'?'selected':'' }}"
                                          style="background:{{ $c }};"
                                          data-color="{{ $c }}"></span>
                                @endforeach
                            </div>
                            <input type="hidden" id="evColore" value="#3b82f6">
                        </div>
                        @if(Auth::user()->ruolo === 'admin')
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="evGlobale">
                                <label class="form-check-label" style="font-size:.82rem;" for="evGlobale">
                                    Visibile a tutti gli utenti
                                </label>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="ev-modal-foot">
                    <button class="btn btn-outline-secondary btn-sm" id="evModalCancel">Annulla</button>
                    <button class="btn btn-danger btn-sm d-none" id="evModalDelete">
                        <i class="bi bi-trash"></i> Elimina
                    </button>
                    <button class="btn btn-dark btn-sm" id="evModalSave">
                        <i class="bi bi-save me-1"></i> Salva
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /cal-root --}}

@endsection

@section('scripts')
    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/it.global.min.js"></script>

            <script>
                const FEED_URL   = '{{ route("eventi.feed") }}';
                const STORE_URL  = '{{ route("eventi.store") }}';
                const CSRF       = '{{ csrf_token() }}';
                const IS_ADMIN   = {{ Auth::user()->ruolo === 'admin' ? 'true' : 'false' }};

                // ── FullCalendar ─────────────────────────────────────────────
                let calendar;

                document.addEventListener('DOMContentLoaded', () => {
                    const el = document.getElementById('calendar');

                    calendar = new FullCalendar.Calendar(el, {
                        locale:           'it',
                        initialView:      'dayGridMonth',
                        headerToolbar: {
                            left:   'prev,next today',
                            center: 'title',
                            right:  'dayGridMonth,timeGridWeek,listMonth',
                        },
                        buttonText: { today:'Oggi', month:'Mese', week:'Settimana', list:'Lista' },
                        events:     FEED_URL,
                        editable:   false,
                        height:     'auto',
                        eventClick: handleEventClick,
                        dateClick:  handleDateClick,
                        eventDidMount(info) {
                            // Tooltip Bootstrap
                            info.el.setAttribute('title', info.event.title);
                        },
                        // Dopo ogni fetch aggiorna l'agenda laterale
                        eventsSet(events) { buildAgenda(events); },
                    });

                    calendar.render();
                    setupModal();
                    setupColorPicker();
                });

                // ── Click su evento ──────────────────────────────────────────
                function handleEventClick(info) {
                    const ev   = info.event;
                    const props = ev.extendedProps;
                    const popup = document.getElementById('evPopup');

                    document.getElementById('evPopupTitle').textContent = ev.title;
                    document.getElementById('evPopupDesc').textContent  = props.descrizione || '';

                    const tipoMap = {
                        manuale:     '👤 Evento manuale',
                        taratura:    '⚙ Taratura attrezzatura',
                        manutenzione:'🔧 Manutenzione',
                        formazione:  '🎓 Formazione personale',
                        offerta:     '📄 Scadenza offerta',
                        commessa:    '📋 Fine commessa',
                    };
                    document.getElementById('evPopupTipo').textContent = tipoMap[props.tipo] || props.tipo;
                    document.getElementById('evPopupTipo').style.color = ev.backgroundColor;

                    const actions = document.getElementById('evPopupActions');
                    actions.innerHTML = '';

                    if (props.link) {
                        actions.innerHTML += `<a href="${props.link}" class="btn btn-sm btn-outline-secondary" style="font-size:.76rem;">Apri →</a>`;
                    }

                    if (props.tipo === 'manuale' && props.db_id) {
                        actions.innerHTML += `<button class="btn btn-sm btn-outline-primary" style="font-size:.76rem;" onclick="editEvento(${props.db_id}, '${ev.title.replace(/'/g,"\\'")}')">Modifica</button>`;
                    }

                    // Posiziona popup vicino al click
                    const rect = info.el.getBoundingClientRect();
                    popup.style.top  = (rect.bottom + window.scrollY + 8) + 'px';
                    popup.style.left = Math.min(rect.left + window.scrollX, window.innerWidth - 320) + 'px';
                    popup.style.display = 'block';
                }

                document.getElementById('evPopupClose').addEventListener('click', () => {
                    document.getElementById('evPopup').style.display = 'none';
                });
                document.addEventListener('click', e => {
                    const popup = document.getElementById('evPopup');
                    if (!popup.contains(e.target) && !e.target.closest('.fc-event')) {
                        popup.style.display = 'none';
                    }
                });

                // ── Click su giorno vuoto → apri modale con data precompilata ─
                function handleDateClick(info) {
                    openModal();
                    const dt = info.dateStr + 'T09:00';
                    document.getElementById('evInizio').value = dt;
                }

                // ── Agenda laterale ───────────────────────────────────────────
                function buildAgenda(allEvents) {
                    const oggi = new Date();
                    oggi.setHours(0,0,0,0);
                    const tra60 = new Date(oggi); tra60.setDate(tra60.getDate() + 60);

                    const prossimi = allEvents
                        .filter(ev => {
                            const s = new Date(ev.start);
                            return s >= oggi && s <= tra60;
                        })
                        .sort((a,b) => new Date(a.start) - new Date(b.start))
                        .slice(0, 20);

                    const list = document.getElementById('agendaList');
                    document.getElementById('agendaCount').textContent = prossimi.length + ' nei prossimi 60gg';

                    if (!prossimi.length) {
                        list.innerHTML = '<div style="padding:1rem;color:#9ca3af;font-size:.82rem;text-align:center;">Nessuna scadenza nei prossimi 60 giorni 🎉</div>';
                        return;
                    }

                    list.innerHTML = prossimi.map(ev => {
                        const d = new Date(ev.start);
                        const diffDays = Math.round((d - oggi) / 86400000);
                        const label = diffDays === 0 ? 'Oggi' : diffDays === 1 ? 'Domani' : d.toLocaleDateString('it-IT', {day:'2-digit',month:'short'});
                        const urgency = diffDays <= 7 ? 'font-weight:600;' : '';
                        return `
        <div class="agenda-item">
            <span class="agenda-dot" style="background:${ev.backgroundColor};"></span>
            <span class="agenda-date" style="${urgency}">${label}</span>
            <span style="flex:1;${urgency}">${ev.title}</span>
        </div>`;
                    }).join('');
                }

                // ── Modale nuovo/modifica evento ──────────────────────────────
                let currentEditId = null;

                function setupModal() {
                    document.getElementById('btnNuovoEvento').addEventListener('click', openModal);
                    document.getElementById('evModalClose').addEventListener('click', closeModal);
                    document.getElementById('evModalCancel').addEventListener('click', closeModal);
                    document.getElementById('evModalOverlay').addEventListener('click', e => {
                        if (e.target === document.getElementById('evModalOverlay')) closeModal();
                    });

                    document.getElementById('evModalSave').addEventListener('click', saveEvento);
                    document.getElementById('evModalDelete').addEventListener('click', deleteEvento);
                }

                function openModal(id = null, titolo = '') {
                    currentEditId = id;
                    document.getElementById('evModalTitle').textContent = id ? 'Modifica evento' : 'Nuovo evento';
                    document.getElementById('evModalDelete').classList.toggle('d-none', !id);
                    document.getElementById('evTitolo').value = titolo;
                    document.getElementById('evDescrizione').value = '';
                    document.getElementById('evInizio').value = '';
                    document.getElementById('evFine').value = '';
                    document.getElementById('evColore').value = '#3b82f6';
                    document.querySelectorAll('.color-swatch').forEach(s => s.classList.toggle('selected', s.dataset.color === '#3b82f6'));
                    if (document.getElementById('evGlobale')) document.getElementById('evGlobale').checked = false;
                    document.getElementById('evModalOverlay').classList.add('open');
                    document.getElementById('evTitolo').focus();
                }

                function editEvento(id, titolo) {
                    document.getElementById('evPopup').style.display = 'none';
                    openModal(id, titolo);
                }
                window.editEvento = editEvento;

                function closeModal() {
                    document.getElementById('evModalOverlay').classList.remove('open');
                    currentEditId = null;
                }

                async function saveEvento() {
                    const titolo = document.getElementById('evTitolo').value.trim();
                    if (!titolo) { document.getElementById('evTitolo').focus(); return; }

                    const body = {
                        _token:      CSRF,
                        titolo,
                        inizio:      document.getElementById('evInizio').value,
                        fine:        document.getElementById('evFine').value || null,
                        descrizione: document.getElementById('evDescrizione').value,
                        colore:      document.getElementById('evColore').value,
                        globale:     document.getElementById('evGlobale')?.checked ? 1 : 0,
                    };

                    let url    = STORE_URL;
                    let method = 'POST';

                    if (currentEditId) {
                        url    = `/eventi/${currentEditId}`;
                        method = 'POST';
                        body._method = 'PUT';
                    }

                    const res = await fetch(url, {
                        method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: JSON.stringify(body),
                    });

                    if (res.ok) {
                        closeModal();
                        calendar.refetchEvents();
                    }
                }

                async function deleteEvento() {
                    if (!currentEditId || !confirm('Eliminare questo evento?')) return;
                    const res = await fetch(`/eventi/${currentEditId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    });
                    if (res.ok) { closeModal(); calendar.refetchEvents(); }
                }

                // ── Color picker ──────────────────────────────────────────────
                function setupColorPicker() {
                    document.querySelectorAll('.color-swatch').forEach(sw => {
                        sw.addEventListener('click', () => {
                            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('selected'));
                            sw.classList.add('selected');
                            document.getElementById('evColore').value = sw.dataset.color;
                        });
                    });
                }
            </script>
@endsection
