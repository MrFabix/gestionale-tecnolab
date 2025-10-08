@extends('layouts.app')
@section('content')
    <h2>Gestione Utenti</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Nuovo Utente</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Username</th>
                <th>Email</th>
                <th>Ruolo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-primary">{{ $user->ruolo }}</span></td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Modifica</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Eliminare questo utente?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Elimina</button>
                        </form>
                        <form action="{{ route('admin.users.sendCredentials', $user) }}" method="POST" style="display:inline-block">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info" title="Invia credenziali via email">
                                <i class="bi bi-envelope"></i> Invia credenziali
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
