@extends('layouts.app')
@section('content')
    <h2>Log azioni utenti</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Data/Ora</th>
                <th>Utente (azione)</th>
                <th>Azione</th>
                <th>Su utente</th>
                <th>Dettagli</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user->name ?? '-' }} <br><small>{{ $log->user->username ?? '-' }}</small></td>
                    <td><span class="badge bg-info">{{ ucfirst($log->azione) }}</span></td>
                    <td>{{ $log->targetUser->name ?? '-' }} <br><small>{{ $log->targetUser->username ?? '-' }}</small></td>
                    <td>{{ $log->dettagli }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $logs->links() }}</div>
@endsection

