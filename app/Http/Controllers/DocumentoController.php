<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoRevisione;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documenti = Documento::with('ultimaRevisione')->orderBy('titolo')->get();
        return view('documenti.index', compact('documenti'));
    }

    public function create()
    {
        return view('documenti.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titolo'      => 'required|string|max:255',
            'categoria'   => 'nullable|string|max:255',
            'descrizione' => 'nullable|string',
            'stato'       => 'required|in:bozza,attivo,obsoleto',
        ]);

        $documento = Documento::create($data);

        return redirect()->route('documenti.show', $documento)->with('success', 'Documento creato.');
    }

    public function show(Documento $documento)
    {
        $documento->load('revisioni');
        return view('documenti.show', compact('documento'));
    }

    public function edit(Documento $documento)
    {
        return view('documenti.edit', compact('documento'));
    }

    public function update(Request $request, Documento $documento)
    {
        $data = $request->validate([
            'titolo'      => 'required|string|max:255',
            'categoria'   => 'nullable|string|max:255',
            'descrizione' => 'nullable|string',
            'stato'       => 'required|in:bozza,attivo,obsoleto',
        ]);

        $documento->update($data);

        return redirect()->route('documenti.show', $documento)->with('success', 'Documento aggiornato.');
    }

    public function destroy(Documento $documento)
    {
        $documento->delete();
        return redirect()->route('documenti.index')->with('success', 'Documento eliminato.');
    }

    // ── REVISIONI ────────────────────────────────────────

    public function storeRevisione(Request $request, Documento $documento)
    {
        $request->validate([
            'data_revisione' => 'required|date',
            'redatto_da'     => 'nullable|string|max:255',
            'motivo'         => 'nullable|string',
            'note'           => 'nullable|string',
            'file'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
        ]);

        $numRev = $documento->revisioni()->max('numero_revisione') ?? -1;

        $revisione = $documento->revisioni()->create([
            'numero_revisione' => $numRev + 1,
            'data_revisione'   => $request->data_revisione,
            'redatto_da'       => $request->redatto_da,
            'motivo'           => $request->motivo,
            'note'             => $request->note,
        ]);

        if ($request->hasFile('file')) {
            $revisione->addMedia($request->file('file'))
                ->usingName($documento->titolo . ' — Rev. ' . $revisione->numero_revisione)
                ->toMediaCollection('file');
        }

        return back()->with('success', 'Revisione aggiunta.');
    }

    public function destroyRevisione(Documento $documento, DocumentoRevisione $revisione)
    {
        $revisione->delete();
        return back()->with('success', 'Revisione eliminata.');
    }
}
