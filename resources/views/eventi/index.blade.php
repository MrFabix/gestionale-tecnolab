@extends('layouts.app')
@section('content')
    <h2>Calendario eventi</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalEvento">Aggiungi evento</button>
    <div id="calendar"></div>

    <!-- Modale nuovo evento -->
    <div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('eventi.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEventoLabel">Nuovo evento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Titolo</label>
                            <input type="text" name="titolo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrizione</label>
                            <textarea name="descrizione" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data/ora inizio</label>
                            <input type="datetime-local" name="inizio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data/ora fine</label>
                            <input type="datetime-local" name="fine" class="form-control">
                        </div>
                        @if(Auth::user()->ruolo === 'admin')
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="globale" id="globale">
                            <label class="form-check-label" for="globale">Evento globale (visibile a tutti)</label>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-success">Salva evento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'it',
                height: 650,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    @foreach($eventi as $evento)
                        {
                            title: @json($evento->titolo),
                            start: @json($evento->inizio),
                            end: @json($evento->fine),
                            description: @json($evento->descrizione),
                            color: @json($evento->user_id ? (auth()->id() == $evento->user_id ? '#0d6efd' : '#6c757d') : '#198754'),
                            id: @json($evento->id),
                            editable_custom: {{ (auth()->id() == $evento->user_id || (is_object(auth()->user()) && auth()->user()->ruolo === 'admin')) ? 'true' : 'false' }},
                        },
                    @endforeach
                ],
                eventDidMount: function(info) {
                    if(info.event.extendedProps.description) {
                        var tooltip = document.createElement('div');
                        tooltip.className = 'fc-tooltip';
                        tooltip.innerHTML = info.event.extendedProps.description;
                        info.el.appendChild(tooltip);
                    }
                },
                eventClick: function(info) {
                    let event = info.event;
                    let canEdit = Boolean(event.extendedProps.editable_custom);
                    let html = '<div class="mb-2"><b>Inizio:</b> ' + event.start.toLocaleString() + '</div>';
                    if(event.end) html += '<div class="mb-2"><b>Fine:</b> ' + event.end.toLocaleString() + '</div>';
                    if(event.extendedProps.description) html += '<div class="mb-2"><b>Descrizione:</b> ' + event.extendedProps.description + '</div>';
                    if (canEdit) {
                        html += '<a href="/eventi/' + event.id + '/edit" class="btn btn-primary me-2">Modifica</a>';
                    }
                    Swal.fire({
                        title: event.title,
                        html: html,
                        showCancelButton: canEdit,
                        showConfirmButton: canEdit,
                        confirmButtonText: 'Elimina',
                        cancelButtonText: 'Chiudi',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        focusCancel: true,
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-secondary'
                        },
                    }).then((result) => {
                        if (canEdit && result.isConfirmed) {
                            Swal.fire({
                                title: 'Sei sicuro?',
                                text: 'Questa azione non può essere annullata!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Sì, elimina!',
                                cancelButtonText: 'Annulla'
                            }).then((result2) => {
                                if (result2.isConfirmed) {
                                    let form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '/eventi/' + event.id;
                                    form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name=csrf-token]').content + '"><input type="hidden" name="_method" value="DELETE">';
                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            });
                        }
                    });
                }
            });
            calendar.render();
        });
    </script>
    <style>
        #calendar { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 20px; }
        .fc-tooltip { background: #f8f9fa; color: #333; border: 1px solid #ccc; border-radius: 4px; padding: 4px 8px; font-size: 0.95em; margin-top: 4px; }
    </style>
@endsection
