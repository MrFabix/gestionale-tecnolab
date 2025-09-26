@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dettagli Cliente</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ $cliente->ragione_sociale }}</h4>
                <p class="card-text"><strong>Email:</strong> {{ $cliente->email ?? '-' }}</p>
                <p class="card-text"><strong>Telefono:</strong> {{ $cliente->telefono ?? '-' }}</p>
                <p class="card-text"><strong>Referente:</strong> {{ $cliente->referente ?? '-' }}</p>
                <p class="card-text"><strong>Indirizzo:</strong> {{ $cliente->indirizzo ?? '-' }}</p>
                <p class="card-text"><strong>Città:</strong> {{ $cliente->cap }} {{ $cliente->citta }} ({{ $cliente->provincia }})</p>
                <p class="card-text"><strong>P. IVA:</strong> {{ $cliente->partita_iva ?? '-' }}</p>
                <p class="card-text"><strong>Codice Fiscale:</strong> {{ $cliente->codice_fiscale ?? '-' }}</p>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('clienti.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Torna alla lista
                </a>
                <div>
                    <a href="{{ route('clienti.edit', $cliente->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Modifica
                    </a>
                    <form action="{{ route('clienti.destroy', $cliente->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-delete">
                            <i class="bi bi-trash"></i> Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const deleteButtons = document.querySelectorAll('.btn-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const form = this.closest('form');

                    Swal.fire({
                        title: "Sei sicuro?",
                        text: "Questa azione eliminerà definitivamente il cliente.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Sì, elimina",
                        cancelButtonText: "Annulla"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
