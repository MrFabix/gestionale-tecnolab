<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Commessa;
use App\Models\Offerta;
use App\Models\AttrezzaturaTaratura;
use App\Models\AttrezzaturaManutenzione;
use App\Models\PersonaleFormazione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EventController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX — vista calendario principale
    // ══════════════════════════════════════════

    public function index()
    {
        return view('eventi.index');
    }

    // ══════════════════════════════════════════
    //  JSON FEED — FullCalendar lo chiama via AJAX
    //  Restituisce eventi manuali + scadenze sistema
    // ══════════════════════════════════════════

    public function feed(Request $request)
    {
        $dal = $request->get('start') ? Carbon::parse($request->get('start')) : now()->startOfMonth();
        $al  = $request->get('end')   ? Carbon::parse($request->get('end'))   : now()->endOfMonth();

        $eventi = [];

        // ── 1. EVENTI MANUALI dal DB ──────────────────────────
        $manuali = Event::where(function ($q) use ($dal, $al) {
                $q->whereBetween('inizio', [$dal, $al])
                    ->orWhereBetween('fine', [$dal, $al])
                    ->orWhere(fn($q2) => $q2->where('inizio', '<=', $dal)->where('fine', '>=', $al));
            })
            ->get();

        foreach ($manuali as $e) {
            $eventi[] = [
                'id'              => 'ev_' . $e->id,
                'title'           => $e->titolo,
                'start'           => $e->inizio,
                'end'             => $e->fine,
                'color'           => $e->colore ?? '#3b82f6',
                'extendedProps'   => [
                    'tipo'        => 'manuale',
                    'descrizione' => $e->descrizione,
                    'globale'     => $e->user_id === null,
                    'db_id'       => $e->id,
                ],
            ];
        }

        // ── 2. SCADENZE TARATURE ──────────────────────────────
        AttrezzaturaTaratura::whereNotNull('data_scadenza')
            ->whereBetween('data_scadenza', [$dal, $al])
            ->with('attrezzatura')
            ->get()
            ->each(function ($t) use (&$eventi) {
                $scad = $t->data_scadenza->isPast();
                $eventi[] = [
                    'id'    => 'tar_' . $t->id,
                    'title' => '⚙ Taratura: ' . $t->attrezzatura->nome,
                    'start' => $t->data_scadenza->format('Y-m-d'),
                    'allDay'=> true,
                    'color' => $scad ? '#dc2626' : '#f59e0b',
                    'extendedProps' => [
                        'tipo'        => 'taratura',
                        'descrizione' => ($scad ? '⚠ SCADUTA — ' : 'Scadenza taratura — ') . $t->attrezzatura->nome,
                        'link'        => route('attrezzature.show', $t->attrezzatura_id),
                    ],
                ];
            });

        // ── 3. SCADENZE MANUTENZIONI ──────────────────────────
        AttrezzaturaManutenzione::whereNotNull('prossima_scadenza')
            ->whereBetween('prossima_scadenza', [$dal, $al])
            ->with('attrezzatura')
            ->get()
            ->each(function ($m) use (&$eventi) {
                $scad = $m->prossima_scadenza->isPast();
                $eventi[] = [
                    'id'    => 'man_' . $m->id,
                    'title' => '🔧 Manut.: ' . $m->attrezzatura->nome,
                    'start' => $m->prossima_scadenza->format('Y-m-d'),
                    'allDay'=> true,
                    'color' => $scad ? '#dc2626' : '#8b5cf6',
                    'extendedProps' => [
                        'tipo'        => 'manutenzione',
                        'descrizione' => ($scad ? '⚠ SCADUTA — ' : 'Manutenzione: ') . ($m->tipo ?? '') . ' — ' . $m->attrezzatura->nome,
                        'link'        => route('attrezzature.show', $m->attrezzatura_id),
                    ],
                ];
            });

        // ── 4. SCADENZE FORMAZIONE PERSONALE ─────────────────
        PersonaleFormazione::whereNotNull('data_scadenza')
            ->whereBetween('data_scadenza', [$dal, $al])
            ->with('personale')
            ->get()
            ->each(function ($f) use (&$eventi) {
                $scad = $f->data_scadenza->isPast();
                $eventi[] = [
                    'id'    => 'form_' . $f->id,
                    'title' => '🎓 ' . $f->personale->cognome . ': ' . \Str::limit($f->titolo, 30),
                    'start' => $f->data_scadenza->format('Y-m-d'),
                    'allDay'=> true,
                    'color' => $scad ? '#dc2626' : '#0891b2',
                    'extendedProps' => [
                        'tipo'        => 'formazione',
                        'descrizione' => ($scad ? '⚠ SCADUTA — ' : 'Formazione: ') . $f->titolo . ' — ' . $f->personale->cognome . ' ' . $f->personale->nome,
                        'link'        => route('personale.show', $f->personale_id),
                    ],
                ];
            });

        // ── 5. SCADENZE OFFERTE ───────────────────────────────
        Offerta::whereNotNull('validita_fino')
            ->whereNotIn('stato', ['accettata', 'rifiutata'])
            ->whereBetween('validita_fino', [$dal, $al])
            ->with('cliente')
            ->get()
            ->each(function ($o) use (&$eventi) {
                $scad = $o->validita_fino->isPast();
                $eventi[] = [
                    'id'    => 'off_' . $o->id,
                    'title' => '📄 Offerta: ' . $o->numero,
                    'start' => $o->validita_fino->format('Y-m-d'),
                    'allDay'=> true,
                    'color' => $scad ? '#6b7280' : '#10b981',
                    'extendedProps' => [
                        'tipo'        => 'offerta',
                        'descrizione' => 'Validità offerta ' . $o->numero . ' — ' . $o->cliente->ragione_sociale,
                        'link'        => route('offerte.show', $o->id),
                    ],
                ];
            });

        // ── 6. COMMESSE CON DATA FINE ─────────────────────────
        Commessa::whereNotNull('data_fine')
            ->where('stato', '!=', 'chiusa')
            ->whereBetween('data_fine', [$dal, $al])
            ->with('cliente')
            ->get()
            ->each(function ($c) use (&$eventi) {
                $eventi[] = [
                    'id'    => 'comm_' . $c->id,
                    'title' => '📋 ' . $c->codice,
                    'start' => $c->data_fine->format('Y-m-d'),
                    'allDay'=> true,
                    'color' => '#f97316',
                    'extendedProps' => [
                        'tipo'        => 'commessa',
                        'descrizione' => 'Fine commessa ' . $c->codice . ' — ' . $c->cliente->ragione_sociale,
                        'link'        => route('commesse.show', $c->id),
                    ],
                ];
            });

        return response()->json($eventi);
    }

    // ══════════════════════════════════════════
    //  STORE — crea evento manuale
    // ══════════════════════════════════════════

    public function store(Request $request)
    {
        $data = $request->validate([
            'titolo'      => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'inizio'      => 'required|date',
            'fine'        => 'nullable|date|after_or_equal:inizio',
            'colore'      => 'nullable|string|max:20',
        ]);

        $evento = Event::create([
            'titolo'      => $data['titolo'],
            'descrizione' => $data['descrizione'] ?? null,
            'inizio'      => $data['inizio'],
            'fine'        => $data['fine'] ?? null,
            'colore'      => $data['colore'] ?? '#3b82f6',
            'tipo'        => 'manuale',
            'user_id'     => null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $evento->id]);
        }

        return redirect()->route('eventi.index')->with('success', 'Evento creato.');
    }

    // ══════════════════════════════════════════
    //  UPDATE — modifica evento manuale
    // ══════════════════════════════════════════

    public function update(Request $request, string $id)
    {
        $evento = Event::findOrFail($id);

        $data = $request->validate([
            'titolo'      => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'inizio'      => 'required|date',
            'fine'        => 'nullable|date|after_or_equal:inizio',
            'colore'      => 'nullable|string|max:20',
        ]);

        $evento->update($data);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('eventi.index')->with('success', 'Evento aggiornato.');
    }

    // ══════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════

    public function destroy(string $id)
    {
        $evento = Event::findOrFail($id);
        $evento->delete();

        return response()->json(['ok' => true]);
    }

    // ══════════════════════════════════════════
    //  HELPER
    // ══════════════════════════════════════════

    private function autorizza(Event $evento): void
    {
        if ($evento->user_id !== null
            && $evento->user_id !== Auth::id()
            && Auth::user()->ruolo !== 'admin') {
            abort(403);
        }
    }

    // Metodi show/create/edit non usati (tutto è modale inline)
    public function show(string $id)   { return redirect()->route('eventi.index'); }
    public function create()           { return redirect()->route('eventi.index'); }
    public function edit(string $id)   { return redirect()->route('eventi.index'); }
}
