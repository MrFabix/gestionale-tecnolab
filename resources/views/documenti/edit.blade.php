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
                @include('documenti._form')
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salva modifiche</button>
                </div>
            </form>
        </div>
    </div>
@endsection
