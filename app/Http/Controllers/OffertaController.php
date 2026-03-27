<?php

namespace App\Http\Controllers;

use App\Models\Offerta;
use App\Models\OffertaRiga;
use App\Models\Cliente;
use App\Models\Commessa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OffertaController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Offerta::with(['cliente', 'commessa'])->orderByDesc('data');

        if ($request->filled('stato'))     $query->where('stato', $request->stato);
        if ($request->filled('cliente_id')) $query->where('cliente_id', $request->cliente_id);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sq) use ($q) {
                $sq->where('numero', 'like', "%$q%")
                    ->orWhereHas('cliente', fn($c) => $c->where('ragione_sociale', 'like', "%$q%"));
            });
        }

        $offerte  = $query->get();
        $clienti  = Cliente::orderBy('ragione_sociale')->get();
        return view('offerte.index', compact('offerte', 'clienti'));
    }

    // ══════════════════════════════════════════
    //  CREATE / STORE
    // ══════════════════════════════════════════

    public function create()
    {
        $clienti = Cliente::orderBy('ragione_sociale')->get();
        $numero  = Offerta::prossimoNumero();
        return view('offerte.create', compact('clienti', 'numero'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero'        => 'required|string|unique:offerte',
            'data'          => 'required|date',
            'validita_fino' => 'nullable|date|after_or_equal:data',
            'cliente_id'    => 'required|exists:clienti,id',
            'attenzione'    => 'nullable|string|max:255',
            'riferimento'   => 'nullable|string|max:255',
            'consegna'      => 'nullable|string|max:255',
            'pagamento'     => 'nullable|string|max:255',
            'commessa_rif'  => 'nullable|string|max:255',
            'stato'         => 'required|in:bozza,inviata,accettata,rifiutata',
            'note'          => 'nullable|string',
            'note_interne'  => 'nullable|string',
            // Righe
            'righe'                       => 'required|array|min:1',
            'righe.*.descrizione'         => 'required|string',
            'righe.*.um'                  => 'required|string|max:20',
            'righe.*.quantita'            => 'required|numeric|min:0',
            'righe.*.prezzo_unitario'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($data, $request) {
            $offerta = Offerta::create(collect($data)->except('righe')->toArray());

            foreach ($data['righe'] as $i => $riga) {
                $offerta->righe()->create([
                    'ordine'          => $i,
                    'descrizione'     => $riga['descrizione'],
                    'um'              => $riga['um'],
                    'quantita'        => $riga['quantita'],
                    'prezzo_unitario' => $riga['prezzo_unitario'],
                ]);
            }

            // Upload allegati
            if ($request->hasFile('allegati')) {
                foreach ($request->file('allegati') as $file) {
                    $offerta->addMedia($file)
                        ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        ->toMediaCollection('allegati');
                }
            }

            // Se già accettata in creazione → crea commessa
            if ($data['stato'] === 'accettata') {
                $this->creaCommessa($offerta);
            }
        });

        return redirect()->route('offerte.index')->with('success', 'Offerta creata.');
    }

    // ══════════════════════════════════════════
    //  SHOW / EDIT / UPDATE
    // ══════════════════════════════════════════

    public function show(Offerta $offerta)
    {
        $offerta->load(['cliente', 'righe', 'commessa']);
        $allegati = $offerta->getMedia('allegati');
        return view('offerte.show', compact('offerta', 'allegati'));
    }

    public function edit(Offerta $offerta)
    {
        $clienti  = Cliente::orderBy('ragione_sociale')->get();
        $allegati = $offerta->getMedia('allegati');
        $offerta->load('righe');
        return view('offerte.edit', compact('offerta', 'clienti', 'allegati'));
    }

    public function update(Request $request, Offerta $offerta)
    {
        $data = $request->validate([
            'data'          => 'required|date',
            'validita_fino' => 'nullable|date|after_or_equal:data',
            'cliente_id'    => 'required|exists:clienti,id',
            'attenzione'    => 'nullable|string|max:255',
            'riferimento'   => 'nullable|string|max:255',
            'consegna'      => 'nullable|string|max:255',
            'pagamento'     => 'nullable|string|max:255',
            'commessa_rif'  => 'nullable|string|max:255',
            'stato'         => 'required|in:bozza,inviata,accettata,rifiutata',
            'note'          => 'nullable|string',
            'note_interne'  => 'nullable|string',
            'righe'                   => 'required|array|min:1',
            'righe.*.descrizione'     => 'required|string',
            'righe.*.um'              => 'required|string|max:20',
            'righe.*.quantita'        => 'required|numeric|min:0',
            'righe.*.prezzo_unitario' => 'required|numeric|min:0',
        ]);

        $statoVecchio = $offerta->stato;

        DB::transaction(function() use ($offerta, $data, $request, $statoVecchio) {
            $offerta->update(collect($data)->except('righe')->toArray());

            // Riscrive tutte le righe
            $offerta->righe()->delete();
            foreach ($data['righe'] as $i => $riga) {
                $offerta->righe()->create([
                    'ordine'          => $i,
                    'descrizione'     => $riga['descrizione'],
                    'um'              => $riga['um'],
                    'quantita'        => $riga['quantita'],
                    'prezzo_unitario' => $riga['prezzo_unitario'],
                ]);
            }

            if ($request->hasFile('allegati')) {
                foreach ($request->file('allegati') as $file) {
                    $offerta->addMedia($file)
                        ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        ->toMediaCollection('allegati');
                }
            }

            // Passaggio a "accettata" → crea commessa se non esiste già
            if ($data['stato'] === 'accettata' && $statoVecchio !== 'accettata' && !$offerta->commessa_id) {
                $this->creaCommessa($offerta);
            }
        });

        return redirect()->route('offerte.show', $offerta)->with('success', 'Offerta aggiornata.');
    }

    public function destroy(Offerta $offerta)
    {
        $offerta->delete();
        return redirect()->route('offerte.index')->with('success', 'Offerta eliminata.');
    }

    // ══════════════════════════════════════════
    //  ACCETTA → COMMESSA (action rapida)
    // ══════════════════════════════════════════

    public function accetta(Offerta $offerta)
    {
        if ($offerta->stato === 'accettata') {
            return back()->with('info', 'Offerta già accettata.');
        }

        DB::transaction(function() use ($offerta) {
            $offerta->update(['stato' => 'accettata']);
            if (!$offerta->commessa_id) {
                $this->creaCommessa($offerta);
            }
        });

        return redirect()->route('offerte.show', $offerta)
            ->with('success', 'Offerta accettata. Commessa creata automaticamente.');
    }

    // ══════════════════════════════════════════
    //  MEDIA — cancellazione allegato
    // ══════════════════════════════════════════

    public function destroyMedia(Offerta $offerta, int $mediaId)
    {
        $offerta->media()->findOrFail($mediaId)->delete();
        return back()->with('success', 'Allegato eliminato.');
    }

    // ══════════════════════════════════════════
    //  GENERA WORD
    // ══════════════════════════════════════════

    public function downloadWord(Offerta $offerta)
    {
        ini_set('memory_limit', '512M');

        $offerta->load(['cliente', 'righe']);

        $templatePath = resource_path('templates/template_offerta.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template offerta non trovato. Caricare il file in resources/templates/template_offerta.docx');
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);
        } catch (\Exception $e) {
            abort(500, 'Errore caricamento template: ' . $e->getMessage());
        }

        $cliente = $offerta->cliente;

        // ── Placeholder intestazione ──────────────────────────────
        $templateProcessor->setValue('numero',          $offerta->numero ?? '-');
        $templateProcessor->setValue('data',            $offerta->data ? $offerta->data->format('d/m/Y') : '-');
        $templateProcessor->setValue('validita_fino',   $offerta->validita_fino ? $offerta->validita_fino->format('d/m/Y') : '60 giorni dalla data');
        $templateProcessor->setValue('consegna',        $offerta->consegna ?? '-');
        $templateProcessor->setValue('pagamento',       $offerta->pagamento ?? '-');
        $templateProcessor->setValue('commessa_rif',    $offerta->commessa_rif ?? '-');
        $templateProcessor->setValue('attenzione',      $offerta->attenzione ?? '-');
        $templateProcessor->setValue('riferimento',     $offerta->riferimento ?? '-');
        $templateProcessor->setValue('note',            $offerta->note ?? '');

        // ── Placeholder cliente ───────────────────────────────────
        $templateProcessor->setValue('cliente_ragione_sociale', $cliente->ragione_sociale ?? '-');
        $templateProcessor->setValue('cliente_indirizzo',       $cliente->indirizzo ?? '-');
        $templateProcessor->setValue('cliente_cap_citta',       trim(
            ($cliente->cap ?? '') . ' ' .
            ($cliente->citta ?? '') . ' ' .
            ($cliente->provincia ? '(' . $cliente->provincia . ')' : '')
        ) ?: '-');
        $templateProcessor->setValue('cliente_piva',    $cliente->partita_iva ?? '-');
        $templateProcessor->setValue('cliente_email',   $cliente->email ?? '-');

        // ── Righe voci (cloneRow) ─────────────────────────────────
        // Nel template Word devi avere una riga tabella con questi placeholder:
        // ${riga_descrizione} | ${riga_um} | ${riga_quantita} | ${riga_prezzo} | ${riga_totale}
        // TemplateProcessor la clonerà tante volte quante sono le righe

        $righe = $offerta->righe;

        // cloneRow cerca la riga che contiene '${riga_descrizione}' e la duplica N volte
        $templateProcessor->cloneRow('riga_descrizione', $righe->count());

        foreach ($righe as $i => $riga) {
            $n = $i + 1; // cloneRow usa indice 1-based
            $templateProcessor->setValue("riga_descrizione#$n",  $riga->descrizione);
            $templateProcessor->setValue("riga_um#$n",           $riga->um);
            $templateProcessor->setValue("riga_quantita#$n",     number_format($riga->quantita, 2, ',', '.'));
            $templateProcessor->setValue("riga_prezzo#$n",       '€ ' . number_format($riga->prezzo_unitario, 2, ',', '.'));
            $templateProcessor->setValue("riga_totale#$n",       '€ ' . number_format($riga->totale_riga, 2, ',', '.'));
        }

        // ── Totale imponibile ─────────────────────────────────────
        $templateProcessor->setValue('totale_imponibile', '€ ' . number_format($offerta->totale, 2, ',', '.'));

        // ── Salva e scarica ───────────────────────────────────────
        $tmpPath = storage_path('app/temp_offerta_' . $offerta->id . '.docx');

        try {
            $templateProcessor->saveAs($tmpPath);
        } catch (\Exception $e) {
            abort(500, 'Errore salvataggio Word: ' . $e->getMessage());
        }

        $filename = 'Offerta_' . $offerta->numero . '_' . str_replace(' ', '_', $cliente->ragione_sociale) . '.docx';

        return response()->download($tmpPath, $filename)->deleteFileAfterSend(true);
    }


    // ══════════════════════════════════════════
    //  HELPER PRIVATO
    // ══════════════════════════════════════════

    private function creaCommessa(Offerta $offerta): void
    {
        $codice = 'C-' . $offerta->numero; // es: C-OFF-2025-001

        $commessa = Commessa::create([
            'codice'      => $codice,
            'descrizione' => 'Da offerta ' . $offerta->numero . ' – ' . ($offerta->riferimento ?? $offerta->cliente->ragione_sociale),
            'cliente_id'  => $offerta->cliente_id,
            'data_inizio' => now()->toDateString(),
            'stato'       => 'aperta',
        ]);

        $offerta->update(['commessa_id' => $commessa->id]);
    }
}
