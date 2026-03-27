<?php

namespace App\Http\Controllers;

use App\Models\Attrezzatura;
use App\Models\AttrezzaturaTaratura;
use App\Models\AttrezzaturaManutenzione;
use Illuminate\Http\Request;

class AttrezzaturaController extends Controller
{
    // ══════════════════════════════════════════
    //  ATTREZZATURE — CRUD
    // ══════════════════════════════════════════

    public function index()
    {
        $attrezzature = Attrezzatura::with(['tarature', 'manutenzioni'])->orderBy('nome')->get();
        return view('attrezzature.index', compact('attrezzature'));
    }

    public function create()
    {
        return view('attrezzature.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'       => 'required|string|max:255',
            'codice'     => 'nullable|string|max:100|unique:attrezzature',
            'marca'      => 'nullable|string|max:100',
            'modello'    => 'nullable|string|max:100',
            'matricola'  => 'nullable|string|max:100',
            'ubicazione' => 'nullable|string|max:255',
            'stato'      => 'required|in:attivo,in_manutenzione,dismesso',
            'note'       => 'nullable|string',
            'immagini.*' => 'nullable|image|max:5120', // max 5MB cad.
            'documenti.*'=> 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $attrezzatura = Attrezzatura::create($data);

        // Upload immagini (multiple)
        if ($request->hasFile('immagini')) {
            foreach ($request->file('immagini') as $img) {
                $attrezzatura->addMedia($img)
                    ->usingName(pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME))
                    ->toMediaCollection('immagini');
            }
        }

        // Upload documenti (multiple)
        if ($request->hasFile('documenti')) {
            foreach ($request->file('documenti') as $doc) {
                $attrezzatura->addMedia($doc)
                    ->usingName(pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME))
                    ->toMediaCollection('documenti');
            }
        }

        return redirect()->route('attrezzature.show', $attrezzatura)
            ->with('success', 'Attrezzatura creata con successo.');
    }

    public function show(Attrezzatura $attrezzatura)
    {
        $attrezzatura->load(['tarature', 'manutenzioni']);
        $immagini  = $attrezzatura->getMedia('immagini');
        $documenti = $attrezzatura->getMedia('documenti');
        return view('attrezzature.show', compact('attrezzatura', 'immagini', 'documenti'));
    }

    public function edit(Attrezzatura $attrezzatura)
    {
        $immagini  = $attrezzatura->getMedia('immagini');
        $documenti = $attrezzatura->getMedia('documenti');
        return view('attrezzature.edit', compact('attrezzatura', 'immagini', 'documenti'));
    }

    public function update(Request $request, Attrezzatura $attrezzatura)
    {
        $data = $request->validate([
            'nome'       => 'required|string|max:255',
            'codice'     => 'nullable|string|max:100|unique:attrezzature,codice,' . $attrezzatura->id,
            'marca'      => 'nullable|string|max:100',
            'modello'    => 'nullable|string|max:100',
            'matricola'  => 'nullable|string|max:100',
            'ubicazione' => 'nullable|string|max:255',
            'stato'      => 'required|in:attivo,in_manutenzione,dismesso',
            'note'       => 'nullable|string',
            'immagini.*' => 'nullable|image|max:5120',
            'documenti.*'=> 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $attrezzatura->update($data);

        if ($request->hasFile('immagini')) {
            foreach ($request->file('immagini') as $img) {
                $attrezzatura->addMedia($img)
                    ->usingName(pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME))
                    ->toMediaCollection('immagini');
            }
        }

        if ($request->hasFile('documenti')) {
            foreach ($request->file('documenti') as $doc) {
                $attrezzatura->addMedia($doc)
                    ->usingName(pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME))
                    ->toMediaCollection('documenti');
            }
        }

        return redirect()->route('attrezzature.show', $attrezzatura)
            ->with('success', 'Attrezzatura aggiornata.');
    }

    public function destroy(Attrezzatura $attrezzatura)
    {
        $attrezzatura->delete(); // Spatie cancella i file automaticamente
        return redirect()->route('attrezzature.index')
            ->with('success', 'Attrezzatura eliminata.');
    }

    // ══════════════════════════════════════════
    //  MEDIA — cancellazione singolo file
    // ══════════════════════════════════════════

    public function destroyMedia(Attrezzatura $attrezzatura, int $mediaId)
    {
        $media = $attrezzatura->media()->findOrFail($mediaId);
        $media->delete();
        return back()->with('success', 'File eliminato.');
    }

    // ══════════════════════════════════════════
    //  TARATURE
    // ══════════════════════════════════════════

    public function storeTaratura(Request $request, Attrezzatura $attrezzatura)
    {
        $data = $request->validate([
            'data_taratura'  => 'required|date',
            'data_scadenza'  => 'nullable|date|after_or_equal:data_taratura',
            'eseguita_da'    => 'nullable|string|max:255',
            'note'           => 'nullable|string',
            'certificato'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $taratura = $attrezzatura->tarature()->create($data);

        if ($request->hasFile('certificato')) {
            $taratura->addMedia($request->file('certificato'))
                ->usingName('Certificato taratura ' . $data['data_taratura'])
                ->toMediaCollection('certificato');
        }

        return back()->with('success', 'Taratura registrata.');
    }

    public function destroyTaratura(Attrezzatura $attrezzatura, AttrezzaturaTaratura $taratura)
    {
        $taratura->delete();
        return back()->with('success', 'Taratura eliminata.');
    }

    // ══════════════════════════════════════════
    //  MANUTENZIONI
    // ══════════════════════════════════════════

    public function storeManutenzione(Request $request, Attrezzatura $attrezzatura)
    {
        $data = $request->validate([
            'data_intervento'  => 'required|date',
            'prossima_scadenza'=> 'nullable|date|after_or_equal:data_intervento',
            'tipo'             => 'required|in:ordinaria,straordinaria',
            'eseguita_da'      => 'nullable|string|max:255',
            'descrizione'      => 'nullable|string',
            'documento'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $manutenzione = $attrezzatura->manutenzioni()->create($data);

        if ($request->hasFile('documento')) {
            $manutenzione->addMedia($request->file('documento'))
                ->usingName('Rapporto manutenzione ' . $data['data_intervento'])
                ->toMediaCollection('documento');
        }

        return back()->with('success', 'Manutenzione registrata.');
    }

    public function destroyManutenzione(Attrezzatura $attrezzatura, AttrezzaturaManutenzione $manutenzione)
    {
        $manutenzione->delete();
        return back()->with('success', 'Manutenzione eliminata.');
    }
}
