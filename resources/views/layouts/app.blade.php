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
        body {
            min-height: 100vh;
            display: flex;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1f1f2e 0%, #181824 100%);
            color: #d1d1e0;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 8px rgba(0,0,0,0.2);
        }
        .sidebar .brand {
            font-size: 1.4rem;
            font-weight: 600;
            padding: 1.5rem 1rem;
            text-align: center;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .nav-link {
            color: #b0b3c0;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.75rem 1.2rem;
            border-radius: 8px;
            margin: 4px 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #2d2d44;
            color: #fff;
        }
        .sidebar .nav-link.active i,
        .sidebar .nav-link:hover i {
            color: #0d6efd;
        }
        /* Contenuto */
        .content {
            flex-grow: 1;
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
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a href="{{ route('clienti.index') }}"
               class="nav-link {{ request()->routeIs('clienti.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Clienti
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('commesse.index') }}"
               class="nav-link {{ request()->routeIs('commesse.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase-fill"></i> Commesse
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Report
            </a>
        </li>
        <li class="nav-item d-none">
            <a href="{{ route('eventi.index') }}"
               class="nav-link {{ request()->routeIs('eventi.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event-fill"></i> Calendario
            </a>
        </li>
        @if(Auth::check() && Auth::user()->ruolo === 'admin')
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Gestione Utenti
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
{{--SweetAlert2--}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield('scripts')

</body>
</html>
