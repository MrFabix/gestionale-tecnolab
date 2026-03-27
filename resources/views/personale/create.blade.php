{{-- resources/views/personale/create.blade.php --}}
@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-person-plus"></i> Nuovo Dipendente</h1>
        <a href="{{ route('personale.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Torna all'elenco
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('personale.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('personale._form')
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Salva dipendente
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
