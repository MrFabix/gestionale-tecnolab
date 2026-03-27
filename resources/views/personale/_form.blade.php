{{-- Partial: resources/views/personale/_form.blade.php --}}
{{-- Usato da create.blade.php e edit.blade.php --}}

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

{{-- ── ANAGRAFICA ─────────────────────────────────────────── --}}
<h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size:.75rem;letter-spacing:.8px;">
    <i class="bi bi-person-vcard me-1"></i> Anagrafica
</h6>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Cognome <span class="text-danger">*</span></label>
        <input type="text" name="cognome" class="form-control @error('cognome') is-invalid @enderror"
               value="{{ old('cognome', $personale->cognome ?? '') }}" required>
        @error('cognome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Nome <span class="text-danger">*</span></label>
        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
               value="{{ old('nome', $personale->nome ?? '') }}" required>
        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Codice Fiscale</label>
        <input type="text" name="codice_fiscale" class="form-control @error('codice_fiscale') is-invalid @enderror"
               value="{{ old('codice_fiscale', $personale->codice_fiscale ?? '') }}"
               maxlength="16" style="text-transform:uppercase;"
               oninput="this.value=this.value.toUpperCase()">
        @error('codice_fiscale')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Data di nascita</label>
        <input type="date" name="data_nascita" class="form-control"
               value="{{ old('data_nascita', isset($personale->data_nascita) ? $personale->data_nascita->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Luogo di nascita</label>
        <input type="text" name="luogo_nascita" class="form-control"
               value="{{ old('luogo_nascita', $personale->luogo_nascita ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Indirizzo residenza</label>
        <input type="text" name="indirizzo" class="form-control"
               value="{{ old('indirizzo', $personale->indirizzo ?? '') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Telefono</label>
        <input type="text" name="telefono" class="form-control"
               value="{{ old('telefono', $personale->telefono ?? '') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $personale->email ?? '') }}">
    </div>
</div>

{{-- ── DATI AZIENDALI ──────────────────────────────────────── --}}
<h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size:.75rem;letter-spacing:.8px;">
    <i class="bi bi-briefcase me-1"></i> Dati aziendali
</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Qualifica</label>
        <input type="text" name="qualifica" class="form-control"
               value="{{ old('qualifica', $personale->qualifica ?? '') }}"
               placeholder="es: Tecnico di laboratorio">
    </div>
    <div class="col-md-3">
        <label class="form-label">Reparto</label>
        <input type="text" name="reparto" class="form-control"
               value="{{ old('reparto', $personale->reparto ?? '') }}"
               placeholder="es: Laboratorio analisi">
    </div>
    <div class="col-md-2">
        <label class="form-label">Data assunzione</label>
        <input type="date" name="data_assunzione" class="form-control"
               value="{{ old('data_assunzione', isset($personale->data_assunzione) ? $personale->data_assunzione->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Fine rapporto</label>
        <input type="date" name="data_fine_rapporto" class="form-control"
               value="{{ old('data_fine_rapporto', isset($personale->data_fine_rapporto) ? $personale->data_fine_rapporto->format('Y-m-d') : '') }}">
    </div>
    <div class="col-md-1">
        <label class="form-label">Stato</label>
        <select name="stato" class="form-select">
            @foreach(['attivo'=>'Attivo','in_ferie'=>'In ferie','sospeso'=>'Sospeso','dimesso'=>'Dimesso'] as $v=>$l)
                <option value="{{ $v }}" {{ old('stato', $personale->stato ?? 'attivo') === $v ? 'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">
            <i class="bi bi-person-gear me-1"></i>
            Collegamento utente sistema
            <small class="text-muted">(opzionale)</small>
        </label>
        <select name="user_id" class="form-select">
            <option value="">— Nessun utente collegato —</option>
            @foreach($utenti as $u)
                <option value="{{ $u->id }}"
                    {{ old('user_id', $personale->user_id ?? null) == $u->id ? 'selected' : '' }}>
                    {{ $u->name }} ({{ $u->username }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">Collega questo dipendente ad un account di accesso al gestionale</small>
    </div>
    <div class="col-md-8">
        <label class="form-label">Note</label>
        <textarea name="note" class="form-control" rows="2">{{ old('note', $personale->note ?? '') }}</textarea>
    </div>
</div>

{{-- ── FOTO PROFILO ────────────────────────────────────────── --}}
<h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size:.75rem;letter-spacing:.8px;">
    <i class="bi bi-camera me-1"></i> Foto profilo
</h6>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        @isset($personale)
            @if($personale->getFirstMediaUrl('foto','thumb'))
                <img src="{{ $personale->getFirstMediaUrl('foto','thumb') }}"
                     class="rounded-circle border mb-2"
                     style="width:80px;height:80px;object-fit:cover;">
            @endif
        @endisset
        <input type="file" name="foto" class="form-control" accept="image/*">
        <small class="text-muted">JPG/PNG, max 3MB. Sostituisce la foto esistente.</small>
    </div>
</div>

{{-- ── DOCUMENTI ───────────────────────────────────────────── --}}
<h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size:.75rem;letter-spacing:.8px;">
    <i class="bi bi-file-earmark-text me-1"></i> Documenti (contratto, patente, CI…)
</h6>
<div class="row g-3">
    <div class="col-md-6">
        <input type="file" name="documenti[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
        <small class="text-muted">PDF o immagini — max 20MB ciascuno. Puoi selezionarne più di uno.</small>
    </div>
    {{-- Documenti già caricati (solo in edit) --}}
    @isset($documenti)
        @if($documenti->count())
            <div class="col-md-6">
                <ul class="list-group">
                    @foreach($documenti as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <a href="{{ $doc->getUrl() }}" target="_blank" class="text-decoration-none small">
                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>{{ $doc->name }}
                            </a>
                            <form action="{{ route('personale.media.destroy', [$personale, $doc->id]) }}" method="POST"
                                  onsubmit="return confirm('Eliminare questo documento?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endisset
</div>
