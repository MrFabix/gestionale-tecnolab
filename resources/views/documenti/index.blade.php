@extends('layouts.app')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-folder2-open"></i> Documenti</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @include('documenti._filesystem', ['parentId' => null])

@endsection
