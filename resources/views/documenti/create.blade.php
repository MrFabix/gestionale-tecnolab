@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-file-earmark-plus"></i> Nuovo documento</h1>
        <a href="{{ route('documenti.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Elenco
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('documenti.store') }}" method="POST">
                @csrf
                @include('documenti._form')
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Crea documento</button>
                </div>
            </form>
        </div>
    </div>
@endsection
