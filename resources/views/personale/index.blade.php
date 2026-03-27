@extends('layouts.app')

@section('content')
    <style>
        .pers-header {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        .pers-header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; letter-spacing: -.5px; }
        .pers-header p  { margin: 0; opacity: .65; font-size: .9rem; }

        .filter-bar {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 1rem 1.25rem; margin-bottom: 1rem;
            display: flex; gap: .75rem; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-bar label { font-size: .72rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; display:block; margin-bottom:.3rem; }
        .filter-bar .form-control,
        .filter-bar .form-select { border-radius: 8px; border-color: #e0e0e0; font-size: .875rem; }

        .pill-filters { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
        .pill { padding:.35rem .9rem; border-radius:999px; font-size:.8rem; font-weight:600; border:1.5px solid #d1d5db; background:#f9fafb; color:#374151; cursor:pointer; transition:all .15s; user-select:none; }
        .pill:hover, .pill.active { border-color:#2c5364; background:#2c5364; color:#fff; }
        .pill.warn-pill.active { background:#b91c1c; border-color:#b91c1c; }
        .pill .cnt { display:inline-flex; align-items:center; justify-content:center; background:rgba(0,0,0,.15); border-radius:999px; width:18px; height:18px; font-size:.68rem; margin-left:.3rem; }

        .pers-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
        .pers-table thead th { background:#f8fafc; border-bottom:2px solid #e5e7eb; padding:.75rem 1rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; white-space:nowrap; cursor:pointer; user-select:none; }
        .pers-table thead th:hover { color:#2c5364; }
        .pers-table thead th .sort-icon { opacity:.35; margin-left:4px; }
        .pers-table thead th.sorted .sort-icon { opacity:1; color:#2c5364; }
        .pers-table thead th.no-sort { cursor:default; }
        .pers-table thead th.no-sort:hover { color:#6b7280; }
        .pers-table tbody tr { border-bottom:1px solid #f0f0f0; transition:background .1s; }
        .pers-table tbody tr:hover { background:#f7fbff; }
        .pers-table td { padding:.65rem 1rem; vertical-align:middle; }

        /* Avatar */
        .avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb; }
        .avatar-ph { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#203a43,#2c5364); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:.9rem; border:2px solid #e5e7eb; }

        /* Badge stato */
        .badge-stato { padding:.28rem .65rem; border-radius:999px; font-size:.71rem; font-weight:700; white-space:nowrap; }
        .badge-attivo   { background:#dcfce7; color:#15803d; }
        .badge-in_ferie { background:#dbeafe; color:#1e40af; }
        .badge-dimesso  { background:#f3f4f6; color:#6b7280; }
        .badge-sospeso  { background:#fef9c3; color:#92400e; }

        /* Badge allerta */
        .badge-alert { padding:.25rem .6rem; border-radius:999px; font-size:.71rem; font-weight:600; white-space:nowrap; }
        .badge-alert-ok   { background:#dcfce7; color:#15803d; }
        .badge-alert-warn { background:#fef9c3; color:#92400e; }
        .badge-alert-scad { background:#fee2e2; color:#b91c1c; }
        .badge-alert-none { background:#f3f4f6; color:#9ca3af; }

        .badge-user { background:#ede9fe; color:#5b21b6; padding:.25rem .6rem; border-radius:999px; font-size:.71rem; font-weight:600; }

        .btn-action { width:30px; height:30px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; display:inline-flex; align-items:center; justify-content:center; color:#374151; font-size:.82rem; transition:all .15s; text-decoration:none; }
        .btn-action:hover      { background:#f3f4f6; color:#2c5364; border-color:#93c5fd; }
        .btn-action.del:hover  { background:#fee2e2; color:#b91c1c; border-color:#fca5a5; }

        .results-count { font-size:.8rem; color:#9ca3af; margin-bottom:.5rem; }
        .results-count strong { color:#374151; }
        .table-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }
        .table-wrapper { overflow-x:auto; }
        .empty-state { text-align:center; padding:4rem 2rem; color:#9ca3af; }
        .empty-state i { font-size:2.8rem; display:block; margin-bottom:1rem; }
    </style>

    @php
        $nScadute  = $personale->filter(fn($p) => $p->formazioniScadute()->count() > 0)->count();
        $nInScad   = $personale->filter(fn($p) => $p->formazioniScadute()->count() === 0 && $p->formazioniInScadenza(30)->count() > 0)->count();
    @endphp

    <div class="pers-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-people-fill me-2"></i>Personale</h1>
                <p>{{ $personale->count() }} dipendenti registrati</p>
            </div>
            <a href="{{ route('personale.create') }}" class="btn btn-light fw-semibold px-4" style="color:#2c5364;">
                <i class="bi bi-person-plus me-1"></i> Nuovo dipendente
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pills --}}
    <div class="pill-filters">
        <span class="pill active" data-filter="tutti">Tutti <span class="cnt">{{ $personale->count() }}</span></span>
        <span class="pill" data-filter="attivo">Attivi <span class="cnt">{{ $personale->where('stato','attivo')->count() }}</span></span>
        <span class="pill" data-filter="in_ferie">In ferie <span class="cnt">{{ $personale->where('stato','in_ferie')->count() }}</span></span>
        <span class="pill" data-filter="sospeso">Sospesi <span class="cnt">{{ $personale->where('stato','sospeso')->count() }}</span></span>
        <span class="pill" data-filter="dimesso">Dimessi <span class="cnt">{{ $personale->where('stato','dimesso')->count() }}</span></span>
        @if($nScadute > 0)
            <span class="pill warn-pill" data-filter="formaz_scaduta" style="border-color:#fca5a5;color:#b91c1c;">
            ⚠ Formazione scaduta <span class="cnt" style="background:rgba(185,28,28,.2);">{{ $nScadute }}</span>
        </span>
        @endif
        @if($nInScad > 0)
            <span class="pill warn-pill" data-filter="formaz_warn" style="border-color:#fcd34d;color:#92400e;">
            ⏰ In scadenza <span class="cnt" style="background:rgba(146,64,14,.15);">{{ $nInScad }}</span>
        </span>
        @endif
    </div>

    {{-- Barra ricerca --}}
    <div class="filter-bar">
        <div style="flex:1;min-width:220px;">
            <label>Cerca</label>
            <div class="input-group">
            <span class="input-group-text bg-white border-end-0 pe-1" style="border-radius:8px 0 0 8px;border-color:#e0e0e0;">
                <i class="bi bi-search text-muted" style="font-size:.82rem;"></i>
            </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-1"
                       placeholder="Nome, cognome, qualifica, reparto…"
                       style="border-radius:0 8px 8px 0;">
            </div>
        </div>
        <div style="min-width:160px;">
            <label>Qualifica</label>
            <select id="filterQualifica" class="form-select">
                <option value="">Tutte</option>
                @foreach($personale->pluck('qualifica')->filter()->unique()->sort() as $q)
                    <option value="{{ $q }}">{{ $q }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px;">
            <label>Reparto</label>
            <select id="filterReparto" class="form-select">
                <option value="">Tutti</option>
                @foreach($personale->pluck('reparto')->filter()->unique()->sort() as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>&nbsp;</label>
            <button class="btn btn-outline-secondary d-block" id="btnReset" style="border-radius:8px;font-size:.85rem;">
                <i class="bi bi-x-circle"></i> Reset
            </button>
        </div>
    </div>

    <p class="results-count"><strong id="visibleCount">{{ $personale->count() }}</strong> risultati</p>

    <div class="table-card">
        <div class="table-wrapper">
            <table class="pers-table">
                <thead>
                <tr>
                    <th class="no-sort" style="width:52px;"></th>
                    <th data-col="1">Cognome / Nome <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th data-col="2">Qualifica <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th data-col="3">Reparto <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th class="no-sort">Contatti</th>
                    <th class="no-sort">Stato</th>
                    <th class="no-sort">Formazione</th>
                    <th class="no-sort">Utente sistema</th>
                    <th class="no-sort" style="width:110px;"></th>
                </tr>
                </thead>
                <tbody id="persTbody">
                @forelse($personale as $p)
                    @php
                        $fScad = $p->formazioniScadute()->count();
                        $fWarn = $p->formazioniInScadenza(30)->count();
                        $thumb = $p->getFirstMediaUrl('foto', 'thumb');
                        $iniziali = strtoupper(mb_substr($p->cognome,0,1).mb_substr($p->nome,0,1));
                    @endphp
                    <tr data-stato="{{ $p->stato }}"
                        data-formaz-scad="{{ $fScad > 0 ? '1' : '0' }}"
                        data-formaz-warn="{{ ($fScad === 0 && $fWarn > 0) ? '1' : '0' }}"
                        data-search="{{ strtolower($p->nome.' '.$p->cognome.' '.($p->qualifica??'').' '.($p->reparto??'').' '.($p->email??'')) }}"
                        data-qualifica="{{ $p->qualifica }}"
                        data-reparto="{{ $p->reparto }}">

                        {{-- Avatar --}}
                        <td>
                            @if($thumb)
                                <img src="{{ $thumb }}" class="avatar" alt="">
                            @else
                                <div class="avatar-ph">{{ $iniziali }}</div>
                            @endif
                        </td>

                        {{-- Nome --}}
                        <td>
                            <a href="{{ route('personale.show', $p) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ $p->cognome }} {{ $p->nome }}
                            </a>
                            @if($p->data_assunzione)
                                <br><small class="text-muted" style="font-size:.74rem;">Dal {{ $p->data_assunzione->format('d/m/Y') }}</small>
                            @endif
                        </td>

                        {{-- Qualifica --}}
                        <td>{{ $p->qualifica ?? '—' }}</td>

                        {{-- Reparto --}}
                        <td>{{ $p->reparto ?? '—' }}</td>

                        {{-- Contatti --}}
                        <td>
                            @if($p->telefono)
                                <a href="tel:{{ $p->telefono }}" class="text-decoration-none text-dark d-block" style="font-size:.82rem;">
                                    <i class="bi bi-telephone text-muted me-1"></i>{{ $p->telefono }}
                                </a>
                            @endif
                            @if($p->email)
                                <a href="mailto:{{ $p->email }}" class="text-decoration-none text-muted d-block" style="font-size:.78rem;">
                                    <i class="bi bi-envelope me-1"></i>{{ $p->email }}
                                </a>
                            @endif
                            @if(!$p->telefono && !$p->email) <span class="text-muted">—</span> @endif
                        </td>

                        {{-- Stato --}}
                        <td>
                            @php $statoMap = ['attivo'=>'Attivo','in_ferie'=>'In ferie','dimesso'=>'Dimesso','sospeso'=>'Sospeso']; @endphp
                            <span class="badge-stato badge-{{ $p->stato }}">
                            {{ $statoMap[$p->stato] ?? $p->stato }}
                        </span>
                        </td>

                        {{-- Formazione --}}
                        <td>
                            @if($fScad > 0)
                                <span class="badge-alert badge-alert-scad">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $fScad }} scadut{{ $fScad === 1 ? 'a' : 'e' }}
                            </span>
                            @elseif($fWarn > 0)
                                <span class="badge-alert badge-alert-warn">
                                <i class="bi bi-clock me-1"></i>{{ $fWarn }} in scadenza
                            </span>
                            @elseif($p->formazioni->count() > 0)
                                <span class="badge-alert badge-alert-ok">
                                <i class="bi bi-check me-1"></i>{{ $p->formazioni->count() }} ok
                            </span>
                            @else
                                <span class="badge-alert badge-alert-none">Nessuna</span>
                            @endif
                        </td>

                        {{-- Utente sistema --}}
                        <td>
                            @if($p->user)
                                <span class="badge-user">
                                <i class="bi bi-person-check me-1"></i>{{ $p->user->username }}
                            </span>
                            @else
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            @endif
                        </td>

                        {{-- Azioni --}}
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('personale.show', $p) }}" class="btn-action" title="Dettaglio"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('personale.edit', $p) }}" class="btn-action" title="Modifica"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('personale.destroy', $p) }}" method="POST"
                                      onsubmit="return confirm('Eliminare {{ addslashes($p->cognome.' '.$p->nome) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action del" title="Elimina"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                Nessun dipendente registrato.<br>
                                <a href="{{ route('personale.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-person-plus"></i> Aggiungi il primo
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="noResults" style="display:none;">
            <div class="empty-state">
                <i class="bi bi-funnel"></i>
                Nessun risultato per i filtri selezionati.
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function(){
            const rows      = Array.from(document.querySelectorAll('#persTbody tr'));
            const search    = document.getElementById('searchInput');
            const filterQ   = document.getElementById('filterQualifica');
            const filterR   = document.getElementById('filterReparto');
            const btnReset  = document.getElementById('btnReset');
            const noResults = document.getElementById('noResults');
            const cntEl     = document.getElementById('visibleCount');
            let pill = 'tutti', sortCol = null, sortAsc = true;

            function filter() {
                const q = search.value.toLowerCase().trim();
                const fq = filterQ.value;
                const fr = filterR.value;
                let n = 0;
                rows.forEach(r => {
                    let show = true;
                    if      (pill === 'formaz_scaduta' && r.dataset.formazScad !== '1') show = false;
                    else if (pill === 'formaz_warn'    && r.dataset.formazWarn !== '1') show = false;
                    else if (!['tutti','formaz_scaduta','formaz_warn'].includes(pill) && r.dataset.stato !== pill) show = false;
                    if (q  && !r.dataset.search.includes(q))       show = false;
                    if (fq && r.dataset.qualifica !== fq)           show = false;
                    if (fr && r.dataset.reparto   !== fr)           show = false;
                    r.style.display = show ? '' : 'none';
                    if (show) n++;
                });
                cntEl.textContent = n;
                noResults.style.display = (n === 0 && rows.length > 0) ? 'block' : 'none';
            }

            document.querySelectorAll('.pill').forEach(p => {
                p.addEventListener('click', () => {
                    document.querySelectorAll('.pill').forEach(x => x.classList.remove('active'));
                    p.classList.add('active');
                    pill = p.dataset.filter;
                    filter();
                });
            });

            [search, filterQ, filterR].forEach(el => el.addEventListener('change' in el ? 'change' : 'input', filter));
            search.addEventListener('input', filter);

            btnReset.addEventListener('click', () => {
                search.value = ''; filterQ.value = ''; filterR.value = '';
                pill = 'tutti';
                document.querySelectorAll('.pill').forEach((p,i) => p.classList.toggle('active', i===0));
                filter();
            });

            document.querySelectorAll('.pers-table thead th[data-col]').forEach(th => {
                th.addEventListener('click', () => {
                    const col = parseInt(th.dataset.col);
                    if (sortCol === col) sortAsc = !sortAsc;
                    else { sortCol = col; sortAsc = true; }
                    document.querySelectorAll('.pers-table thead th').forEach(t => {
                        t.classList.remove('sorted');
                        const ic = t.querySelector('.sort-icon');
                        if (ic) ic.className = 'bi bi-chevron-expand sort-icon';
                    });
                    th.classList.add('sorted');
                    th.querySelector('.sort-icon').className = `bi bi-chevron-${sortAsc?'up':'down'} sort-icon`;
                    const tbody = document.getElementById('persTbody');
                    rows.slice().sort((a,b) => {
                        const at = a.cells[col]?.innerText.trim().toLowerCase()||'';
                        const bt = b.cells[col]?.innerText.trim().toLowerCase()||'';
                        return sortAsc ? at.localeCompare(bt,'it') : bt.localeCompare(at,'it');
                    }).forEach(r => tbody.appendChild(r));
                    filter();
                });
            });
        })();
    </script>
@endsection
