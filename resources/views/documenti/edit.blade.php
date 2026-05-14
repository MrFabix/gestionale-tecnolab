@extends('layouts.app')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-pencil"></i> Modifica documento</h1>
        <a href="{{ route('documenti.show', $documento) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Annulla
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('documenti.update', $documento) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Titolo *</label>
                        <input type="text" name="titolo" class="form-control @error('titolo') is-invalid @enderror"
                               value="{{ old('titolo', $documento->titolo) }}" required>
                        @error('titolo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stato</label>
                        <select name="stato" class="form-select">
                            @foreach(['bozza' => 'Bozza', 'attivo' => 'Attivo', 'obsoleto' => 'Obsoleto'] as $val => $label)
                                <option value="{{ $val }}" {{ old('stato', $documento->stato) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cartella</label>
                        <select name="cartella_id" class="form-select">
                            <option value="">— Radice —</option>
                            @foreach($cartelle as $c)
                                <option value="{{ $c->id }}" {{ old('cartella_id', $documento->cartella_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descrizione</label>
                        <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione', $documento->descrizione) }}</textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salva</button>
                </div>
            </form>
        </div>
    </div>

@endsection
