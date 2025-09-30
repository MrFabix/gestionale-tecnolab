@extends('layouts.app')
@section('content')
<div class="container" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Modifica evento</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('eventi.update', $evento->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="titolo" class="form-label">Titolo</label>
                    <input type="text" name="titolo" id="titolo" class="form-control @error('titolo') is-invalid @enderror" value="{{ old('titolo', $evento->titolo) }}" required>
                    @error('titolo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="descrizione" class="form-label">Descrizione</label>
                    <textarea name="descrizione" id="descrizione" class="form-control @error('descrizione') is-invalid @enderror">{{ old('descrizione', $evento->descrizione) }}</textarea>
                    @error('descrizione')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="inizio" class="form-label">Data/ora inizio</label>
                    <input type="datetime-local" name="inizio" id="inizio" class="form-control @error('inizio') is-invalid @enderror" value="{{ old('inizio', date('Y-m-d\TH:i', strtotime($evento->inizio))) }}" required>
                    @error('inizio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="fine" class="form-label">Data/ora fine</label>
                    <input type="datetime-local" name="fine" id="fine" class="form-control @error('fine') is-invalid @enderror" value="{{ old('fine', $evento->fine ? date('Y-m-d\TH:i', strtotime($evento->fine)) : '') }}">
                    @error('fine')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('eventi.index') }}" class="btn btn-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
