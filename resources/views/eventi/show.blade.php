@extends('layouts.app')
@section('content')
<h1>Dettaglio evento</h1>
<p><strong>Titolo:</strong> {{ $evento->titolo }}</p>
<p><strong>Descrizione:</strong> {{ $evento->descrizione }}</p>
<p><strong>Inizio:</strong> {{ $evento->inizio }}</p>
<p><strong>Fine:</strong> {{ $evento->fine }}</p>
@if($evento->user_id)
    <p><strong>Privato</strong></p>
@else
    <p><strong>Globale</strong></p>
@endif
<a href="{{ route('eventi.index') }}">Torna alla lista</a>
@endsection
