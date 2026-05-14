<?php

namespace App\Http\Controllers;

use App\Models\Cartella;
use Illuminate\Http\Request;

class CartellaController extends Controller
{
    public function show(Cartella $cartella)
    {
        $cartella->load(['sottocartelle', 'documenti.ultimaRevisione', 'documenti.ultimaRevisione.media']);
        return view('documenti.cartella', compact('cartella'));
    }

    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|string|max:255', 'parent_id' => 'nullable|exists:cartelle,id']);
        Cartella::create($request->only('nome', 'parent_id'));

        if ($request->parent_id) {
            return redirect()->route('cartelle.show', $request->parent_id)->with('success', 'Cartella creata.');
        }
        return redirect()->route('documenti.index')->with('success', 'Cartella creata.');
    }

    public function update(Request $request, Cartella $cartella)
    {
        $request->validate(['nome' => 'required|string|max:255']);
        $cartella->update(['nome' => $request->nome]);

        return back()->with('success', 'Cartella rinominata.');
    }

    public function destroy(Cartella $cartella)
    {
        $parentId = $cartella->parent_id;
        $cartella->delete();

        if ($parentId) {
            return redirect()->route('cartelle.show', $parentId)->with('success', 'Cartella eliminata.');
        }
        return redirect()->route('documenti.index')->with('success', 'Cartella eliminata.');
    }
}
