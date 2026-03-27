@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-pencil"></i> Modifica — {{ $personale->cognome }} {{ $personale->nome }}</h1>
        <a href="{{ route('personale.show', $personale) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna al dettaglio
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('personale.update', $personale) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('personale._form')
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Salva modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
