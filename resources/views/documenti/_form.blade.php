<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Titolo *</label>
        <input type="text" name="titolo" class="form-control @error('titolo') is-invalid @enderror"
               value="{{ old('titolo', $documento->titolo ?? '') }}" required>
        @error('titolo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Categoria</label>
        <input type="text" name="categoria" class="form-control"
               value="{{ old('categoria', $documento->categoria ?? '') }}"
               placeholder="Es. Manuale, Procedura…">
    </div>
    <div class="col-md-3">
        <label class="form-label">Stato</label>
        <select name="stato" class="form-select">
            @foreach(['bozza' => 'Bozza', 'attivo' => 'Attivo', 'obsoleto' => 'Obsoleto'] as $val => $label)
                <option value="{{ $val }}" {{ old('stato', $documento->stato ?? 'bozza') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Descrizione</label>
        <textarea name="descrizione" class="form-control" rows="3">{{ old('descrizione', $documento->descrizione ?? '') }}</textarea>
    </div>
</div>
