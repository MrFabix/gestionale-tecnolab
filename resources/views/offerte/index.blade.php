@extends('layouts.app')

@section('content')
    <style>
        .off-header { background:linear-gradient(135deg,#1a1a2e,#c00000 200%); border-radius:16px; padding:1.5rem 2rem; margin-bottom:1.5rem; color:#fff; }
        .off-header h1 { font-size:1.8rem; font-weight:700; margin:0; }
        .off-header p  { margin:0; opacity:.65; font-size:.9rem; }

        .filter-bar { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:flex-end; }
        .filter-bar label { font-size:.72rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; display:block; margin-bottom:.3rem; }
        .filter-bar .form-control, .filter-bar .form-select { border-radius:8px; border-color:#e0e0e0; font-size:.875rem; }

        .pill-filters { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
        .pill { padding:.35rem .9rem; border-radius:999px; font-size:.8rem; font-weight:600; border:1.5px solid #d1d5db; background:#f9fafb; color:#374151; cursor:pointer; transition:all .15s; user-select:none; }
        .pill:hover,.pill.active { border-color:#c00000; background:#c00000; color:#fff; }
        .pill .cnt { display:inline-flex; align-items:center; justify-content:center; background:rgba(0,0,0,.15); border-radius:999px; width:18px; height:18px; font-size:.68rem; margin-left:.3rem; }

        .off-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
        .off-table thead th { background:#f8fafc; border-bottom:2px solid #e5e7eb; padding:.75rem 1rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; white-space:nowrap; }
        .off-table tbody tr { border-bottom:1px solid #f0f0f0; transition:background .1s; }
        .off-table tbody tr:hover { background:#fff5f5; }
        .off-table td { padding:.65rem 1rem; vertical-align:middle; }

        .badge-stato { padding:.28rem .7rem; border-radius:999px; font-size:.71rem; font-weight:700; white-space:nowrap; }
        .badge-bozza     { background:#f3f4f6; color:#6b7280; }
        .badge-inviata   { background:#dbeafe; color:#1e40af; }
        .badge-accettata { background:#dcfce7; color:#15803d; }
        .badge-rifiutata { background:#fee2e2; color:#b91c1c; }

        .btn-action { width:30px; height:30px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; display:inline-flex; align-items:center; justify-content:center; color:#374151; font-size:.82rem; transition:all .15s; text-decoration:none; }
        .btn-action:hover      { background:#f3f4f6; color:#c00000; border-color:#fca5a5; }
        .btn-action.del:hover  { background:#fee2e2; color:#b91c1c; border-color:#fca5a5; }
        .btn-action.ok:hover   { background:#dcfce7; color:#15803d; border-color:#86efac; }
        .btn-action.dl:hover   { background:#ede9fe; color:#5b21b6; border-color:#c4b5fd; }

        .totale-cell { font-weight:700; font-variant-numeric:tabular-nums; }
        .table-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }
        .results-count { font-size:.8rem; color:#9ca3af; margin-bottom:.5rem; }
        .results-count strong { color:#374151; }
        .empty-state { text-align:center; padding:4rem 2rem; color:#9ca3af; }
        .empty-state i { font-size:2.8rem; display:block; margin-bottom:1rem; }
        .badge-commessa { background:#ede9fe; color:#5b21b6; padding:.2rem .55rem; border-radius:999px; font-size:.7rem; font-weight:600; text-decoration:none; }
    </style>

    <div class="off-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-file-earmark-text me-2"></i>Offerte / Preventivi</h1>
                <p>{{ $offerte->count() }} offerte totali</p>
            </div>
            <a href="{{ route('offerte.create') }}" class="btn btn-light fw-semibold px-4" style="color:#c00000;">
                <i class="bi bi-plus-lg me-1"></i> Nuova offerta
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Pills --}}
    <div class="pill-filters">
        <span class="pill active" data-filter="tutti">Tutte <span class="cnt">{{ $offerte->count() }}</span></span>
        <span class="pill" data-filter="bozza">Bozza <span class="cnt">{{ $offerte->where('stato','bozza')->count() }}</span></span>
        <span class="pill" data-filter="inviata">Inviate <span class="cnt">{{ $offerte->where('stato','inviata')->count() }}</span></span>
        <span class="pill" data-filter="accettata">Accettate <span class="cnt">{{ $offerte->where('stato','accettata')->count() }}</span></span>
        <span class="pill" data-filter="rifiutata">Rifiutate <span class="cnt">{{ $offerte->where('stato','rifiutata')->count() }}</span></span>
    </div>

    {{-- Filtri --}}
    <div class="filter-bar">
        <div style="flex:1;min-width:200px;">
            <label>Cerca</label>
            <div class="input-group">
            <span class="input-group-text bg-white border-end-0 pe-1" style="border-radius:8px 0 0 8px;border-color:#e0e0e0;">
                <i class="bi bi-search text-muted" style="font-size:.82rem;"></i>
            </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-1"
                       placeholder="Numero, cliente…" style="border-radius:0 8px 8px 0;">
            </div>
        </div>
        <div style="min-width:200px;">
            <label>Cliente</label>
            <select id="filterCliente" class="form-select">
                <option value="">Tutti</option>
                @foreach($clienti as $c)
                    <option value="{{ $c->ragione_sociale }}">{{ $c->ragione_sociale }}</option>
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

    <p class="results-count"><strong id="visibleCount">{{ $offerte->count() }}</strong> risultati</p>

    <div class="table-card">
        <div style="overflow-x:auto;">
            <table class="off-table">
                <thead>
                <tr>
                    <th>N° Offerta</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Validità</th>
                    <th>Stato</th>
                    <th>Totale (€)</th>
                    <th>Commessa</th>
                    <th style="width:130px;"></th>
                </tr>
                </thead>
                <tbody id="offTbody">
                @forelse($offerte as $o)
                    <tr data-stato="{{ $o->stato }}"
                        data-search="{{ strtolower($o->numero.' '.$o->cliente->ragione_sociale) }}"
                        data-cliente="{{ $o->cliente->ragione_sociale }}">

                        <td>
                            <a href="{{ route('offerte.show', $o) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ $o->numero }}
                            </a>
                        </td>
                        <td>{{ $o->data->format('d/m/Y') }}</td>
                        <td>{{ $o->cliente->ragione_sociale }}</td>
                        <td>
                            @if($o->validita_fino)
                                @if($o->isScaduta())
                                    <span style="color:#b91c1c;font-size:.8rem;"><i class="bi bi-exclamation-triangle"></i> {{ $o->validita_fino->format('d/m/Y') }}</span>
                                @else
                                    <span style="font-size:.85rem;">{{ $o->validita_fino->format('d/m/Y') }}</span>
                                @endif
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><span class="badge-stato badge-{{ $o->stato }}">{{ ucfirst($o->stato) }}</span></td>
                        <td class="totale-cell">{{ number_format($o->totale, 2, ',', '.') }}</td>
                        <td>
                            @if($o->commessa)
                                <a href="{{ route('commesse.show', $o->commessa) }}" class="badge-commessa">
                                    <i class="bi bi-briefcase me-1"></i>{{ $o->commessa->codice }}
                                </a>
                            @else <span class="text-muted" style="font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('offerte.show', $o) }}"         class="btn-action" title="Dettaglio"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('offerte.edit', $o) }}"         class="btn-action" title="Modifica"><i class="bi bi-pencil"></i></a>
                                <a href="{{ route('offerte.downloadWord', $o) }}" class="btn-action dl" title="Scarica Word"><i class="bi bi-file-earmark-word"></i></a>
                                @if(!in_array($o->stato, ['accettata','rifiutata']))
                                    <form action="{{ route('offerte.accetta', $o) }}" method="POST" onsubmit="return confirm('Accettare l\'offerta e creare la commessa?')">
                                        @csrf
                                        <button class="btn-action ok" title="Accetta → Commessa"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                @endif
                                <form action="{{ route('offerte.destroy', $o) }}" method="POST" onsubmit="return confirm('Eliminare l\'offerta {{ addslashes($o->numero) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-action del" title="Elimina"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-file-earmark-text"></i>
                                Nessuna offerta registrata.<br>
                                <a href="{{ route('offerte.create') }}" class="btn btn-primary mt-3"><i class="bi bi-plus"></i> Crea la prima</a>
                            </div>
                        </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="noResults" style="display:none;">
            <div class="empty-state"><i class="bi bi-funnel"></i> Nessun risultato.</div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function(){
            const rows=Array.from(document.querySelectorAll('#offTbody tr'));
            const search=document.getElementById('searchInput');
            const filterC=document.getElementById('filterCliente');
            const btnR=document.getElementById('btnReset');
            const noR=document.getElementById('noResults');
            const cnt=document.getElementById('visibleCount');
            let pill='tutti';

            function filter(){
                const q=search.value.toLowerCase().trim();
                const fc=filterC.value;
                let n=0;
                rows.forEach(r=>{
                    let show=true;
                    if(pill!=='tutti' && r.dataset.stato!==pill) show=false;
                    if(q && !r.dataset.search.includes(q)) show=false;
                    if(fc && r.dataset.cliente!==fc) show=false;
                    r.style.display=show?'':'none';
                    if(show)n++;
                });
                cnt.textContent=n;
                noR.style.display=(n===0&&rows.length>0)?'block':'none';
            }

            document.querySelectorAll('.pill').forEach(p=>{
                p.addEventListener('click',()=>{
                    document.querySelectorAll('.pill').forEach(x=>x.classList.remove('active'));
                    p.classList.add('active'); pill=p.dataset.filter; filter();
                });
            });
            search.addEventListener('input',filter);
            filterC.addEventListener('change',filter);
            btnR.addEventListener('click',()=>{
                search.value='';filterC.value='';pill='tutti';
                document.querySelectorAll('.pill').forEach((p,i)=>p.classList.toggle('active',i===0));
                filter();
            });
        })();
    </script>
@endsection
