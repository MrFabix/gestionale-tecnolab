@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap');

        .cal-root { font-family: 'DM Sans', sans-serif; }

        /* ── Header ── */
        .cal-header {
            background: linear-gradient(135deg, #0d0d0d 0%, #1a3a2a 100%);
            border-radius: 18px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .cal-header::before {
            content:''; position:absolute; inset:0;
            background: radial-gradient(ellipse 60% 100% at 100% -10%, rgba(16,185,129,.35) 0%, transparent 60%);
            pointer-events:none;
        }
        .cal-header h1 { font-size:1.75rem; font-weight:700; margin:0; letter-spacing:-.5px; }
        .cal-header p  { margin:.2rem 0 0; opacity:.55; font-size:.88rem; }
        .cal-header-stats { display:flex; gap:1.5rem; margin-top:1rem; }
        .cal-stat { font-size:.78rem; opacity:.8; }
        .cal-stat strong { font-size:1.1rem; font-weight:700; opacity:1; display:block; }

        /* ── Layout ── */
        .cal-layout { display:grid; grid-template-columns:1fr 310px; gap:1.25rem; align-items:start; }
        @media(max-width:900px){ .cal-layout{ grid-template-columns:1fr; } }

        /* ── Calendario card ── */
        .cal-card {
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:16px;
            padding:1.25rem;
        }

        /* ── FullCalendar overrides ── */
        .fc .fc-toolbar-title { font-size:1.05rem!important; font-weight:700!important; font-family:'DM Sans',sans-serif!important; }
        .fc .fc-button {
            font-family:'DM Sans',sans-serif!important;
            font-size:.78rem!important;
            font-weight:600!important;
            border-radius:8px!important;
            padding:.28rem .7rem!important;
            box-shadow:none!important;
        }
        .fc .fc-button-primary { background:#111!important; border-color:#111!important; }
        .fc .fc-button-primary:hover { background:#333!important; border-color:#333!important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background:#10b981!important; border-color:#10b981!important; }
        .fc .fc-daygrid-day-number { font-size:.78rem; font-weight:600; color:#374151; }
        .fc .fc-day-today { background:rgba(16,185,129,.07)!important; }
        .fc .fc-day-today .fc-daygrid-day-number {
            color:#059669; background:#dcfce7; border-radius:50%;
            width:22px; height:22px; display:flex; align-items:center; justify-content:center;
        }
        .fc-event { border-radius:5px!important; font-size:.73rem!important; font-weight:600!important; border:none!important; padding:2px 5px!important; cursor:pointer; }
        .fc .fc-col-header-cell-cushion { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; text-decoration:none; }
        .fc .fc-daygrid-day-frame { min-height:72px; }
        .fc-list-event-dot { border-radius:50%!important; }

        /* ── Sidebar ── */
        .cal-sidebar { display:flex; flex-direction:column; gap:1rem; }

        .btn-new-event {
            background:#10b981; color:#fff; border:none;
            border-radius:11px; padding:.65rem 1.25rem;
            font-size:.84rem; font-weight:700;
            display:flex; align-items:center; gap:.45rem;
            cursor:pointer; transition:all .15s; width:100%; justify-content:center;
            box-shadow:0 2px 8px rgba(16,185,129,.3);
        }
        .btn-new-event:hover { background:#059669; box-shadow:0 4px 12px rgba(16,185,129,.4); transform:translateY(-1px); }

        /* Legenda */
        .legend-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:1rem 1.25rem; }
        .legend-card h6 { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; margin-bottom:.75rem; }
        .legend-item { display:flex; align-items:center; gap:.6rem; font-size:.81rem; color:#374151; margin-bottom:.4rem; }
        .legend-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }

        /* Agenda */
        .agenda-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; }
        .agenda-head { padding:.8rem 1.2rem; border-bottom:1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
        .agenda-head h6 { margin:0; font-size:.86rem; font-weight:700; }
        .agenda-item {
            display:flex; align-items:flex-start; gap:.7rem;
            padding:.6rem 1.2rem; border-bottom:1px solid #f9fafb;
            font-size:.8rem; transition:background .1s;
        }
        .agenda-item:hover { background:#f9fafb; }
        .agenda-item:last-child { border-bottom:none; }
        .agenda-date { font-size:.7rem; color:#9ca3af; min-width:56px; margin-top:2px; line-height:1.3; }
        .agenda-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; margin-top:4px; }
        .agenda-title { flex:1; color:#374151; line-height:1.3; }

        /* ── Popup dettaglio (fixed centrato sul mobile, popover su desktop) ── */
        .ev-popup-backdrop {
            display:none; position:fixed; inset:0; z-index:1040;
        }
        .ev-popup-backdrop.open { display:block; }
        .ev-popup {
            position:fixed; z-index:1041;
            background:#fff; border:1px solid #e5e7eb;
            border-radius:14px; box-shadow:0 12px 40px rgba(0,0,0,.15);
            padding:0; min-width:250px; max-width:300px;
            overflow:hidden;
            transition:opacity .15s, transform .15s;
        }
        .ev-popup-head {
            padding:.75rem 1rem;
            display:flex; align-items:center; gap:.5rem;
            border-bottom:1px solid #f3f4f6;
        }
        .ev-popup-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
        .ev-popup-tipo { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
        .ev-popup-close {
            margin-left:auto; background:none; border:none; width:22px; height:22px;
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            color:#9ca3af; cursor:pointer; font-size:.85rem;
            transition:background .1s;
        }
        .ev-popup-close:hover { background:#f3f4f6; color:#374151; }
        .ev-popup-body { padding:.75rem 1rem; }
        .ev-popup-title { font-size:.92rem; font-weight:700; margin-bottom:.3rem; color:#111; }
        .ev-popup-desc { font-size:.8rem; color:#6b7280; margin-bottom:.65rem; line-height:1.4; }
        .ev-popup-foot { padding:.6rem 1rem; border-top:1px solid #f3f4f6; display:flex; gap:.4rem; }

        /* ── Modale nuovo/modifica ── */
        .ev-modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,.5); z-index:1050;
            align-items:center; justify-content:center;
            backdrop-filter:blur(3px);
        }
        .ev-modal-overlay.open { display:flex; }
        .ev-modal {
            background:#fff; border-radius:20px;
            width:100%; max-width:500px; margin:1rem;
            box-shadow:0 32px 80px rgba(0,0,0,.2);
            overflow:hidden; animation:slideUp .22s ease;
        }
        @keyframes slideUp { from{transform:translateY(16px);opacity:0} to{transform:none;opacity:1} }
        .ev-modal-head {
            padding:1.25rem 1.5rem;
            background:linear-gradient(135deg,#0d0d0d,#1a3a2a);
            color:#fff;
            display:flex; justify-content:space-between; align-items:center;
        }
        .ev-modal-head h5 { margin:0; font-size:1rem; font-weight:700; }
        .ev-modal-head .btn-close { filter:invert(1); opacity:.7; }
        .ev-modal-head .btn-close:hover { opacity:1; }
        .ev-modal-body { padding:1.35rem 1.5rem; }
        .ev-modal-foot { padding:.9rem 1.5rem; border-top:1px solid #f0f0f0; display:flex; gap:.5rem; justify-content:flex-end; background:#fafafa; }

        .ev-modal-body .form-label { font-size:.79rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
        .ev-modal-body .form-control { font-size:.86rem; border-radius:9px; border-color:#e5e7eb; }
        .ev-modal-body .form-control:focus { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.15); }

        /* Color picker */
        .color-picker { display:flex; gap:.5rem; flex-wrap:wrap; padding:.1rem 0; }
        .color-swatch {
            width:26px; height:26px; border-radius:50%; cursor:pointer;
            border:3px solid transparent; transition:transform .12s, border-color .12s;
        }
        .color-swatch:hover { transform:scale(1.15); }
        .color-swatch.selected { border-color:#374151; transform:scale(1.1); }

        /* Toast */
        .ev-toast-wrap {
            position:fixed; bottom:1.5rem; right:1.5rem; z-index:2000;
            display:flex; flex-direction:column; gap:.5rem; pointer-events:none;
        }
        .ev-toast {
            background:#111; color:#fff; border-radius:10px;
            padding:.6rem 1rem; font-size:.83rem; font-weight:500;
            display:flex; align-items:center; gap:.5rem;
            pointer-events:all;
            animation:toastIn .2s ease;
            box-shadow:0 4px 20px rgba(0,0,0,.25);
        }
        .ev-toast.success { background:#059669; }
        .ev-toast.error   { background:#dc2626; }
        @keyframes toastIn { from{transform:translateX(20px);opacity:0} to{transform:none;opacity:1} }

        /* Delete confirm overlay */
        .del-confirm-wrap {
            position:fixed; inset:0; background:rgba(0,0,0,.5);
            z-index:1060; display:none;
            align-items:center; justify-content:center;
            backdrop-filter:blur(3px);
        }
        .del-confirm-wrap.open { display:flex; }
        .del-confirm-box {
            background:#fff; border-radius:16px; padding:1.5rem;
            max-width:360px; width:calc(100% - 2rem);
            box-shadow:0 24px 60px rgba(0,0,0,.2);
            animation:slideUp .2s ease;
            text-align:center;
        }
        .del-confirm-box .del-icon { font-size:2rem; margin-bottom:.75rem; }
        .del-confirm-box h6 { font-size:1rem; font-weight:700; margin-bottom:.4rem; }
        .del-confirm-box p { font-size:.85rem; color:#6b7280; margin-bottom:1.25rem; }
        .del-confirm-box .del-actions { display:flex; gap:.5rem; justify-content:center; }
    </style>

    <div class="cal-root">

        {{-- Header --}}
        <div class="cal-header">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h1><i class="bi bi-calendar3 me-2"></i>Calendario & Scadenze</h1>
                    <p>Scadenze automatiche dal sistema + eventi manuali</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="cal-layout">

            {{-- CALENDARIO --}}
            <div class="cal-card">
                <div id="calendar"></div>
            </div>

            {{-- SIDEBAR --}}
            <div class="cal-sidebar">

                <button class="btn-new-event" id="btnNuovoEvento">
                    <i class="bi bi-plus-lg"></i> Nuovo evento
                </button>

                {{-- Legenda --}}
                <div class="legend-card">
                    <h6>Legenda</h6>
                    @foreach([
                        ['#3b82f6', 'Evento manuale'],
                        ['#f59e0b', 'Taratura in scadenza'],
                        ['#8b5cf6', 'Manutenzione in scadenza'],
                        ['#0891b2', 'Formazione personale'],
                        ['#10b981', 'Validità offerta'],
                        ['#f97316', 'Fine commessa'],
                        ['#dc2626', 'Scaduto / urgente'],
                    ] as [$color, $label])
                        <div class="legend-item">
                            <span class="legend-dot" style="background:{{ $color }};"></span>
                            {{ $label }}
                        </div>
                    @endforeach
                </div>

                {{-- Prossime scadenze --}}
                <div class="agenda-card">
                    <div class="agenda-head">
                        <h6><i class="bi bi-list-ul me-1 text-muted"></i>Prossime scadenze</h6>
                        <span class="badge bg-light text-muted" id="agendaCount" style="font-size:.7rem;">—</span>
                    </div>
                    <div id="agendaList">
                        <div style="padding:1.25rem;color:#9ca3af;font-size:.82rem;text-align:center;">
                            <i class="bi bi-hourglass-split me-1"></i> Caricamento...
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- POPUP dettaglio evento --}}
        <div id="evPopupBackdrop" class="ev-popup-backdrop"></div>
        <div class="ev-popup" id="evPopup" style="display:none;">
            <div class="ev-popup-head">
                <span class="ev-popup-dot" id="evPopupDot"></span>
                <span class="ev-popup-tipo" id="evPopupTipo"></span>
                <button class="ev-popup-close" id="evPopupClose"><i class="bi bi-x"></i></button>
            </div>
            <div class="ev-popup-body">
                <div class="ev-popup-title" id="evPopupTitle"></div>
                <div class="ev-popup-desc" id="evPopupDesc"></div>
            </div>
            <div class="ev-popup-foot" id="evPopupFoot"></div>
        </div>

        {{-- MODALE nuovo/modifica evento --}}
        <div class="ev-modal-overlay" id="evModalOverlay">
            <div class="ev-modal">
                <div class="ev-modal-head">
                    <h5 id="evModalTitle">Nuovo evento</h5>
                    <button class="btn-close" id="evModalClose"></button>
                </div>
                <div class="ev-modal-body">
                    <form id="evForm" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Titolo *</label>
                            <input type="text" id="evTitolo" class="form-control" required placeholder="Nome evento o appuntamento">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Inizio *</label>
                                <input type="datetime-local" id="evInizio" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Fine</label>
                                <input type="datetime-local" id="evFine" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrizione</label>
                            <textarea id="evDescrizione" class="form-control" rows="2" placeholder="Note opzionali..."></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Colore</label>
                            <div class="color-picker" id="colorPicker">
                                @foreach(['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#f97316','#0891b2','#6b7280','#ec4899','#14b8a6'] as $c)
                                    <span class="color-swatch {{ $c==='#3b82f6'?'selected':'' }}"
                                          style="background:{{ $c }};" data-color="{{ $c }}"></span>
                                @endforeach
                            </div>
                            <input type="hidden" id="evColore" value="#3b82f6">
                        </div>
                        @if(Auth::user()->ruolo === 'admin')
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="evGlobale">
                                <label class="form-check-label" style="font-size:.82rem;" for="evGlobale">
                                    <i class="bi bi-globe2 me-1"></i>Visibile a tutti gli utenti
                                </label>
                            </div>
                        @endif
                    </form>
                </div>
                <div class="ev-modal-foot">
                    <button class="btn btn-outline-secondary btn-sm" id="evModalCancel">Annulla</button>
                    <button class="btn btn-outline-danger btn-sm d-none" id="evModalDelete">
                        <i class="bi bi-trash me-1"></i>Elimina
                    </button>
                    <button class="btn btn-dark btn-sm" id="evModalSave">
                        <i class="bi bi-check-lg me-1"></i>Salva
                    </button>
                </div>
            </div>
        </div>

        {{-- Conferma eliminazione --}}
        <div class="del-confirm-wrap" id="delConfirmWrap">
            <div class="del-confirm-box">
                <div class="del-icon">🗑️</div>
                <h6>Eliminare questo evento?</h6>
                <p id="delConfirmName">L'azione non può essere annullata.</p>
                <div class="del-actions">
                    <button class="btn btn-outline-secondary btn-sm px-3" id="delConfirmNo">Annulla</button>
                    <button class="btn btn-danger btn-sm px-3" id="delConfirmYes">
                        <i class="bi bi-trash me-1"></i>Elimina
                    </button>
                </div>
            </div>
        </div>

        {{-- Toast container --}}
        <div class="ev-toast-wrap" id="toastWrap"></div>

    </div>{{-- /cal-root --}}
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/it.global.min.js"></script>

    <script>
        const FEED_URL  = '{{ route("eventi.feed") }}';
        const STORE_URL = '{{ route("eventi.store") }}';
        const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const IS_ADMIN  = {{ Auth::user()->ruolo === 'admin' ? 'true' : 'false' }};

        let calendar;
        let currentEditId   = null;
        let currentEditName = '';

        // ── Toast ────────────────────────────────────────────────
        function showToast(msg, type = 'success') {
            const wrap = document.getElementById('toastWrap');
            const t = document.createElement('div');
            t.className = `ev-toast ${type}`;
            const icon = type === 'success' ? '✓' : '✕';
            t.innerHTML = `<span>${icon}</span> ${msg}`;
            wrap.appendChild(t);
            setTimeout(() => t.remove(), 3200);
        }

        // ── FullCalendar ─────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                locale:      'it',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek,listMonth',
                },
                buttonText: { today:'Oggi', month:'Mese', week:'Settimana', list:'Lista' },
                events:    FEED_URL,
                editable:  false,
                height:    'auto',
                eventClick: handleEventClick,
                dateClick:  handleDateClick,
                eventsSet:  buildAgenda,
            });
            calendar.render();
            setupModal();
            setupColorPicker();
            setupDeleteConfirm();
        });

        // ── Click su evento ──────────────────────────────────────
        function handleEventClick(info) {
            const ev    = info.event;
            const props = ev.extendedProps;
            const popup = document.getElementById('evPopup');

            // Colore dot e tipo
            document.getElementById('evPopupDot').style.background  = ev.backgroundColor;
            document.getElementById('evPopupTipo').textContent = tipoLabel(props.tipo);
            document.getElementById('evPopupTipo').style.color = ev.backgroundColor;
            document.getElementById('evPopupTitle').textContent = ev.title;
            document.getElementById('evPopupDesc').textContent  = props.descrizione || '';

            // Azioni footer
            const foot = document.getElementById('evPopupFoot');
            foot.innerHTML = '';

            if (props.link) {
                foot.innerHTML += `<a href="${props.link}" class="btn btn-sm btn-outline-secondary" style="font-size:.76rem;">Apri <i class="bi bi-arrow-right"></i></a>`;
            }

            if (props.tipo === 'manuale' && props.db_id) {
                // Serializza i dati per il modale
                const inizio = ev.startStr ? ev.startStr.slice(0,16) : '';
                const fine   = ev.endStr   ? ev.endStr.slice(0,16)   : '';
                const desc   = (props.descrizione || '').replace(/'/g, '&#39;');
                const col    = ev.backgroundColor || '#3b82f6';

                foot.innerHTML += `
                    <button class="btn btn-sm btn-outline-primary ms-auto" style="font-size:.76rem;"
                        onclick="editEvento(${props.db_id},'${ev.title.replace(/'/g,"\\'")}','${inizio}','${fine}','${desc}','${col}')">
                        <i class="bi bi-pencil me-1"></i>Modifica
                    </button>
                    <button class="btn btn-sm btn-outline-danger" style="font-size:.76rem;"
                        onclick="confirmDelete(${props.db_id},'${ev.title.replace(/'/g,"\\'")}')">
                        <i class="bi bi-trash"></i>
                    </button>`;
            }

            // Posiziona popup vicino all'evento
            const rect = info.el.getBoundingClientRect();
            const popW = 300, popH = 200;
            let top  = rect.bottom + window.scrollY + 8;
            let left = rect.left + window.scrollX;

            if (left + popW > window.innerWidth - 16) left = window.innerWidth - popW - 16;
            if (left < 8) left = 8;
            if (rect.bottom + popH > window.innerHeight) top = rect.top + window.scrollY - popH - 8;

            popup.style.top  = top  + 'px';
            popup.style.left = left + 'px';
            popup.style.display = 'block';
            document.getElementById('evPopupBackdrop').classList.add('open');
        }

        function tipoLabel(tipo) {
            const map = {
                manuale:      '👤 Evento manuale',
                taratura:     '⚙ Taratura',
                manutenzione: '🔧 Manutenzione',
                formazione:   '🎓 Formazione',
                offerta:      '📄 Offerta',
                commessa:     '📋 Commessa',
            };
            return map[tipo] || tipo;
        }

        function closePopup() {
            document.getElementById('evPopup').style.display = 'none';
            document.getElementById('evPopupBackdrop').classList.remove('open');
        }

        document.getElementById('evPopupClose').addEventListener('click', closePopup);
        document.getElementById('evPopupBackdrop').addEventListener('click', closePopup);

        // ── Click su giorno vuoto ─────────────────────────────────
        function handleDateClick(info) {
            openModal();
            document.getElementById('evInizio').value = info.dateStr + 'T09:00';
        }

        // ── Agenda laterale ───────────────────────────────────────
        function buildAgenda(allEvents) {
            const oggi  = new Date(); oggi.setHours(0,0,0,0);
            const tra60 = new Date(oggi); tra60.setDate(tra60.getDate() + 60);

            const prossimi = allEvents
                .filter(ev => { const s = new Date(ev.start); return s >= oggi && s <= tra60; })
                .sort((a,b) => new Date(a.start) - new Date(b.start))
                .slice(0, 25);

            document.getElementById('agendaCount').textContent = prossimi.length + ' / 60gg';

            const list = document.getElementById('agendaList');
            if (!prossimi.length) {
                list.innerHTML = '<div style="padding:1.25rem;color:#9ca3af;font-size:.82rem;text-align:center;">Nessuna scadenza nei prossimi 60 giorni 🎉</div>';
                return;
            }
            list.innerHTML = prossimi.map(ev => {
                const d = new Date(ev.start);
                const diff = Math.round((d - oggi) / 86400000);
                const label = diff === 0 ? '<strong>Oggi</strong>' : diff === 1 ? 'Domani' : d.toLocaleDateString('it-IT',{day:'2-digit',month:'short'});
                const urgent = diff <= 3 ? 'color:#dc2626;font-weight:600;' : diff <= 7 ? 'font-weight:600;' : '';
                return `
                <div class="agenda-item">
                    <span class="agenda-dot" style="background:${ev.backgroundColor};margin-top:5px;"></span>
                    <span class="agenda-date" style="${urgent}">${label}</span>
                    <span class="agenda-title" style="${urgent}">${ev.title}</span>
                </div>`;
            }).join('');
        }

        // ── Modale ───────────────────────────────────────────────
        function setupModal() {
            document.getElementById('btnNuovoEvento').addEventListener('click', () => openModal());
            document.getElementById('evModalClose').addEventListener('click', closeModal);
            document.getElementById('evModalCancel').addEventListener('click', closeModal);
            document.getElementById('evModalOverlay').addEventListener('click', e => {
                if (e.target === document.getElementById('evModalOverlay')) closeModal();
            });
            document.getElementById('evModalSave').addEventListener('click', saveEvento);
            document.getElementById('evModalDelete').addEventListener('click', () => {
                closeModal();
                confirmDelete(currentEditId, currentEditName);
            });
        }

        function openModal(id = null, titolo = '', inizio = '', fine = '', descrizione = '', colore = '#3b82f6') {
            currentEditId   = id;
            currentEditName = titolo;

            document.getElementById('evModalTitle').textContent = id ? 'Modifica evento' : 'Nuovo evento';
            document.getElementById('evModalDelete').classList.toggle('d-none', !id);

            document.getElementById('evTitolo').value      = titolo;
            document.getElementById('evInizio').value      = inizio;
            document.getElementById('evFine').value        = fine;
            document.getElementById('evDescrizione').value = descrizione;
            document.getElementById('evColore').value      = colore;

            document.querySelectorAll('.color-swatch').forEach(s =>
                s.classList.toggle('selected', s.dataset.color === colore)
            );
            if (document.getElementById('evGlobale')) document.getElementById('evGlobale').checked = false;

            document.getElementById('evModalOverlay').classList.add('open');
            setTimeout(() => document.getElementById('evTitolo').focus(), 50);
        }

        function editEvento(id, titolo, inizio, fine, descrizione, colore) {
            closePopup();
            openModal(id, titolo, inizio, fine, descrizione, colore);
        }
        window.editEvento = editEvento;

        function closeModal() {
            document.getElementById('evModalOverlay').classList.remove('open');
            currentEditId   = null;
            currentEditName = '';
        }

        async function saveEvento() {
            const titolo = document.getElementById('evTitolo').value.trim();
            const inizio = document.getElementById('evInizio').value;
            if (!titolo) { document.getElementById('evTitolo').focus(); showToast('Inserisci un titolo.', 'error'); return; }
            if (!inizio) { document.getElementById('evInizio').focus(); showToast('Inserisci la data di inizio.', 'error'); return; }

            const btn = document.getElementById('evModalSave');
            btn.disabled = true;

            const body = {
                _token:      CSRF,
                titolo,
                inizio,
                fine:        document.getElementById('evFine').value || null,
                descrizione: document.getElementById('evDescrizione').value,
                colore:      document.getElementById('evColore').value,
                globale:     document.getElementById('evGlobale')?.checked ? 1 : 0,
            };

            let url = STORE_URL, method = 'POST';
            if (currentEditId) { url = `/eventi/${currentEditId}`; body._method = 'PUT'; }

            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
                    body: JSON.stringify(body),
                });
                if (res.ok) {
                    closeModal();
                    calendar.refetchEvents();
                    showToast(currentEditId ? 'Evento aggiornato.' : 'Evento creato.');
                } else {
                    const err = await res.json().catch(() => ({}));
                    const msg = err.message || Object.values(err.errors || {})[0]?.[0] || 'Errore nel salvataggio.';
                    showToast(msg, 'error');
                }
            } catch {
                showToast('Errore di rete.', 'error');
            } finally {
                btn.disabled = false;
            }
        }

        // ── Conferma eliminazione ─────────────────────────────────
        let pendingDeleteId = null;

        function confirmDelete(id, nome) {
            pendingDeleteId = id;
            document.getElementById('delConfirmName').textContent = `"${nome}" verrà eliminato definitivamente.`;
            document.getElementById('delConfirmWrap').classList.add('open');
        }
        window.confirmDelete = confirmDelete;

        function setupDeleteConfirm() {
            document.getElementById('delConfirmNo').addEventListener('click', () => {
                document.getElementById('delConfirmWrap').classList.remove('open');
                pendingDeleteId = null;
            });
            document.getElementById('delConfirmYes').addEventListener('click', async () => {
                if (!pendingDeleteId) return;
                const btn = document.getElementById('delConfirmYes');
                btn.disabled = true;

                try {
                    const res = await fetch(`/eventi/${pendingDeleteId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    });
                    document.getElementById('delConfirmWrap').classList.remove('open');
                    if (res.ok) {
                        calendar.refetchEvents();
                        showToast('Evento eliminato.');
                    } else {
                        showToast('Impossibile eliminare l\'evento.', 'error');
                    }
                } catch {
                    showToast('Errore di rete.', 'error');
                } finally {
                    btn.disabled = false;
                    pendingDeleteId = null;
                }
            });
        }

        // ── Color picker ──────────────────────────────────────────
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
