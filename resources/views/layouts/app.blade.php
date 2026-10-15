<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">



    <style>
        /* Session success gestita da Swal toast */
        .alert-success { display: none !important; }
        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            background: linear-gradient(180deg, #181926 0%, #12131c 100%);
            color: #d1d1e0;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.25);
            z-index: 1000;
        }
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.22);
        }
        .sidebar .brand {
            padding: 1.25rem 1rem 1rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: rgba(0,0,0,0.12);
        }
        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #717790;
            padding: 0.9rem 1.1rem 0.25rem 1.1rem;
            user-select: none;
            list-style: none;
        }
        .sidebar-section-title:first-of-type {
            padding-top: 0.4rem;
        }
        .sidebar .nav-link {
            color: #a6abbd;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.48rem 0.85rem;
            border-radius: 7px;
            margin: 1.5px 8px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            position: relative;
        }
        .sidebar .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            margin-right: 11px;
            color: #70758d;
            transition: color 0.2s ease;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }
        .sidebar .nav-link:hover i {
            color: #e0e2ec;
        }
        .sidebar .nav-link.active {
            background: rgba(217, 4, 41, 0.14);
            color: #ffffff;
            font-weight: 600;
            border-left: 3px solid #d90429;
            padding-left: calc(0.85rem - 3px);
        }
        .sidebar .nav-link.active i {
            color: #d90429;
        }
        /* Contenuto */
        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 2rem;
        }
        .content h1 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar">
    <div class="brand">
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="max-width:200px; max-height:120px;">
        </a>
    </div>
    <ul class="nav flex-column mt-2 mb-3">
        {{-- PANORAMICA --}}
        <li class="sidebar-section-title">Panoramica</li>
        <li class="nav-item">
            <a href="{{ route('home') }}"
               class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('eventi.index') }}"
               class="nav-link {{ request()->routeIs('eventi.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Calendario
            </a>
        </li>

        {{-- COMMERCIALE --}}
        <li class="sidebar-section-title">Commerciale</li>
        <li class="nav-item">
            <a href="{{ route('clienti.index') }}"
               class="nav-link {{ request()->routeIs('clienti.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clienti
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('offerte.index') }}"
               class="nav-link {{ request()->routeIs('offerte.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Offerte
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('commesse.index') }}"
               class="nav-link {{ request()->routeIs('commesse.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase-fill"></i> Commesse
            </a>
        </li>

        {{-- LABORATORIO --}}
        <li class="sidebar-section-title">Laboratorio & Prove</li>
        <li class="nav-item">
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check-fill"></i> Rapporti di Prova
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('attrezzature.index') }}"
               class="nav-link {{ request()->routeIs('attrezzature.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Attrezzature
            </a>
        </li>

        {{-- ACQUISTI & FORNITORI --}}
        <li class="sidebar-section-title">Acquisti & Fornitori</li>
        <li class="nav-item">
            <a href="{{ route('fornitori.index') }}"
               class="nav-link {{ request()->routeIs('fornitori.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Fornitori
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('rda.index') }}"
               class="nav-link {{ request()->routeIs('rda.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check-fill"></i> Richieste Acquisto (RDA)
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('oda.index') }}"
               class="nav-link {{ request()->routeIs('oda.*') ? 'active' : '' }}">
                <i class="bi bi-bag-check-fill"></i> Ordini Acquisto (ODA)
            </a>
        </li>

        {{-- AZIENDA & GESTIONE --}}
        <li class="sidebar-section-title">Azienda & Gestione</li>
        <li class="nav-item">
            <a href="{{ route('personale.index') }}"
               class="nav-link {{ request()->routeIs('personale.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Personale
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('documenti.index') }}"
               class="nav-link {{ request()->routeIs('documenti.*') || request()->routeIs('cartelle.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i> Documenti
            </a>
        </li>
        @if(Auth::check() && Auth::user()->ruolo === 'admin')
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Gestione Utenti
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('impostazioni.index') }}" class="nav-link {{ request()->routeIs('impostazioni.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i> Impostazioni
                </a>
            </li>
        @endif
    </ul>
    <div class="mt-auto p-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
        @auth
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-person-circle fs-3 me-2 text-light"></i>
                <div>
                    <strong class="d-block text-white">{{ Auth::user()->name }}</strong>
                    <small class="text-muted">{{ Auth::user()->username }}</small>
                    <span class="badge bg-primary ms-1">{{ Auth::user()->ruolo }}</span>
                </div>
            </div>
            <div class="d-flex flex-column">
                <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-light mb-2">
                    <i class="bi bi-gear"></i> Profilo
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger w-100">
                        <i class="bi bi-box-arrow-right"></i> Esci
                    </button>
                </form>
            </div>
        @endauth
    </div>



</div>

{{-- Contenuto principale --}}
<div class="content">
    @yield('content')
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Toast globale ─────────────────────────────────
const SwalToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
});

@if(session('success'))
    SwalToast.fire({ icon: 'success', title: @json(session('success')) });
@endif
@if(session('error'))
    SwalToast.fire({ icon: 'error', title: @json(session('error')) });
@endif
@if(session('warning'))
    SwalToast.fire({ icon: 'warning', title: @json(session('warning')) });
@endif

// ── Confirm globale su data-confirm ──────────────
document.addEventListener('submit', function(e) {
    const form = e.target;
    const msg  = form.dataset.confirm;
    if (!msg) return;
    e.preventDefault();
    Swal.fire({
        title: 'Sei sicuro?',
        text: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sì, elimina',
        cancelButtonText: 'Annulla',
    }).then(r => { if (r.isConfirmed) { form.dataset.confirm = ''; form.submit(); } });
});
</script>
@yield('scripts')

</body>
</html>
