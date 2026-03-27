{{-- Partial riutilizzato da create.blade.php e edit.blade.php --}}

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-3">
    {{-- Dati anagrafici --}}
    <div class="col-md-6">
        <label class="form-label">Nome <span class="text-danger">*</span></label>
        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
               value="{{ old('nome', $attrezzatura->nome ?? '') }}" required>
        @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Codice interno</label>
        <input type="text" name="codice" class="form-control @error('codice') is-invalid @enderror"
               value="{{ old('codice', $attrezzatura->codice ?? '') }}">
        @error('codice')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Stato</label>
        <select name="stato" class="form-select">
            @foreach(['attivo'=>'Attivo','in_manutenzione'=>'In manutenzione','dismesso'=>'Dismesso'] as $v=>$l)
                <option value="{{ $v }}" {{ old('stato', $attrezzatura->stato ?? 'attivo') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Marca</label>
        <input type="text" name="marca" class="form-control"
               value="{{ old('marca', $attrezzatura->marca ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Modello</label>
        <input type="text" name="modello" class="form-control"
               value="{{ old('modello', $attrezzatura->modello ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Matricola / Serial N°</label>
        <input type="text" name="matricola" class="form-control"
               value="{{ old('matricola', $attrezzatura->matricola ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Ubicazione</label>
        <input type="text" name="ubicazione" class="form-control"
               value="{{ old('ubicazione', $attrezzatura->ubicazione ?? '') }}"
               placeholder="es: Laboratorio A - Banco 3">
    </div>
    <div class="col-md-6">
        <label class="form-label">Note</label>
        <textarea name="note" class="form-control" rows="2">{{ old('note', $attrezzatura->note ?? '') }}</textarea>
    </div>

    {{-- Upload immagini --}}
    <div class="col-12">
        <hr><h5><i class="bi bi-images"></i> Foto / Immagini</h5>
        <input type="file" name="immagini[]" class="form-control" multiple accept="image/*">
        <small class="text-muted">Puoi selezionare più immagini (JPG, PNG, WEBP — max 5MB ciascuna)</small>

        {{-- Immagini già caricate (solo in edit) --}}
        @isset($immagini)
            @if($immagini->count())
                <div class="row g-2 mt-2">
                    @foreach($immagini as $img)
                        <div class="col-auto text-center">
                            <img src="{{ $img->getUrl() }}" class="rounded border" style="width:100px;height:100px;object-fit:cover;">
                            <div class="mt-1">
                                <button type="button" class="btn btn-danger btn-sm"
                                        onclick="if(confirm('Eliminare questa immagine?')) document.getElementById('delete-img-{{ $img->id }}').submit()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endisset
    </div>

    {{-- Upload documenti --}}
    <div class="col-12">
        <hr><h5><i class="bi bi-file-earmark-pdf"></i> Documenti (manuali, schede tecniche...)</h5>
        <input type="file" name="documenti[]" class="form-control" multiple accept=".pdf,.doc,.docx">
        <small class="text-muted">PDF, Word — max 20MB ciascuno</small>

        {{-- Documenti già caricati (solo in edit) --}}
        @isset($documenti)
            @if($documenti->count())
                <ul class="list-group mt-2">
                    @foreach($documenti as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ $doc->getUrl() }}" target="_blank">
                                <i class="bi bi-file-earmark-pdf text-danger"></i> {{ $doc->name }}
                            </a>
                            <button type="button" class="btn btn-sm btn-danger"
                                    onclick="if(confirm('Eliminare questo documento?')) document.getElementById('delete-doc-{{ $doc->id }}').submit()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endisset
    </div>
</div>
