<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login - {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-card {
            width: 420px;
            border: 2px solid #b71c1c;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(183,28,28,0.15);
            background: #fff;
            padding: 2.5rem 2rem 2rem 2rem;
            position: relative;
        }
        .login-card h3 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: #b71c1c;
        }
        .login-logo {
            display: block;
            margin: 0 auto 1rem auto;
            width: 64px;
            height: 64px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #b71c1c 0%, #212121 100%);
            border: none;
            font-weight: 600;
            transition: box-shadow 0.2s;
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(183,28,28,0.2);
            background: linear-gradient(90deg, #212121 0%, #b71c1c 100%);
        }
        .form-check-label {
            font-size: 0.95rem;
        }
        .footer {
            text-align: center;
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div>
    <div class="login-card">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Logo" class="login-logo" style="filter: grayscale(100%) brightness(0.5) sepia(1) hue-rotate(-20deg) saturate(8);">
        <h3>Login</h3>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" type="text"
                       class="form-control @error('username') is-invalid @enderror"
                       name="username" value="{{ old('username') }}" required autofocus>
                @error('username')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required>
                @error('password')
                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Ricordami</label>
            </div>
            <!-- ...dentro il form, subito dopo il bottone Accedi -->
            <button type="submit" class="btn btn-primary w-100">Accedi</button>
            <div class="text-center mt-3">
                <a href="#" class="text-danger text-decoration-underline">Password dimenticata?</a>
            </div>
        </form>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Tutti i diritti riservati.
    </div>
</div>
</body>
</html>
