<?php

namespace App\Http\Controllers;

use App\Models\Commessa;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CommessaController extends Controller
{
    public function index()
    {
        $commesse = Commessa::with('cliente')->get();
        return view('commesse.index', compact('commesse'));
    }

    public function create()
    {
        $clienti = Cliente::all();
        return view('commesse.create', compact('clienti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codice' => 'required|string|max:50|unique:commesse',
            'descrizione' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clienti,id',
            'data_inizio' => 'nullable|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'stato' => 'required|in:aperta,in_lavorazione,chiusa',
        ]);

        Commessa::create($request->all());

        return redirect()->route('commesse.index')->with('success', 'Commessa creata con successo.');
    }

    public function show(Commessa $commessa)
    {
        return view('commesse.show', compact('commessa'));
    }

    public function edit(Commessa $commessa)
    {
        $clienti = Cliente::all();
        return view('commesse.edit', compact('commessa', 'clienti'));
    }

    public function update(Request $request, Commessa $commessa)
    {
        $request->validate([
            'descrizione' => 'required|string|max:255',
            'cliente_id' => 'required|exists:clienti,id',
            'data_inizio' => 'nullable|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'stato' => 'required|in:aperta,in_lavorazione,chiusa',
        ]);

        $commessa->update($request->all());

        return redirect()->route('commesse.index')->with('success', 'Commessa aggiornata con successo.');
    }

    public function destroy(Commessa $commessa)
    {
        $commessa->delete();
        return redirect()->route('commesse.index')->with('success', 'Commessa eliminata con successo.');
    }
}
