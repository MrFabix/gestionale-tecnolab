<?php

namespace App\Http\Controllers;

use App\Models\Personale;
use App\Models\PersonaleFormazione;
use App\Models\User;
use Illuminate\Http\Request;

class PersonaleController extends Controller
{
    // ══════════════════════════════════════════
    //  PERSONALE — CRUD
    // ══════════════════════════════════════════

    public function index()
    {
        $personale = Personale::with(['user', 'formazioni'])->orderBy('cognome')->get();
        return view('personale.index', compact('personale'));
    }

    public function create()
    {
        $utenti = User::orderBy('name')->get();
        return view('personale.create', compact('utenti'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'               => 'required|string|max:100',
            'cognome'            => 'required|string|max:100',
            'codice_fiscale'     => 'nullable|string|max:16|unique:personale',
            'data_nascita'       => 'nullable|date',
            'luogo_nascita'      => 'nullable|string|max:100',
            'indirizzo'          => 'nullable|string|max:255',
            'telefono'           => 'nullable|string|max:30',
            'email'              => 'nullable|email|max:150',
            'qualifica'          => 'nullable|string|max:150',
            'reparto'            => 'nullable|string|max:100',
            'data_assunzione'    => 'nullable|date',
            'data_fine_rapporto' => 'nullable|date|after_or_equal:data_assunzione',
            'stato'              => 'required|in:attivo,in_ferie,dimesso,sospeso',
            'user_id'            => 'nullable|exists:users,id',
            'note'               => 'nullable|string',
            'foto'               => 'nullable|image|max:3072',
            'documenti.*'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $p = Personale::create($data);

        if ($request->hasFile('foto')) {
            $p->addMedia($request->file('foto'))
                ->toMediaCollection('foto');
        }

        if ($request->hasFile('documenti')) {
            foreach ($request->file('documenti') as $doc) {
                $p->addMedia($doc)
                    ->usingName(pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME))
                    ->withCustomProperties(['nome_originale' => $doc->getClientOriginalName()])
                    ->toMediaCollection('documenti');
            }
        }

        return redirect()->route('personale.show', $p)
            ->with('success', 'Dipendente aggiunto con successo.');
    }

    public function show(Personale $personale)
    {
        $personale->load(['user', 'formazioni']);
        $documenti = $personale->getMedia('documenti');
        return view('personale.show', compact('personale', 'documenti'));
    }

    public function edit(Personale $personale)
    {
        $utenti    = User::orderBy('name')->get();
        $documenti = $personale->getMedia('documenti');
        return view('personale.edit', compact('personale', 'utenti', 'documenti'));
    }

    public function update(Request $request, Personale $personale)
    {
        $data = $request->validate([
            'nome'               => 'required|string|max:100',
            'cognome'            => 'required|string|max:100',
            'codice_fiscale'     => 'nullable|string|max:16|unique:personale,codice_fiscale,'.$personale->id,
            'data_nascita'       => 'nullable|date',
            'luogo_nascita'      => 'nullable|string|max:100',
            'indirizzo'          => 'nullable|string|max:255',
            'telefono'           => 'nullable|string|max:30',
            'email'              => 'nullable|email|max:150',
            'qualifica'          => 'nullable|string|max:150',
            'reparto'            => 'nullable|string|max:100',
            'data_assunzione'    => 'nullable|date',
            'data_fine_rapporto' => 'nullable|date|after_or_equal:data_assunzione',
            'stato'              => 'required|in:attivo,in_ferie,dimesso,sospeso',
            'user_id'            => 'nullable|exists:users,id',
            'note'               => 'nullable|string',
            'foto'               => 'nullable|image|max:3072',
            'documenti.*'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $personale->update($data);

        if ($request->hasFile('foto')) {
            $personale->addMedia($request->file('foto'))
                ->toMediaCollection('foto'); // singleFile: sostituisce automaticamente
        }

        if ($request->hasFile('documenti')) {
            foreach ($request->file('documenti') as $doc) {
                $personale->addMedia($doc)
                    ->usingName(pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME))
                    ->withCustomProperties(['nome_originale' => $doc->getClientOriginalName()])
                    ->toMediaCollection('documenti');
            }
        }

        return redirect()->route('personale.show', $personale)
            ->with('success', 'Dati aggiornati.');
    }

    public function destroy(Personale $personale)
    {
        $personale->delete();
        return redirect()->route('personale.index')
            ->with('success', 'Dipendente eliminato.');
    }

    // ══════════════════════════════════════════
    //  MEDIA — cancellazione singolo documento
    // ══════════════════════════════════════════

    public function destroyMedia(Personale $personale, int $mediaId)
    {
        $personale->media()->findOrFail($mediaId)->delete();
        return back()->with('success', 'Documento eliminato.');
    }

    // ══════════════════════════════════════════
    //  FORMAZIONE
    // ══════════════════════════════════════════

    public function storeFormazione(Request $request, Personale $personale)
    {
        $data = $request->validate([
            'titolo'              => 'required|string|max:255',
            'ente_erogatore'      => 'nullable|string|max:255',
            'data_conseguimento'  => 'required|date',
            'data_scadenza'       => 'nullable|date|after_or_equal:data_conseguimento',
            'note'                => 'nullable|string',
            'attestato'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $f = $personale->formazioni()->create($data);

        if ($request->hasFile('attestato')) {
            $f->addMedia($request->file('attestato'))
                ->usingName('Attestato – ' . $data['titolo'])
                ->toMediaCollection('attestato');
        }

        return back()->with('success', 'Formazione registrata.');
    }

    public function updateFormazione(Request $request, Personale $personale, PersonaleFormazione $formazione)
    {
        $data = $request->validate([
            'titolo'              => 'required|string|max:255',
            'ente_erogatore'      => 'nullable|string|max:255',
            'data_conseguimento'  => 'required|date',
            'data_scadenza'       => 'nullable|date|after_or_equal:data_conseguimento',
            'note'                => 'nullable|string',
            'attestato'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $formazione->update($data);

        if ($request->hasFile('attestato')) {
            $formazione->clearMediaCollection('attestato');
            $formazione->addMedia($request->file('attestato'))
                ->usingName('Attestato – ' . $data['titolo'])
                ->toMediaCollection('attestato');
        }

        return back()->with('success', 'Formazione aggiornata.');
    }

    public function destroyFormazione(Personale $personale, PersonaleFormazione $formazione)
    {
        $formazione->delete();
        return back()->with('success', 'Formazione eliminata.');
    }
}
