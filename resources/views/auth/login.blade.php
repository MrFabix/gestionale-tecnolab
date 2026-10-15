<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi &mdash; {{ config('app.name', 'TecnoLab Gestionale') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3 & Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-primary: #d90429;
            --brand-primary-hover: #b71c1c;
            --brand-dark: #181824;
            --brand-surface: #ffffff;
            --brand-muted: #64748b;
            --brand-border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* Colonna Form (Sinistra) */
        .login-form-side {
            flex: 1 1 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 2.5rem;
            background: #ffffff;
            position: relative;
            z-index: 2;
        }

        @media (min-width: 992px) {
            .login-form-side {
                max-width: 520px;
                padding: 4rem 3.5rem;
            }
        }

        @media (min-width: 1400px) {
            .login-form-side {
                max-width: 580px;
                padding: 5rem 4.5rem;
            }
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            margin: auto;
        }

        .brand-logo-img {
            max-height: 64px;
            max-width: 220px;
            object-fit: contain;
            display: block;
            margin-bottom: 2rem;
            transition: transform 0.2s ease;
        }

        .login-header h1 {
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--brand-dark);
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--brand-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 1.15rem;
            pointer-events: none;
            z-index: 4;
            transition: color 0.2s ease;
        }

        .input-group-custom .form-control {
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            font-size: 0.95rem;
            border-radius: 12px;
            border: 1.5px solid var(--brand-border);
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .input-group-custom .form-control:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(217, 4, 41, 0.12);
        }

        .input-group-custom .form-control:focus + .input-icon,
        .input-group-custom:focus-within .input-icon {
            color: var(--brand-primary);
        }

        .input-group-custom .form-control.is-invalid {
            border-color: #ef4444;
            background-image: none !important;
        }

        .input-group-custom .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
            border-color: #ef4444;
        }

        .input-group-custom .form-control.is-invalid + .input-icon,
        .input-group-custom:has(.form-control.is-invalid) .input-icon {
            color: #ef4444;
        }

        .btn-toggle-password {
            position: absolute;
            right: 0.75rem;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 4;
            transition: color 0.2s ease;
        }

        .btn-toggle-password:hover {
            color: var(--brand-dark);
        }

        .form-check-input {
            width: 1.15em;
            height: 1.15em;
            margin-top: 0.15em;
            border-color: #cbd5e1;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.15);
            border-color: var(--brand-primary);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-primary) 0%, #ba181b 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 0 4px 14px rgba(217, 4, 41, 0.28);
            transition: all 0.25s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #ba181b 0%, #660708 100%);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(217, 4, 41, 0.35);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-link {
            color: var(--brand-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--brand-primary);
            text-decoration: underline;
        }

        .login-footer {
            font-size: 0.825rem;
            color: #94a3b8;
            text-align: center;
            margin-top: 2rem;
        }

        /* Colonna Immagine (Destra) */
        .login-hero-side {
            flex: 1 1 50%;
            position: relative;
            background-color: var(--brand-dark);
            background-image: url('{{ asset('img/login-bg.jpg') }}');
            background-size: cover;
            background-position: center center;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .login-hero-side {
                display: flex;
            }
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(24, 24, 36, 0.88) 0%, rgba(30, 20, 25, 0.75) 50%, rgba(183, 28, 28, 0.75) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 580px;
            color: #ffffff;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-size: 0.825rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            color: #fecdd3;
        }

        .hero-title {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.25rem;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .hero-title span {
            color: #fda4af;
        }

        .hero-description {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #e2e8f0;
            margin-bottom: 2.5rem;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.2rem;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 1.2rem 1.25rem;
            border-radius: 14px;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .feature-item:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.12);
        }

        .feature-item i {
            font-size: 1.4rem;
            color: #f43f5e;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .feature-item h6 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #ffffff;
        }

        .feature-item p {
            font-size: 0.8rem;
            color: #cbd5e1;
            margin-bottom: 0;
            line-height: 1.4;
        }

        /* Decorative ambient glows */
        .ambient-glow-1 {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(217, 4, 41, 0.35) 0%, transparent 70%);
            z-index: 1;
            filter: blur(40px);
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- COLONNA SINISTRA: FORM DI ACCESSO --}}
    <div class="login-form-side">
        <div></div> {{-- Spacer per centratura verticale --}}

        <div class="form-container">
            {{-- Logo Aziendale --}}
            <a href="{{ url('/') }}" class="text-decoration-none">
                <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name', 'TecnoLab') }}" class="brand-logo-img">
            </a>

            {{-- Intestazione --}}
            <div class="login-header">
                <h1>Bentornato</h1>
                <p>Inserisci le tue credenziali per accedere al gestionale</p>
            </div>

            {{-- Eventuale messaggio di errore di sessione --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('status') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Messaggio di Errore Validazione / Credenziali --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 p-3 mb-3 border-0 shadow-sm" role="alert" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important; border-radius: 12px; color: #991b1b;">
                    <i class="bi bi-shield-x fs-4 flex-shrink-0 text-danger"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-0" style="font-size: 0.95rem;">Accesso non riuscito</div>
                        <div class="small" style="font-size: 0.85rem; opacity: 0.9;">
                            @php
                                $firstErr = $errors->first('username') ?: $errors->first('password');
                                if (empty($firstErr) || $firstErr === 'auth.failed') {
                                    $firstErr = 'Nome utente o password non corretti. Verifica i tuoi dati e riprova.';
                                }
                            @endphp
                            {{ $firstErr }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
                </div>
            @endif

            {{-- Form Login --}}
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- Campo Username --}}
                <div class="mb-3">
                    <label for="username" class="form-label">Nome Utente</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person input-icon"></i>
                        <input id="username"
                               type="text"
                               class="form-control @if($errors->has('username') || $errors->any()) is-invalid @endif"
                               name="username"
                               value="{{ old('username') }}"
                               placeholder="Inserisci il tuo username"
                               required
                               autocomplete="username"
                               autofocus>
                    </div>

                </div>

                {{-- Campo Password --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">Password</label>
                    </div>
                    <div class="input-group-custom">
                        <i class="bi bi-lock input-icon"></i>
                        <input id="password"
                               type="password"
                               class="form-control @if($errors->has('password') || $errors->has('username')) is-invalid @endif"
                               name="password"
                               placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                               required
                               autocomplete="current-password">
                        <button type="button" class="btn-toggle-password" id="togglePasswordBtn" title="Mostra/Nascondi password" aria-label="Mostra/Nascondi password">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>

                </div>

                {{-- Ricordami --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="remember"
                               id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Rimani collegato
                        </label>
                    </div>
                </div>

                {{-- Tasto Submit --}}
                <button type="submit" class="btn btn-login w-100" id="loginSubmitBtn">
                    <span>Accedi al Gestionale</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="login-footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'TecnoLab') }} &bull; Piattaforma Gestionale
        </div>
    </div>

    {{-- COLONNA DESTRA: IMMAGINE & SHOWCASE HERO --}}
    <div class="login-hero-side">
        <div class="hero-overlay"></div>
        <div class="ambient-glow-1"></div>

        <div class="hero-content">
            <div class="hero-badge">
                <i class="bi bi-shield-check"></i> Area Riservata TecnoLab
            </div>

            <h2 class="hero-title">
                Precisione, Qualità e <span>Controllo</span> in ogni processo.
            </h2>

            <p class="hero-description">
                La piattaforma integrata per la gestione completa delle commesse, tracciamento della strumentazione, certificati di taratura e rendicontazione tecnica di laboratorio.
            </p>

            <div class="feature-grid">
                <div class="feature-item">
                    <i class="bi bi-kanban"></i>
                    <h6>Gestione Commesse</h6>
                    <p>Avanzamento lavori, documentazione e relazioni clienti in tempo reale.</p>
                </div>

                <div class="feature-item">
                    <i class="bi bi-tools"></i>
                    <h6>Parco Attrezzature</h6>
                    <p>Scadenze tarature, manutenzioni e schede tecniche sempre aggiornate.</p>
                </div>

                <div class="feature-item">
                    <i class="bi bi-file-earmark-check"></i>
                    <h6>Report & Certificati</h6>
                    <p>Generazione automatica e archiviazione conforme dei documenti di prova.</p>
                </div>

                <div class="feature-item">
                    <i class="bi bi-shield-lock"></i>
                    <h6>Sicurezza & Ruoli</h6>
                    <p>Accesso protetto e tracciabilità operativa per l'intero team.</p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Script Bootstrap & Interazioni --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Password Visibility
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    if (togglePasswordBtn && passwordInput && togglePasswordIcon) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            togglePasswordIcon.classList.toggle('bi-eye', !isPassword);
            togglePasswordIcon.classList.toggle('bi-eye-slash', isPassword);
        });
    }

    // Feedback visivo submit
    const loginForm = document.querySelector('form');
    const loginSubmitBtn = document.getElementById('loginSubmitBtn');
    if (loginForm && loginSubmitBtn) {
        loginForm.addEventListener('submit', function () {
            if (loginForm.checkValidity()) {
                loginSubmitBtn.classList.add('disabled');
                loginSubmitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Accesso in corso...</span>
                `;
            }
        });
    }
</script>
</body>
</html>
