<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $eventi = Event::visibiliPerUtente($userId)->orderBy('inizio')->get();
        return view('eventi.index', compact('eventi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('eventi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'inizio' => 'required|date',
            'fine' => 'nullable|date|after_or_equal:inizio',
        ]);
        $isAdmin = Auth::user()->ruolo === 'admin';
        $userId = $request->has('globale') && $isAdmin ? null : Auth::id();
        Event::create([
            'titolo' => $request->titolo,
            'descrizione' => $request->descrizione,
            'inizio' => $request->inizio,
            'fine' => $request->fine,
            'user_id' => $userId,
        ]);
        return redirect()->route('eventi.index')->with('success', 'Evento creato con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $evento = Event::findOrFail($id);
        // Solo l'utente proprietario o admin può vedere l'evento privato
        if ($evento->user_id !== null && $evento->user_id !== Auth::id() && Auth::user()->ruolo !== 'admin') {
            abort(403);
        }
        return view('eventi.show', compact('evento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $evento = Event::findOrFail($id);
        if ($evento->user_id !== null && $evento->user_id !== Auth::id() && Auth::user()->ruolo !== 'admin') {
            abort(403);
        }
        return view('eventi.edit', compact('evento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $evento = Event::findOrFail($id);
        if ($evento->user_id !== null && $evento->user_id !== Auth::id() && Auth::user()->ruolo !== 'admin') {
            abort(403);
        }
        $request->validate([
            'titolo' => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'inizio' => 'required|date',
            'fine' => 'nullable|date|after_or_equal:inizio',
        ]);
        $evento->update([
            'titolo' => $request->titolo,
            'descrizione' => $request->descrizione,
            'inizio' => $request->inizio,
            'fine' => $request->fine,
        ]);
        return redirect()->route('eventi.index')->with('success', 'Evento aggiornato con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $evento = Event::findOrFail($id);
        if ($evento->user_id !== null && $evento->user_id !== Auth::id() && Auth::user()->ruolo !== 'admin') {
            abort(403);
        }
        $evento->delete();
        return redirect()->route('eventi.index')->with('success', 'Evento eliminato con successo');
    }
}
