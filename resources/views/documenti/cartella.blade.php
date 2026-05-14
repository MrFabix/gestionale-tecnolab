@extends('layouts.app')
@section('content')

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('documenti.index') }}"><i class="bi bi-folder2-open"></i> Documenti</a>
            </li>
            @foreach($cartella->breadcrumb() as $i => $c)
                @if($loop->last)
                    <li class="breadcrumb-item active">{{ $c->nome }}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ route('cartelle.show', $c) }}">{{ $c->nome }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @include('documenti._filesystem', [
        'parentId'  => $cartella->id,
        'cartelle'  => $cartella->sottocartelle,
        'documenti' => $cartella->documenti,
    ])

@endsection
