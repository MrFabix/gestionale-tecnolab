<?php

namespace App\Http\Controllers;

use App\Models\Oda;
use App\Models\OdaRiga;
use App\Models\Rda;
use App\Models\RdaRiga;
use App\Models\Fornitore;
use App\Models\Commessa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;

class OdaController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX — Elenco Ordini di Acquisto (ODA)
    // ══════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Oda::with(['fornitore', 'rda', 'commessa', 'righe'])->orderByDesc('id');

        // Ricerca testo (numero ODA, fornitore, rif offerta, descrizione bene)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('numero', 'like', "%{$q}%")
                   ->orWhere('riferimento_offerta', 'like', "%{$q}%")
                   ->orWhere('riferimento_interno', 'like', "%{$q}%")
                   ->orWhere('note_aggiuntive', 'like', "%{$q}%")
                   ->orWhereHas('fornitore', fn($fq) => $fq->where('ragione_sociale', 'like', "%{$q}%")->orWhere('codice', 'like', "%{$q}%"))
                   ->orWhereHas('commessa', fn($cq) => $cq->where('codice', 'like', "%{$q}%"))
                   ->orWhereHas('righe', fn($rq) => $rq->where('descrizione_bene', 'like', "%{$q}%")->orWhere('codice_articolo', 'like', "%{$q}%"));
            });
        }

        // Filtro stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro fornitore
        if ($request->filled('fornitore_id')) {
            $query->where('fornitore_id', $request->fornitore_id);
        }

        // Filtro RDA di origine
        if ($request->filled('rda_id')) {
            $query->where('rda_id', $request->rda_id);
        }

        $odaList = $query->paginate(15)->withQueryString();

        // Statistiche rapide
        $totaleOda       = Oda::count();
        $inviatiCount    = Oda::where('stato', 'inviato')->count();
        $consegnatiCount = Oda::where('stato', 'consegnato')->count();
        $totaleSpesa     = Oda::where('stato', '!=', 'annullato')->sum('totale_imponibile');

        $fornitori = Fornitore::orderBy('ragione_sociale')->get();

        return view('oda.index', compact(
            'odaList',
            'totaleOda',
            'inviatiCount',
            'consegnatiCount',
            'totaleSpesa',
            'fornitori'
        ));
    }

    // ══════════════════════════════════════════
    //  CREATE — Creazione Singolo ODA
    // ══════════════════════════════════════════

    public function create(Request $request)
    {
        $prossimoNumero = Oda::prossimoNumero();
        $fornitori      = Fornitore::orderBy('ragione_sociale')->get();
        $commesse       = Commessa::where('stato', '!=', 'chiusa')->orderByDesc('id')->get();
        $defaultInterno = Auth::user()?->name ?? 'Ufficio Acquisti';

        // Eventuale precompilazione da RDA
        $rda = null;
        $prefilledRighe = [];
        $selectedFornitoreId = $request->query('fornitore_id');

        if ($request->filled('rda_id')) {
            $rda = Rda::with(['righe.fornitore'])->find($request->query('rda_id'));
            if ($rda) {
                // Includi TUTTI gli articoli della RDA, indipendentemente dal fornitore consigliato
                foreach ($rda->righe as $r) {
                    $prefilledRighe[] = [
                        'rda_riga_id'      => $r->id,
                        'descrizione_bene' => $r->descrizione_prodotto,
                        'codice_articolo'  => $r->codice_prodotto,
                        'unita_misura'     => $r->unita_misura ?: 'PZ',
                        'quantita'         => $r->quantita ?: 1,
                        'costo_unitario'   => 0,
                        'totale_riga'      => 0,
                        'note'             => $r->note,
                    ];
                }

                // Se non è stato passato un fornitore specifico ma la prima riga ne ha uno, suggeriscilo
                if (!$selectedFornitoreId && $rda->righe->first()?->fornitore_id) {
                    $selectedFornitoreId = $rda->righe->first()->fornitore_id;
                }
            }
        }

        $fornitoriList = $fornitori->map(fn($f) => [
            'id' => $f->id,
            'nome' => $f->ragione_sociale . ($f->codice ? " ({$f->codice})" : ''),
        ])->values()->all();

        return view('oda.create', compact(
            'prossimoNumero',
            'fornitori',
            'fornitoriList',
            'commesse',
            'defaultInterno',
            'rda',
            'prefilledRighe',
            'selectedFornitoreId'
        ));
    }

    // ══════════════════════════════════════════
    //  STORE — Salvataggio ODA
    // ══════════════════════════════════════════

    public function store(Request $request)
    {
        $request->validate([
            'numero'                 => 'required|string|max:50|unique:oda,numero',
            'data_ordine'            => 'required|date',
            'fornitore_id'           => 'required|exists:fornitori,id',
            'rda_id'                 => 'nullable|exists:rda,id',
            'commessa_id'            => 'nullable|exists:commesse,id',
            'destinazione'           => 'required|string|max:255',
            'data_consegna_prevista' => 'nullable|date',
            'resa'                   => 'required|string|max:100',
            'trasporto'              => 'required|string|max:100',
            'imballo'                => 'required|string|max:100',
            'condizioni_pagamento'   => 'required|string|max:150',
            'riferimento_offerta'    => 'nullable|string|max:255',
            'riferimento_interno'    => 'nullable|string|max:150',
            'aliquota_iva'           => 'required|numeric|min:0|max:100',
            'stato'                  => 'required|in:bozza,inviato,confermato,consegnato,annullato',
            'note_aggiuntive'        => 'nullable|string',
            'righe'                  => 'required|array|min:1',
            'righe.*.descrizione_bene'=> 'required|string',
            'righe.*.quantita'       => 'required|numeric|min:0.01',
            'righe.*.costo_unitario' => 'required|numeric|min:0',
        ], [
            'righe.required'                 => 'Devi inserire almeno un bene o servizio da ordinare.',
            'righe.*.descrizione_bene.required' => 'La descrizione è obbligatoria per ogni riga.',
            'righe.*.quantita.required'      => 'La quantità è obbligatoria per ogni riga.',
            'righe.*.costo_unitario.required'=> 'Il costo unitario è obbligatorio.',
        ]);

        DB::transaction(function () use ($request, &$oda) {
            $oda = Oda::create([
                'numero'                 => $request->numero,
                'data_ordine'            => $request->data_ordine,
                'fornitore_id'           => $request->fornitore_id,
                'rda_id'                 => $request->rda_id,
                'commessa_id'            => $request->commessa_id,
                'user_id'                => Auth::id(),
                'destinazione'           => $request->destinazione,
                'data_consegna_prevista' => $request->data_consegna_prevista,
                'resa'                   => $request->resa,
                'trasporto'              => $request->trasporto,
                'imballo'                => $request->imballo,
                'condizioni_pagamento'   => $request->condizioni_pagamento,
                'riferimento_offerta'    => $request->riferimento_offerta,
                'riferimento_interno'    => $request->riferimento_interno,
                'aliquota_iva'           => $request->aliquota_iva,
                'stato'                  => $request->stato,
                'note_aggiuntive'        => $request->note_aggiuntive,
            ]);

            foreach ($request->righe as $index => $riga) {
                $qta = (float) $riga['quantita'];
                $costo = (float) $riga['costo_unitario'];
                $totaleRiga = round($qta * $costo, 2);

                $oda->righe()->create([
                    'rda_riga_id'      => !empty($riga['rda_riga_id']) ? $riga['rda_riga_id'] : null,
                    'descrizione_bene' => $riga['descrizione_bene'],
                    'codice_articolo'  => $riga['codice_articolo'] ?? null,
                    'unita_misura'     => $riga['unita_misura'] ?? 'PZ',
                    'quantita'         => $qta,
                    'costo_unitario'   => $costo,
                    'totale_riga'      => $totaleRiga,
                    'ordine'           => $index + 1,
                    'note'             => $riga['note'] ?? null,
                ]);
            }

            // Calcola totali
            $oda->ricalcolaTotali();

            // Aggiorna automaticamente la scheda fornitore (Tabella 2 MD PG04/04-00)
            $oda->sincronizzaSchedaFornitore();

            // Se collegata a una RDA, aggiorna lo stato dell'RDA a 'ordinata'
            if ($oda->rda_id) {
                $oda->rda->update(['stato' => 'ordinata']);
            }
        });

        return redirect()->route('oda.show', $oda)
            ->with('success', "Ordine di Acquisto {$oda->numero} emesso con successo e registrato nella scheda fornitore.");
    }

    // ══════════════════════════════════════════
    //  WIZARD: TRASFORMAZIONE DIRETTA RDA → ODA
    // ══════════════════════════════════════════

    public function wizardDaRda(Rda $rda)
    {
        $rda->load(['righe.fornitore']);

        // Raggruppa le righe per fornitore
        $fornitoriGruppi = [];
        $righeSenzaFornitore = [];

        foreach ($rda->righe as $riga) {
            if ($riga->fornitore_id && $riga->fornitore) {
                $fId = $riga->fornitore_id;
                if (!isset($fornitoriGruppi[$fId])) {
                    $fornitoriGruppi[$fId] = [
                        'fornitore' => $riga->fornitore,
                        'righe'     => [],
                    ];
                }
                $fornitoriGruppi[$fId]['righe'][] = $riga;
            } else {
                $righeSenzaFornitore[] = $riga;
            }
        }

        $tuttiFornitori = Fornitore::orderBy('ragione_sociale')->get();

        return view('oda.wizard-da-rda', compact(
            'rda',
            'fornitoriGruppi',
            'righeSenzaFornitore',
            'tuttiFornitori'
        ));
    }

    // ══════════════════════════════════════════
    //  STORE MULTIPLO: GENERA ODA MULTIPLI DA RDA
    // ══════════════════════════════════════════

    public function storeDaRdaMultipli(Request $request, Rda $rda)
    {
        $rda->load('righe.fornitore');
        $gruppiSelezionati = $request->input('gruppi', []);

        if (empty($gruppiSelezionati)) {
            return back()->with('error', 'Nessun fornitore selezionato per la generazione degli ODA.');
        }

        $ordiniCreati = [];

        DB::transaction(function () use ($rda, $gruppiSelezionati, $request, &$ordiniCreati) {
            foreach ($gruppiSelezionati as $fornitoreId => $datiGruppo) {
                if (empty($datiGruppo['attivo'])) {
                    continue;
                }

                $righeIds = $datiGruppo['righe_ids'] ?? [];
                if (empty($righeIds)) {
                    continue;
                }

                $fornitore = Fornitore::find($fornitoreId);
                if (!$fornitore) {
                    continue;
                }

                $numeroOda = Oda::prossimoNumero();

                $oda = Oda::create([
                    'numero'                 => $numeroOda,
                    'data_ordine'            => now()->toDateString(),
                    'fornitore_id'           => $fornitore->id,
                    'rda_id'                 => $rda->id,
                    'commessa_id'            => null,
                    'user_id'                => Auth::id(),
                    'destinazione'           => "VIA DELL'INFORMATICA 3  - S. PALOMBA POMEZIA  (RM)",
                    'data_consegna_prevista' => $rda->data_approvvigionamento ? $rda->data_approvvigionamento->toDateString() : null,
                    'resa'                   => 'VS CURA',
                    'trasporto'              => 'VS CURA',
                    'imballo'                => 'VS CURA',
                    'condizioni_pagamento'   => 'BB   30GG FM',
                    'riferimento_offerta'    => 'Richiesta di Acquisto N° ' . $rda->numero,
                    'riferimento_interno'    => $rda->richiedente ?: Auth::user()?->name,
                    'aliquota_iva'           => 22.00,
                    'stato'                  => 'inviato',
                    'note_aggiuntive'        => $rda->note,
                ]);

                $index = 1;
                foreach ($righeIds as $rId) {
                    $rigaRda = $rda->righe->firstWhere('id', $rId);
                    if ($rigaRda) {
                        $costo = !empty($datiGruppo['costi'][$rId]) ? (float) $datiGruppo['costi'][$rId] : 0.00;
                        $qta   = (float) $rigaRda->quantita;
                        $totale = round($qta * $costo, 2);

                        $oda->righe()->create([
                            'rda_riga_id'      => $rigaRda->id,
                            'descrizione_bene' => $rigaRda->descrizione_prodotto,
                            'codice_articolo'  => $rigaRda->codice_prodotto,
                            'unita_misura'     => $rigaRda->unita_misura ?: 'PZ',
                            'quantita'         => $qta,
                            'costo_unitario'   => $costo,
                            'totale_riga'      => $totale,
                            'ordine'           => $index++,
                            'note'             => $rigaRda->note,
                        ]);
                    }
                }

                $oda->ricalcolaTotali();
                $oda->sincronizzaSchedaFornitore();
                $ordiniCreati[] = $oda->numero;
            }

            // Aggiorna lo stato della RDA a "ordinata"
            $rda->update(['stato' => 'ordinata']);
        });

        if (empty($ordiniCreati)) {
            return back()->with('error', 'Nessun ordine è stato generato. Seleziona almeno una riga fornitore.');
        }

        $elencoNumeri = implode(', ', $ordiniCreati);
        return redirect()->route('oda.index')
            ->with('success', "Generati con successo " . count($ordiniCreati) . " Ordini di Acquisto: {$elencoNumeri}. Schede fornitori aggiornate automaticamente.");
    }

    // ══════════════════════════════════════════
    //  STORE SINGOLO: GENERA ODA UNICO CON ARTICOLI A SCELTA DA RDA
    // ══════════════════════════════════════════

    public function storeDaRdaSingolo(Request $request, Rda $rda)
    {
        $request->validate([
            'fornitore_id' => 'required|exists:fornitori,id',
            'righe_ids'    => 'required|array|min:1',
            'righe_ids.*'  => 'exists:rda_righe,id',
        ], [
            'fornitore_id.required' => 'Devi selezionare il Fornitore a cui intestare l\'ordine.',
            'righe_ids.required'    => 'Seleziona almeno un articolo da inserire nell\'ordine.',
        ]);

        $numeroOda = Oda::prossimoNumero();
        $rda->load('righe');

        DB::transaction(function () use ($request, $rda, $numeroOda, &$oda) {
            $oda = Oda::create([
                'numero'                 => $numeroOda,
                'data_ordine'            => $request->input('data_ordine', now()->toDateString()),
                'fornitore_id'           => $request->fornitore_id,
                'rda_id'                 => $rda->id,
                'commessa_id'            => $request->input('commessa_id', null),
                'user_id'                => Auth::id(),
                'destinazione'           => "VIA DELL'INFORMATICA 3  - S. PALOMBA POMEZIA  (RM)",
                'data_consegna_prevista' => $rda->data_approvvigionamento ? $rda->data_approvvigionamento->toDateString() : null,
                'resa'                   => 'VS CURA',
                'trasporto'              => 'VS CURA',
                'imballo'                => 'VS CURA',
                'condizioni_pagamento'   => 'BB   30GG FM',
                'riferimento_offerta'    => 'Richiesta di Acquisto N° ' . $rda->numero,
                'riferimento_interno'    => $rda->richiedente ?: Auth::user()?->name,
                'aliquota_iva'           => 22.00,
                'stato'                  => 'inviato',
                'note_aggiuntive'        => $rda->note,
            ]);

            $index = 1;
            foreach ($request->righe_ids as $rId) {
                $rigaRda = $rda->righe->firstWhere('id', $rId);
                if ($rigaRda) {
                    $costo = !empty($request->costi[$rId]) ? (float) $request->costi[$rId] : 0.00;
                    $qta   = (float) $rigaRda->quantita;
                    $totale = round($qta * $costo, 2);

                    $oda->righe()->create([
                        'rda_riga_id'      => $rigaRda->id,
                        'descrizione_bene' => $rigaRda->descrizione_prodotto,
                        'codice_articolo'  => $rigaRda->codice_prodotto,
                        'unita_misura'     => $rigaRda->unita_misura ?: 'PZ',
                        'quantita'         => $qta,
                        'costo_unitario'   => $costo,
                        'totale_riga'      => $totale,
                        'ordine'           => $index++,
                        'note'             => $rigaRda->note,
                    ]);
                }
            }

            $oda->ricalcolaTotali();
            $oda->sincronizzaSchedaFornitore();

            // Aggiorna lo stato della RDA a "ordinata"
            $rda->update(['stato' => 'ordinata']);
        });

        return redirect()->route('oda.show', $oda)
            ->with('success', "Ordine di Acquisto {$oda->numero} emesso con successo e registrato nella scheda fornitore.");
    }

    // ══════════════════════════════════════════
    //  SHOW — Scheda Dettaglio ODA (MD PG 04/02-00)
    // ══════════════════════════════════════════

    public function show(Oda $oda)
    {
        $oda->load(['fornitore', 'rda', 'commessa', 'user', 'righe', 'fornitura']);
        return view('oda.show', compact('oda'));
    }

    // ══════════════════════════════════════════
    //  EDIT — Modifica ODA
    // ══════════════════════════════════════════

    public function edit(Oda $oda)
    {
        $oda->load(['fornitore', 'rda', 'commessa', 'righe']);
        $fornitori = Fornitore::orderBy('ragione_sociale')->get();
        $commesse  = Commessa::where('stato', '!=', 'chiusa')->orderByDesc('id')->get();

        $fornitoriList = $fornitori->map(fn($f) => [
            'id' => $f->id,
            'nome' => $f->ragione_sociale . ($f->codice ? " ({$f->codice})" : ''),
        ])->values()->all();

        $existingRighe = $oda->righe->map(fn($r) => [
            'rda_riga_id'      => $r->rda_riga_id,
            'descrizione_bene' => $r->descrizione_bene,
            'codice_articolo'  => $r->codice_articolo,
            'unita_misura'     => $r->unita_misura,
            'quantita'         => $r->quantita,
            'costo_unitario'   => $r->costo_unitario,
            'totale_riga'      => $r->totale_riga,
            'note'             => $r->note,
        ])->values()->all();

        return view('oda.edit', compact('oda', 'fornitori', 'fornitoriList', 'commesse', 'existingRighe'));
    }

    // ══════════════════════════════════════════
    //  UPDATE — Aggiornamento ODA e Righe
    // ══════════════════════════════════════════

    public function update(Request $request, Oda $oda)
    {
        $request->validate([
            'numero'                 => 'required|string|max:50|unique:oda,numero,' . $oda->id,
            'data_ordine'            => 'required|date',
            'fornitore_id'           => 'required|exists:fornitori,id',
            'rda_id'                 => 'nullable|exists:rda,id',
            'commessa_id'            => 'nullable|exists:commesse,id',
            'destinazione'           => 'required|string|max:255',
            'data_consegna_prevista' => 'nullable|date',
            'resa'                   => 'required|string|max:100',
            'trasporto'              => 'required|string|max:100',
            'imballo'                => 'required|string|max:100',
            'condizioni_pagamento'   => 'required|string|max:150',
            'riferimento_offerta'    => 'nullable|string|max:255',
            'riferimento_interno'    => 'nullable|string|max:150',
            'aliquota_iva'           => 'required|numeric|min:0|max:100',
            'stato'                  => 'required|in:bozza,inviato,confermato,consegnato,annullato',
            'note_aggiuntive'        => 'nullable|string',
            'righe'                  => 'required|array|min:1',
            'righe.*.descrizione_bene'=> 'required|string',
            'righe.*.quantita'       => 'required|numeric|min:0.01',
            'righe.*.costo_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $oda) {
            $oda->update([
                'numero'                 => $request->numero,
                'data_ordine'            => $request->data_ordine,
                'fornitore_id'           => $request->fornitore_id,
                'rda_id'                 => $request->rda_id,
                'commessa_id'            => $request->commessa_id,
                'destinazione'           => $request->destinazione,
                'data_consegna_prevista' => $request->data_consegna_prevista,
                'resa'                   => $request->resa,
                'trasporto'              => $request->trasporto,
                'imballo'                => $request->imballo,
                'condizioni_pagamento'   => $request->condizioni_pagamento,
                'riferimento_offerta'    => $request->riferimento_offerta,
                'riferimento_interno'    => $request->riferimento_interno,
                'aliquota_iva'           => $request->aliquota_iva,
                'stato'                  => $request->stato,
                'note_aggiuntive'        => $request->note_aggiuntive,
            ]);

            $oda->righe()->delete();

            foreach ($request->righe as $index => $riga) {
                $qta = (float) $riga['quantita'];
                $costo = (float) $riga['costo_unitario'];
                $totaleRiga = round($qta * $costo, 2);

                $oda->righe()->create([
                    'rda_riga_id'      => !empty($riga['rda_riga_id']) ? $riga['rda_riga_id'] : null,
                    'descrizione_bene' => $riga['descrizione_bene'],
                    'codice_articolo'  => $riga['codice_articolo'] ?? null,
                    'unita_misura'     => $riga['unita_misura'] ?? 'PZ',
                    'quantita'         => $qta,
                    'costo_unitario'   => $costo,
                    'totale_riga'      => $totaleRiga,
                    'ordine'           => $index + 1,
                    'note'             => $riga['note'] ?? null,
                ]);
            }

            $oda->ricalcolaTotali();
            $oda->sincronizzaSchedaFornitore();
        });

        return redirect()->route('oda.show', $oda)
            ->with('success', "Ordine di Acquisto {$oda->numero} aggiornato con successo.");
    }

    // ══════════════════════════════════════════
    //  DESTROY — Eliminazione ODA
    // ══════════════════════════════════════════

    public function destroy(Oda $oda)
    {
        $numero = $oda->numero;
        DB::transaction(function () use ($oda) {
            if ($oda->fornitura) {
                $oda->fornitura->delete();
            }
            $oda->delete();
        });

        return redirect()->route('oda.index')
            ->with('success', "Ordine di Acquisto {$numero} eliminato.");
    }

    // ══════════════════════════════════════════
    //  CAMBIO STATO RAPIDO
    // ══════════════════════════════════════════

    public function cambiaStato(Request $request, Oda $oda)
    {
        $request->validate([
            'stato' => 'required|in:bozza,inviato,confermato,consegnato,annullato',
        ]);

        $oda->update(['stato' => $request->stato]);

        // Se consegnato o annullato, aggiorna le note nello storico fornitore
        if ($oda->fornitura) {
            if ($request->stato === 'consegnato') {
                $oda->fornitura->update(['esito' => 'positivo', 'note' => "Fornitura completata con successo - ODA {$oda->numero}"]);
            } elseif ($request->stato === 'annullato') {
                $oda->fornitura->delete();
            }
        }

        return back()->with('success', "Stato dell'Ordine di Acquisto {$oda->numero} aggiornato a " . $oda->stato_label . ".");
    }

    // ══════════════════════════════════════════
    //  DOWNLOAD WORD (MD PG 04/02-00)
    // ══════════════════════════════════════════

    public function downloadDocx(Oda $oda)
    {
        $oda->load(['fornitore', 'righe', 'rda', 'commessa']);

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(9.5);

        // ── PAGINA 1: MODULO ORDINE DI ACQUISTO ──
        $section = $phpWord->addSection([
            'marginTop'    => 800,
            'marginBottom' => 800,
            'marginLeft'   => 1000,
            'marginRight'  => 1000,
        ]);

        // Header Tabella Intestazione Ufficiale
        $tHead = $section->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 60]);
        $tHead->addRow();
        $tHead->addCell(3500)->addText('TECNOLAB S.r.l.', ['bold' => true, 'size' => 12]);
        $tHead->addCell(3500)->addText('ORDINE DI ACQUISTO', ['bold' => true, 'size' => 12, 'color' => 'D90429'], ['alignment' => Jc::CENTER]);
        $tHead->addCell(3000)->addText("MD PG 04/02-00\nPag. 1 di 2", ['bold' => true, 'size' => 8.5], ['alignment' => Jc::END]);

        $section->addTextBreak(1);

        // Tabella Dati Ordine e Fornitore
        $tInfo = $section->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 60]);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('DATA:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->data_ordine ? $oda->data_ordine->format('d/m/Y') : '—', ['bold' => true]);
        $tInfo->addCell(5000, ['gridSpan' => 2, 'bgColor' => 'F9FAFB'])->addText('SPETT.LE FORNITORE:', ['bold' => true, 'size' => 8.5]);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('ORDINE N°:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->numero, ['bold' => true, 'size' => 10, 'color' => 'D90429']);
        $fornTxt = ($oda->fornitore->ragione_sociale ?? '') . "\n" . 
                   ($oda->fornitore->indirizzo_completo ?? '') . "\n" . 
                   "P.I. " . ($oda->fornitore->partita_iva ?? '—') . "   C.F. " . ($oda->fornitore->codice_fiscale ?? '—');
        $tInfo->addCell(5000, ['gridSpan' => 2, 'vMerge' => 'restart'])->addText($fornTxt, ['size' => 9]);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('DESTINAZIONE:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->destinazione, ['size' => 8.5]);
        $tInfo->addCell(5000, ['gridSpan' => 2, 'vMerge' => 'continue']);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('CONSEGNA:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->data_consegna_prevista ? $oda->data_consegna_prevista->format('d/m/Y') : 'A vista', ['bold' => true]);
        $tInfo->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('RESA:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(2500)->addText($oda->resa, ['size' => 8.5]);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('TRASPORTO:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->trasporto, ['size' => 8.5]);
        $tInfo->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('IMBALLO:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(2500)->addText($oda->imballo, ['size' => 8.5]);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('PAGAMENTO:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->condizioni_pagamento, ['bold' => true]);
        $tInfo->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('COD. FORNITORE:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(2500)->addText($oda->fornitore->codice ?? '—', ['bold' => true]);

        $tInfo->addRow();
        $tInfo->addCell(2000, ['bgColor' => 'F2F2F2'])->addText('VS RIF. OFFERTA:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(3000)->addText($oda->riferimento_offerta ?: '—', ['size' => 8.5]);
        $tInfo->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('COMMESSA N°:', ['bold' => true, 'size' => 8.5]);
        $tInfo->addCell(2500)->addText($oda->commessa->codice ?? '—', ['bold' => true]);

        $section->addTextBreak(1);

        // ── TABELLA BENI E SERVIZI (MD PG 04/02-00) ──
        $tRighe = $section->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 60]);

        $tRighe->addRow();
        $tRighe->addCell(5200, ['bgColor' => 'D9D9D9'])->addText('DESCRIZIONE BENI', ['bold' => true, 'size' => 8.5]);
        $tRighe->addCell(1000, ['bgColor' => 'D9D9D9'])->addText('UM', ['bold' => true, 'size' => 8.5], ['alignment' => Jc::CENTER]);
        $tRighe->addCell(1200, ['bgColor' => 'D9D9D9'])->addText('Q.TÀ', ['bold' => true, 'size' => 8.5], ['alignment' => Jc::CENTER]);
        $tRighe->addCell(1300, ['bgColor' => 'D9D9D9'])->addText('COSTO €', ['bold' => true, 'size' => 8.5], ['alignment' => Jc::END]);
        $tRighe->addCell(1300, ['bgColor' => 'D9D9D9'])->addText('TOTALE €', ['bold' => true, 'size' => 8.5], ['alignment' => Jc::END]);

        foreach ($oda->righe as $r) {
            $tRighe->addRow();
            $tRighe->addCell(5200)->addText($r->descrizione_bene . ($r->codice_articolo ? " (Cod. {$r->codice_articolo})" : ''), ['size' => 8.5]);
            $tRighe->addCell(1000)->addText($r->unita_misura, ['size' => 8.5], ['alignment' => Jc::CENTER]);
            $tRighe->addCell(1200)->addText(number_format($r->quantita, 2, ',', ''), ['size' => 8.5], ['alignment' => Jc::CENTER]);
            $tRighe->addCell(1300)->addText(number_format($r->costo_unitario, 2, ',', '.'), ['size' => 8.5], ['alignment' => Jc::END]);
            $tRighe->addCell(1300)->addText(number_format($r->totale_riga, 2, ',', '.'), ['bold' => true, 'size' => 8.5], ['alignment' => Jc::END]);
        }

        // Righe vuote estetiche per uniformità al foglio stampato
        $righeMancanti = max(0, 6 - $oda->righe->count());
        for ($i = 0; $i < $righeMancanti; $i++) {
            $tRighe->addRow();
            $tRighe->addCell(5200)->addText(' ', ['size' => 8.5]);
            $tRighe->addCell(1000)->addText(' ', ['size' => 8.5]);
            $tRighe->addCell(1200)->addText(' ', ['size' => 8.5]);
            $tRighe->addCell(1300)->addText(' ', ['size' => 8.5]);
            $tRighe->addCell(1300)->addText(' ', ['size' => 8.5]);
        }

        // Riquadro Note e Totale Imponibile
        $tTot = $section->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 60]);
        $tTot->addRow();
        $tTot->addCell(7400, ['bgColor' => 'FAFAFA'])->addText("NOTE AGGIUNTIVE:\n" . ($oda->note_aggiuntive ?: '—'), ['size' => 8.5]);
        $tTot->addCell(2600, ['bgColor' => 'F2F2F2'])->addText("TOTALE IMPONIBILE:\n€ " . number_format($oda->totale_imponibile, 2, ',', '.'), ['bold' => true, 'size' => 10, 'color' => 'D90429'], ['alignment' => Jc::END]);

        // ── PAGINA 2: CONDIZIONI GENERALI DI FORNITURA TECNOLAB ──
        $section2 = $phpWord->addSection([
            'marginTop'    => 800,
            'marginBottom' => 800,
            'marginLeft'   => 1000,
            'marginRight'  => 1000,
        ]);

        $tHead2 = $section2->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 60]);
        $tHead2->addRow();
        $tHead2->addCell(3500)->addText('TECNOLAB S.r.l.', ['bold' => true, 'size' => 12]);
        $tHead2->addCell(3500)->addText('ORDINE DI ACQUISTO', ['bold' => true, 'size' => 12, 'color' => 'D90429'], ['alignment' => Jc::CENTER]);
        $tHead2->addCell(3000)->addText("MD PG 04/02-00\nPag. 2 di 2", ['bold' => true, 'size' => 8.5], ['alignment' => Jc::END]);

        $section2->addTextBreak(1);
        $section2->addText('CONDIZIONI GENERALI DI FORNITURA', ['bold' => true, 'size' => 11, 'color' => 'D90429']);

        // Testo condizioni estratto fedelmente da ODA-TL_011_26 - Siad.xlsx
        $condizioni = [
            'INTESTAZIONE E SPEDIZIONE FATTURA' => "Tecnolab srl - Sede Legale: Via Lucullo 8 - 00187 Roma - P.I. e C.F. 17160371005\nSede Operativa: Via dell'Informatica 3, 00071 - Santa Palomba Pomezia (RM) - Tel 06-89279160 - Email info@tecnolabroma.it",
            'LOGISTICA' => "Si prega di avvisare a merce pronta per organizzare ingresso merci.\nPrendere nota dei Ns orari di scarico: Lun. - Ven. 8:00-13:00 / 14:00-16:30.",
            'PAGAMENTI' => "A) Le parti, di comune accordo, convengono che in deroga a quanto previsto nel C.C. ed al D.Lgs 231/02 ed attesi i lunghi tempi di pagamento dell'Amministrazione Appaltante, il pagamento delle prestazioni relative al presente contratto avvenga con le modalità concordate in testata ordine.\nPenale applicabile a fronte di mancata osservanza dei tempi di consegna: 10% dell'ordine.",
            'CESSIONE DI CREDITO E RATIFICA DOCUMENTI' => "B) Non si potrà procedere a cessioni di credito o altre delegazioni al pagamento senza la preventiva ed esplicita autorizzazione della scrivente che se ne riserva la facoltà. Il presente ordine costituisce l'unico documento valido a regolare il rapporto tra le parti. In assenza di riscontro entro 3 gg lavorativi dalla data del presente ordine, lo stesso si intende automaticamente accettato in ogni suo contenuto.",
            'OBBLIGHI DEL FORNITORE' => "C) Riportare su ogni fattura il riferimento dell'Ordine, apporre la clausola di non cedibilità su ogni fattura.\n(D) Le certificazioni richieste sono da intendersi in accompagnamento alla fornitura; la fornitura non verrà considerata completa, quindi non fatturabile, in assenza della suddetta documentazione e del timbro C.Q.T. (Controllo Qualità TecnoLab).",
            'FORO COMPETENTE' => "(E) Per qualsiasi controversia, in diretta ed indiretta connessione con il presente ordine, sarà competente il Foro di Roma."
        ];

        foreach ($condizioni as $titolo => $testo) {
            $section2->addText($titolo, ['bold' => true, 'size' => 8.5]);
            $section2->addText($testo, ['size' => 8, 'color' => '333333']);
            $section2->addTextBreak(0.5);
        }

        $section2->addTextBreak(1);

        // Riquadro Firme (Firma Fornitore, Firma RAL, Firma RLAB)
        $tFirme = $section2->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80]);
        $tFirme->addRow(1000);
        $tFirme->addCell(3333)->addText("Timbro e Firma Fornitore per accettazione:\n\n\n_________________________________", ['size' => 8]);
        $tFirme->addCell(3333)->addText("Firma Responsabile Acquisti / RAL:\n\n\n_________________________________", ['size' => 8]);
        $tFirme->addCell(3334)->addText("Firma Direzione Laboratorio / RLAB:\n\n\n_________________________________", ['size' => 8]);

        // Output download file
        $cleanNum = str_replace(['/', '\\'], '_', $oda->numero);
        $filename = 'ODA_' . $cleanNum . '.docx';
        $tempPath = storage_path('app/temp_' . time() . '_' . $filename);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
