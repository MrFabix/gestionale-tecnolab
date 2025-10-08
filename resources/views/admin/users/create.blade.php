@extends('layouts.app')
@section('content')
    <h2>Nuovo Utente</h2>
    <form method="POST" action="{{ route('admin.users.store') }}" class="card p-4 mb-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
            @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Ruolo</label>
            <select name="ruolo" class="form-select" required>
                <option value="user" {{ old('ruolo') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('ruolo') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('ruolo') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Conferma Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success btn-sm">Salva Modifiche</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Annulla</a>
        </div>
       </form>
@endsection

