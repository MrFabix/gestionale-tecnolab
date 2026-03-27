@extends('layouts.app')

@section('content')
    <style>
        .att-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        .att-header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; letter-spacing: -.5px; }
        .att-header p  { margin: 0; opacity: .65; font-size: .9rem; }

        .filter-bar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-bar .form-control,
        .filter-bar .form-select {
            border-radius: 8px;
            border-color: #e0e0e0;
            font-size: .875rem;
        }
        .filter-bar label { font-size: .72rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; display:block; margin-bottom:.3rem; }

        .pill-filters { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
        .pill-filters .pill {
            padding: .35rem .9rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1.5px solid #d1d5db;
            background: #f9fafb;
            color: #374151;
            cursor: pointer;
            transition: all .15s;
            user-select: none;
        }
        .pill-filters .pill:hover,
        .pill-filters .pill.active { border-color: #d90429; background: #d90429; color: #fff; }
        .pill-filters .pill.warn-pill.active { background: #b91c1c; border-color: #b91c1c; }
        .pill-filters .pill .cnt {
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(0,0,0,.18);
            border-radius: 999px; width: 18px; height: 18px;
            font-size: .68rem; margin-left: .3rem;
        }

        .att-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .875rem; }
        .att-table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e5e7eb;
            padding: .75rem 1rem;
            font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            color: #6b7280; white-space: nowrap;
            cursor: pointer; user-select: none;
        }
        .att-table thead th:hover { color: #d90429; }
        .att-table thead th .sort-icon { opacity: .35; margin-left: 4px; }
        .att-table thead th.sorted .sort-icon { opacity: 1; color: #d90429; }
        .att-table thead th.no-sort { cursor: default; }
        .att-table thead th.no-sort:hover { color: #6b7280; }

        .att-table tbody tr { border-bottom: 1px solid #f0f0f0; transition: background .1s; }
        .att-table tbody tr:hover { background: #fafbff; }
        .att-table td { padding: .65rem 1rem; vertical-align: middle; }

        .att-thumb {
            width: 42px; height: 42px; border-radius: 8px;
            object-fit: cover; border: 1px solid #e5e7eb;
        }
        .att-thumb-ph {
            width: 42px; height: 42px; border-radius: 8px;
            background: #f3f4f6; display: flex; align-items: center; justify-content: center;
            color: #d1d5db; font-size: 1.1rem; border: 1px solid #e5e7eb;
        }

        .badge-stato { padding: .28rem .65rem; border-radius: 999px; font-size: .71rem; font-weight: 700; white-space: nowrap; }
        .badge-attivo       { background: #dcfce7; color: #15803d; }
        .badge-manutenzione { background: #fef9c3; color: #92400e; }
        .badge-dismesso     { background: #f3f4f6; color: #6b7280; }

        .badge-tar { padding: .25rem .6rem; border-radius: 999px; font-size: .71rem; font-weight: 600; white-space: nowrap; }
        .badge-tar-ok   { background: #dcfce7; color: #15803d; }
        .badge-tar-warn { background: #fef9c3; color: #92400e; }
        .badge-tar-scad { background: #fee2e2; color: #b91c1c; }
        .badge-tar-none { background: #f3f4f6; color: #9ca3af; }

        .btn-action {
            width: 30px; height: 30px; border-radius: 7px;
            border: 1px solid #e5e7eb; background: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            color: #374151; font-size: .82rem;
            transition: all .15s; text-decoration: none;
        }
        .btn-action:hover        { background: #f3f4f6; color: #d90429; border-color: #fca5a5; }
        .btn-action.del:hover    { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }

        .results-count { font-size: .8rem; color: #9ca3af; margin-bottom: .5rem; }
        .results-count strong { color: #374151; }

        .table-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; }
        .table-wrapper { overflow-x: auto; }

        .empty-state { text-align: center; padding: 4rem 2rem; color: #9ca3af; }
        .empty-state i { font-size: 2.8rem; display: block; margin-bottom: 1rem; }
    </style>

    {{-- Header --}}
    <div class="att-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-tools me-2"></i>Attrezzature</h1>
                <p>{{ $attrezzature->count() }} attrezzature registrate nel sistema</p>
            </div>
            <a href="{{ route('attrezzature.create') }}" class="btn btn-danger fw-semibold px-4">
                <i class="bi bi-plus-lg me-1"></i> Nuova
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pill filters rapidi --}}
    @php
        $nScadute = $attrezzature->filter(fn($a) =>
            $a->tarature->first() &&
            $a->tarature->first()->data_scadenza &&
            $a->tarature->first()->data_scadenza->isPast()
        )->count();
    @endphp
    <div class="pill-filters">
    <span class="pill active" data-filter="tutti">
        Tutti <span class="cnt">{{ $attrezzature->count() }}</span>
    </span>
        <span class="pill" data-filter="attivo">
        Attivi <span class="cnt">{{ $attrezzature->where('stato','attivo')->count() }}</span>
    </span>
        <span class="pill" data-filter="in_manutenzione">
        In manutenzione <span class="cnt">{{ $attrezzature->where('stato','in_manutenzione')->count() }}</span>
    </span>
        <span class="pill" data-filter="dismesso">
        Dismessi <span class="cnt">{{ $attrezzature->where('stato','dismesso')->count() }}</span>
    </span>
        @if($nScadute > 0)
            <span class="pill warn-pill" data-filter="taratura_scaduta" style="border-color:#fca5a5;color:#b91c1c;">
        ⚠ Taratura scaduta <span class="cnt" style="background:rgba(185,28,28,.2);">{{ $nScadute }}</span>
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
                       placeholder="Nome, codice, marca, modello, matricola…"
                       style="border-radius:0 8px 8px 0;">
            </div>
        </div>
        <div style="min-width:170px;">
            <label>Ubicazione</label>
            <select id="filterUbicazione" class="form-select">
                <option value="">Tutte</option>
                @foreach($attrezzature->pluck('ubicazione')->filter()->unique()->sort() as $u)
                    <option value="{{ $u }}">{{ $u }}</option>
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

    <p class="results-count"><strong id="visibleCount">{{ $attrezzature->count() }}</strong> risultati</p>

    {{-- Tabella --}}
    <div class="table-card">
        <div class="table-wrapper">
            <table class="att-table">
                <thead>
                <tr>
                    <th class="no-sort" style="width:52px;"></th>
                    <th data-col="1">Nome <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th data-col="2">Codice <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th data-col="3">Marca / Modello <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th data-col="4">Ubicazione <i class="bi bi-chevron-expand sort-icon"></i></th>
                    <th class="no-sort">Stato</th>
                    <th class="no-sort">Taratura</th>
                    <th class="no-sort">Pross. Manut.</th>
                    <th class="no-sort" style="width:110px;"></th>
                </tr>
                </thead>
                <tbody id="attTbody">
                @forelse($attrezzature as $a)
                    @php
                        $ut = $a->tarature->first();
                        $um = $a->manutenzioni->first();
                        $tarScad = $ut && $ut->data_scadenza && $ut->data_scadenza->isPast();
                        $tarWarn = $ut && $ut->data_scadenza && !$tarScad && $ut->data_scadenza->diffInDays(now()) <= 30;
                        $thumb   = $a->getFirstMediaUrl('immagini', 'thumb');
                    @endphp
                    <tr data-stato="{{ $a->stato }}"
                        data-tar-scad="{{ $tarScad ? '1' : '0' }}"
                        data-search="{{ strtolower($a->nome.' '.$a->codice.' '.$a->marca.' '.$a->modello.' '.$a->matricola) }}"
                        data-ubicazione="{{ $a->ubicazione }}">

                        <td>
                            @if($thumb)
                                <img src="{{ $thumb }}" class="att-thumb" alt="">
                            @else
                                <div class="att-thumb-ph"><i class="bi bi-tools"></i></div>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('attrezzature.show', $a) }}"
                               class="fw-semibold text-decoration-none text-dark lh-sm">
                                {{ $a->nome }}
                            </a>
                            @if($a->matricola)
                                <br><small class="text-muted" style="font-size:.75rem;">S/N: {{ $a->matricola }}</small>
                            @endif
                        </td>

                        <td>
                            @if($a->codice)
                                <code style="font-size:.78rem;background:#f3f4f6;padding:.2rem .45rem;border-radius:5px;color:#374151;">{{ $a->codice }}</code>
                            @else —
                            @endif
                        </td>

                        <td>
                            {{ $a->marca ?? '' }}
                            @if($a->marca && $a->modello)<span class="text-muted"> / </span>@endif
                            {{ $a->modello ?? '' }}
                            @if(!$a->marca && !$a->modello)<span class="text-muted">—</span>@endif
                        </td>

                        <td>
                            @if($a->ubicazione)
                                <i class="bi bi-geo-alt text-muted me-1" style="font-size:.8rem;"></i>{{ $a->ubicazione }}
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                        <span class="badge-stato badge-{{ $a->stato === 'in_manutenzione' ? 'manutenzione' : $a->stato }}">
                            {{ ['attivo'=>'Attivo','in_manutenzione'=>'Manutenzione','dismesso'=>'Dismesso'][$a->stato] ?? $a->stato }}
                        </span>
                        </td>

                        <td>
                            @if(!$ut)
                                <span class="badge-tar badge-tar-none">Nessuna</span>
                            @elseif(!$ut->data_scadenza)
                                <span class="badge-tar badge-tar-ok">{{ $ut->data_taratura->format('d/m/Y') }}</span>
                            @elseif($tarScad)
                                <span class="badge-tar badge-tar-scad"><i class="bi bi-exclamation-triangle me-1"></i>{{ $ut->data_scadenza->format('d/m/Y') }}</span>
                            @elseif($tarWarn)
                                <span class="badge-tar badge-tar-warn"><i class="bi bi-clock me-1"></i>{{ $ut->data_scadenza->format('d/m/Y') }}</span>
                            @else
                                <span class="badge-tar badge-tar-ok"><i class="bi bi-check me-1"></i>{{ $ut->data_scadenza->format('d/m/Y') }}</span>
                            @endif
                        </td>

                        <td>
                            @if($um && $um->prossima_scadenza)
                                <span class="badge-tar {{ $um->prossima_scadenza->isPast() ? 'badge-tar-scad' : 'badge-tar-ok' }}">
                                <i class="bi bi-wrench me-1"></i>{{ $um->prossima_scadenza->format('d/m/Y') }}
                            </span>
                            @else
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('attrezzature.show', $a) }}" class="btn-action" title="Dettaglio"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('attrezzature.edit', $a) }}" class="btn-action" title="Modifica"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('attrezzature.destroy', $a) }}" method="POST"
                                      onsubmit="return confirm('Eliminare {{ addslashes($a->nome) }}?')">
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
                                <i class="bi bi-tools"></i>
                                Nessuna attrezzatura registrata.<br>
                                <a href="{{ route('attrezzature.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus"></i> Aggiungi la prima
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
            const rows      = Array.from(document.querySelectorAll('#attTbody tr'));
            const search    = document.getElementById('searchInput');
            const filterUb  = document.getElementById('filterUbicazione');
            const btnReset  = document.getElementById('btnReset');
            const noResults = document.getElementById('noResults');
            const cntEl     = document.getElementById('visibleCount');

            let pill    = 'tutti';
            let sortCol = null;
            let sortAsc = true;

            function filter() {
                const q  = search.value.toLowerCase().trim();
                const ub = filterUb.value;
                let n = 0;
                rows.forEach(r => {
                    let show = true;
                    if (pill === 'taratura_scaduta' && r.dataset.tarScad !== '1') show = false;
                    else if (!['tutti','taratura_scaduta'].includes(pill) && r.dataset.stato !== pill) show = false;
                    if (q  && !r.dataset.search.includes(q))        show = false;
                    if (ub && r.dataset.ubicazione !== ub)           show = false;
                    r.style.display = show ? '' : 'none';
                    if (show) n++;
                });
                cntEl.textContent = n;
                noResults.style.display = (n === 0 && rows.length > 0) ? 'block' : 'none';
            }

            // Pills
            document.querySelectorAll('.pill').forEach(p => {
                p.addEventListener('click', () => {
                    document.querySelectorAll('.pill').forEach(x => x.classList.remove('active'));
                    p.classList.add('active');
                    pill = p.dataset.filter;
                    filter();
                });
            });

            search.addEventListener('input', filter);
            filterUb.addEventListener('change', filter);

            btnReset.addEventListener('click', () => {
                search.value = '';
                filterUb.value = '';
                pill = 'tutti';
                document.querySelectorAll('.pill').forEach((p,i) => p.classList.toggle('active', i===0));
                filter();
            });

            // Sort
            document.querySelectorAll('.att-table thead th[data-col]').forEach(th => {
                th.addEventListener('click', () => {
                    const col = parseInt(th.dataset.col);
                    if (sortCol === col) sortAsc = !sortAsc;
                    else { sortCol = col; sortAsc = true; }
                    document.querySelectorAll('.att-table thead th').forEach(t => {
                        t.classList.remove('sorted');
                        const ic = t.querySelector('.sort-icon');
                        if (ic) ic.className = 'bi bi-chevron-expand sort-icon';
                    });
                    th.classList.add('sorted');
                    th.querySelector('.sort-icon').className = `bi bi-chevron-${sortAsc?'up':'down'} sort-icon`;

                    const tbody = document.getElementById('attTbody');
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
