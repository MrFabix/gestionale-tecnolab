@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-pencil"></i> Modifica — {{ $attrezzatura->nome }}</h1>
        <a href="{{ route('attrezzature.show', $attrezzatura) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna al dettaglio
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="form-edit-attrezzatura" action="{{ route('attrezzature.update', $attrezzatura) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('attrezzature._form')
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salva modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Form eliminazione media (fuori dal form principale per evitare form annidati) --}}
    @foreach($immagini as $img)
        <form id="delete-img-{{ $img->id }}"
              action="{{ route('attrezzature.media.destroy', [$attrezzatura, $img->id]) }}"
              method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach
    @foreach($documenti as $doc)
        <form id="delete-doc-{{ $doc->id }}"
              action="{{ route('attrezzature.media.destroy', [$attrezzatura, $doc->id]) }}"
              method="POST" style="display:none">
            @csrf @method('DELETE')
        </form>
    @endforeach
@endsection
