@extends('layouts.app')
@section('content')
<h1>Crea nuovo evento</h1>
<form method="POST" action="{{ route('eventi.store') }}">
    @csrf
    <label for="titolo">Titolo:</label>
    <input type="text" name="titolo" id="titolo" required><br>
    <label for="descrizione">Descrizione:</label>
    <textarea name="descrizione" id="descrizione"></textarea><br>
    <label for="inizio">Inizio:</label>
    <input type="datetime-local" name="inizio" id="inizio" required><br>
    <label for="fine">Fine:</label>
    <input type="datetime-local" name="fine" id="fine"><br>
    @if(Auth::user() && Auth::user()->ruolo === 'admin')
        <label><input type="checkbox" name="globale"> Evento globale</label><br>
    @endif
    <button type="submit">Salva</button>
</form>
@endsection

