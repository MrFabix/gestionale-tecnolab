@extends('layouts.app')
@section('content')
    <h2>Profilo Utente</h2>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Dati personali</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nome:</strong> {{ Auth::user()->name }}</li>
                        <li class="list-group-item"><strong>Username:</strong> {{ Auth::user()->username }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ Auth::user()->email }}</li>
                        <li class="list-group-item"><strong>Ruolo:</strong> <span class="badge bg-primary">{{ Auth::user()->ruolo }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Cambia password</h5>
                    <form method="POST" action="{{ route('profile.updatePassword') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nuova password</label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Conferma nuova password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Aggiorna password</button>
                    </form>
                    @if(session('password_updated'))
                        <div class="alert alert-success mt-3">Password aggiornata con successo.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

