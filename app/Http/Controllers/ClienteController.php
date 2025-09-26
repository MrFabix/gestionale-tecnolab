<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clienti = Cliente::all();
        return view('clienti.index', compact('clienti'));
    }

    public function create()
    {
        return view('clienti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ragione_sociale' => 'required|string|max:255',
            'email' => 'nullable|email'
        ]);

        Cliente::create($request->all());

        return redirect()->route('clienti.index')->with('success', 'Cliente creato con successo.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clienti.edit', compact('cliente'));
    }



    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'ragione_sociale' => 'required|string|max:255',
            'email' => 'nullable|email'
        ]);

        $cliente->update($request->all());

        return redirect()->route('clienti.index')->with('success', 'Cliente aggiornato con successo.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clienti.index')->with('success', 'Cliente eliminato.');
    }

    public function show(Cliente $cliente)
    {
        return view('clienti.show', compact('cliente'));
    }

}
