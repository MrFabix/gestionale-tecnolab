<?php

namespace App\Http\Controllers;

use App\Models\Cartella;
use App\Models\Documento;
use App\Models\DocumentoRevisione;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    /** Root del filesystem documenti */
    public function index()
    {
        $cartelle  = Cartella::whereNull('parent_id')->orderBy('nome')->get();
        $documenti = Documento::whereNull('cartella_id')->with(['ultimaRevisione', 'ultimaRevisione.media'])->orderBy('titolo')->get();
        return view('documenti.index', compact('cartelle', 'documenti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titolo'            => 'required|string|max:255',
            'descrizione'       => 'nullable|string',
            'stato'             => 'required|in:bozza,attivo,obsoleto',
            'cartella_id'       => 'nullable|exists:cartelle,id',
            'numero_revisione'  => 'required|integer|min:0',
            'data_revisione'    => 'required|date',
            'redatto_da'        => 'nullable|string|max:255',
            'motivo'            => 'nullable|string',
            'note_rev'          => 'nullable|string',
            'file'              => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:51200',
        ]);

        $documento = Documento::create($request->only('titolo', 'descrizione', 'stato', 'cartella_id'));

        $revisione = $documento->revisioni()->create([
            'numero_revisione' => $request->numero_revisione,
            'data_revisione'   => $request->data_revisione,
            'redatto_da'       => $request->redatto_da,
            'motivo'           => $request->motivo,
            'note'             => $request->note_rev,
        ]);

        if ($request->hasFile('file')) {
            $revisione->addMedia($request->file('file'))
                ->usingName($documento->titolo . ' — Rev. ' . $request->numero_revisione)
                ->toMediaCollection('file');
        }

        return redirect()->route('documenti.show', $documento)->with('success', 'Documento creato.');
    }

    public function show(Documento $documento)
    {
        $documento->load(['revisioni', 'revisioni.media', 'cartella']);
        return view('documenti.show', compact('documento'));
    }

    public function edit(Documento $documento)
    {
        $cartelle = Cartella::orderBy('nome')->get();
        return view('documenti.edit', compact('documento', 'cartelle'));
    }

    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'titolo'      => 'required|string|max:255',
            'descrizione' => 'nullable|string',
            'stato'       => 'required|in:bozza,attivo,obsoleto',
            'cartella_id' => 'nullable|exists:cartelle,id',
        ]);

        $documento->update($request->only('titolo', 'descrizione', 'stato', 'cartella_id'));

        return redirect()->route('documenti.show', $documento)->with('success', 'Documento aggiornato.');
    }

    public function destroy(Documento $documento)
    {
        $cartellaId = $documento->cartella_id;
        $documento->delete();

        if ($cartellaId) {
            return redirect()->route('cartelle.show', $cartellaId)->with('success', 'Documento eliminato.');
        }
        return redirect()->route('documenti.index')->with('success', 'Documento eliminato.');
    }

    // ── REVISIONI ────────────────────────────────────────

    public function storeRevisione(Request $request, Documento $documento)
    {
        $request->validate([
            'numero_revisione' => 'required|integer|min:0',
            'data_revisione'   => 'required|date',
            'redatto_da'       => 'nullable|string|max:255',
            'motivo'           => 'nullable|string',
            'note'             => 'nullable|string',
            'file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:51200',
        ]);

        $revisione = $documento->revisioni()->create([
            'numero_revisione' => $request->numero_revisione,
            'data_revisione'   => $request->data_revisione,
            'redatto_da'       => $request->redatto_da,
            'motivo'           => $request->motivo,
            'note'             => $request->note,
        ]);

        if ($request->hasFile('file')) {
            $revisione->addMedia($request->file('file'))
                ->usingName($documento->titolo . ' — Rev. ' . $request->numero_revisione)
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
