@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dettagli Commessa</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ $commessa->codice }}</h4>
                <p class="card-text"><strong>Descrizione:</strong> {{ $commessa->descrizione }}</p>
                <p class="card-text"><strong>Cliente:</strong> {{ $commessa->cliente->ragione_sociale ?? '-' }}</p>
                <p class="card-text"><strong>Data Inizio:</strong> {{ $commessa->data_inizio ?? '-' }}</p>
                <p class="card-text"><strong>Data Fine:</strong> {{ $commessa->data_fine ?? '-' }}</p>
                <p class="card-text"><strong>Stato:</strong>
                    <span class="badge
                    @if($commessa->stato=='aperta') bg-success
                    @elseif($commessa->stato=='in_lavorazione') bg-warning
                    @else bg-secondary @endif">
                    {{ ucfirst($commessa->stato) }}
                </span>
                </p>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('commesse.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Torna alla lista
                </a>
                <div>
                    <a href="{{ route('commesse.edit', $commessa->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Modifica
                    </a>
                    <form action="{{ route('commesse.destroy', $commessa->id) }}" method="POST" class="d-inline delete-form">
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
                        text: "Questa azione eliminerà definitivamente la commessa.",
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
