@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-plus-circle"></i> Nuova Attrezzatura</h1>
        <a href="{{ route('attrezzature.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna all'elenco
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('attrezzature.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('attrezzature._form')
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salva Attrezzatura
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
