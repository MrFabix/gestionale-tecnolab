<?php

namespace App\Http\Controllers;

use App\Models\Impostazione;
use App\Services\FornitoreQualificaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpostazioneController extends Controller
{
    public function index()
    {
        $mesiInattivita = (int) Impostazione::get('fornitori_inattivita_mesi', 12);
        $squalificaAttiva = (bool) Impostazione::get('fornitori_squalifica_automatica_attiva', true);
        $statoDopoInattivita = Impostazione::get('fornitori_stato_dopo_inattivita', 'non_qualificato');

        return view('impostazioni.index', compact(
            'mesiInattivita',
            'squalificaAttiva',
            'statoDopoInattivita'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'fornitori_inattivita_mesi'           => 'required|integer|min:1|max:120',
            'fornitori_squalifica_automatica_attiva' => 'nullable|boolean',
            'fornitori_stato_dopo_inattivita'     => 'required|in:non_qualificato,sospeso',
        ]);

        Impostazione::set(
            'fornitori_inattivita_mesi',
            (int) $validated['fornitori_inattivita_mesi'],
            'integer',
            'fornitori',
            'Mesi di inattività per squalifica automatica',
            'Numero di mesi senza ordini o acquisti trascorsi i quali un fornitore perde la qualifica.'
        );

        Impostazione::set(
            'fornitori_squalifica_automatica_attiva',
            $request->has('fornitori_squalifica_automatica_attiva'),
            'boolean',
            'fornitori',
            'Squalifica automatica per inattività attiva',
            'Se abilitato, il gestionale imposta automaticamente lo stato a non qualificato se non vi sono acquisti entro il periodo.'
        );

        Impostazione::set(
            'fornitori_stato_dopo_inattivita',
            $validated['fornitori_stato_dopo_inattivita'],
            'string',
            'fornitori',
            'Nuovo stato da applicare per inattività',
            'Stato assegnato al fornitore inattivo.'
        );

        return redirect()->route('impostazioni.index')
            ->with('success', 'Impostazioni globali aggiornate con successo.');
    }

    public function verificaInattivitaFornitori(FornitoreQualificaService $service)
    {
        $res = $service->verificaTuttiFornitori(Auth::id());

        if (!$res['attiva']) {
            return redirect()->back()->with('warning', 'La verifica automatica è disattivata nelle impostazioni.');
        }

        if ($res['totale_squalificati'] > 0) {
            $nomi = collect($res['squalificati'])->pluck('ragione_sociale')->implode(', ');
            return redirect()->back()->with('success', "Verifica completata: {$res['totale_squalificati']} fornitore/i squalificato/i per inattività oltre {$res['mesi_soglia']} mesi ({$nomi}).");
        }

        return redirect()->back()->with('success', "Verifica completata: tutti i {$res['totale_verificati']} fornitori qualificati risultano attivi entro la soglia di {$res['mesi_soglia']} mesi.");
    }
}
