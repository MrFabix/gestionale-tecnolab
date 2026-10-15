<?php

namespace App\Http\Controllers;

use App\Models\Rda;
use App\Models\RdaRiga;
use App\Models\Fornitore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;

class RdaController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX — Elenco Richieste di Acquisto
    // ══════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Rda::with(['righe.fornitore'])->orderByDesc('id');

        // Ricerca testo (numero, richiedente, note, o testo riga)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('numero', 'like', "%{$q}%")
                   ->orWhere('richiedente', 'like', "%{$q}%")
                   ->orWhere('note', 'like', "%{$q}%")
                   ->orWhereHas('righe', function ($rq) use ($q) {
                       $rq->where('descrizione_prodotto', 'like', "%{$q}%")
                          ->orWhere('codice_prodotto', 'like', "%{$q}%")
                          ->orWhere('fornitore_consigliato_testo', 'like', "%{$q}%")
                          ->orWhereHas('fornitore', fn($fq) => $fq->where('ragione_sociale', 'like', "%{$q}%"));
                   });
            });
        }

        // Filtro per stato
        if ($request->filled('stato')) {
            $query->where('stato', $request->stato);
        }

        // Filtro per fornitore
        if ($request->filled('fornitore_id')) {
            $fid = $request->fornitore_id;
            $query->whereHas('righe', fn($rq) => $rq->where('fornitore_id', $fid));
        }

        $rdaList = $query->paginate(15)->withQueryString();

        // Conteggi per statistiche veloci
        $totaliRda      = Rda::count();
        $inAttesaCount  = Rda::where('stato', 'in_attesa')->count();
        $approvateCount = Rda::where('stato', 'approvata')->count();
        $ordinateCount  = Rda::where('stato', 'ordinata')->count();

        $fornitori = Fornitore::orderBy('ragione_sociale')->get();

        return view('rda.index', compact(
            'rdaList',
            'totaliRda',
            'inAttesaCount',
            'approvateCount',
            'ordinateCount',
            'fornitori'
        ));
    }

    // ══════════════════════════════════════════
    //  CREATE — Compilazione Nuova RDA
    // ══════════════════════════════════════════

    public function create()
    {
        $prossimoNumero = Rda::prossimoNumero();
        $fornitori      = Fornitore::orderBy('ragione_sociale')->get();
        $defaultRichiedente = Auth::user()?->name ?? '';

        $fornitoriList = $fornitori->map(fn($f) => [
            'id' => $f->id,
            'nome' => $f->ragione_sociale . ($f->codice ? " ({$f->codice})" : ''),
        ])->values()->all();

        return view('rda.create', compact('prossimoNumero', 'fornitori', 'defaultRichiedente', 'fornitoriList'));
    }

    // ══════════════════════════════════════════
    //  STORE — Salvataggio Nuova RDA con Righe
    // ══════════════════════════════════════════

    public function store(Request $request)
    {
        $request->validate([
            'numero'                             => 'required|string|max:50|unique:rda,numero',
            'richiedente'                        => 'required|string|max:255',
            'data_richiesta'                     => 'required|date',
            'data_approvvigionamento'            => 'nullable|date',
            'stato'                              => 'required|in:bozza,in_attesa,approvata,ordinata,rifiutata',
            'note'                               => 'nullable|string',
            'righe'                              => 'required|array|min:1',
            'righe.*.descrizione_prodotto'       => 'required|string',
            'righe.*.codice_prodotto'            => 'nullable|string|max:100',
            'righe.*.quantita'                   => 'required|numeric|min:0.01',
            'righe.*.unita_misura'               => 'nullable|string|max:20',
            'righe.*.fornitore_id'               => 'nullable|exists:fornitori,id',
            'righe.*.fornitore_consigliato_testo'=> 'nullable|string|max:255',
        ], [
            'righe.required'                     => 'Devi inserire almeno una riga prodotto/servizio.',
            'righe.*.descrizione_prodotto.required' => 'La descrizione prodotto è obbligatoria per ogni riga.',
            'righe.*.quantita.required'          => 'La quantità è obbligatoria per ogni riga.',
        ]);

        DB::transaction(function () use ($request, &$rda) {
            $rda = Rda::create([
                'numero'                  => $request->numero,
                'richiedente'             => $request->richiedente,
                'user_id'                 => Auth::id(),
                'data_richiesta'          => $request->data_richiesta,
                'data_approvvigionamento' => $request->data_approvvigionamento,
                'stato'                   => $request->stato,
                'note'                    => $request->note,
            ]);

            foreach ($request->righe as $index => $riga) {
                $rda->righe()->create([
                    'descrizione_prodotto'        => $riga['descrizione_prodotto'],
                    'codice_prodotto'             => $riga['codice_prodotto'] ?? null,
                    'quantita'                    => $riga['quantita'],
                    'unita_misura'                => $riga['unita_misura'] ?? 'pz',
                    'fornitore_id'                => !empty($riga['fornitore_id']) ? $riga['fornitore_id'] : null,
                    'fornitore_consigliato_testo' => $riga['fornitore_consigliato_testo'] ?? null,
                    'ordine'                      => $index + 1,
                    'note'                        => $riga['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('rda.show', $rda)
            ->with('success', "Richiesta di Acquisto {$rda->numero} creata con successo.");
    }

    // ══════════════════════════════════════════
    //  SHOW — Visualizzazione RDA (MD PG04/01-00)
    // ══════════════════════════════════════════

    public function show(Rda $rda)
    {
        $rda->load(['righe.fornitore', 'user']);
        return view('rda.show', compact('rda'));
    }

    // ══════════════════════════════════════════
    //  EDIT — Modifica RDA
    // ══════════════════════════════════════════

    public function edit(Rda $rda)
    {
        $rda->load(['righe.fornitore']);
        $fornitori = Fornitore::orderBy('ragione_sociale')->get();

        $fornitoriList = $fornitori->map(fn($f) => [
            'id' => $f->id,
            'nome' => $f->ragione_sociale . ($f->codice ? " ({$f->codice})" : ''),
        ])->values()->all();

        $existingRighe = $rda->righe->map(fn($r) => [
            'descrizione_prodotto' => $r->descrizione_prodotto,
            'codice_prodotto' => $r->codice_prodotto,
            'quantita' => $r->quantita,
            'unita_misura' => $r->unita_misura,
            'fornitore_id' => $r->fornitore_id,
            'fornitore_consigliato_testo' => $r->fornitore_consigliato_testo,
            'note' => $r->note,
        ])->values()->all();

        return view('rda.edit', compact('rda', 'fornitori', 'fornitoriList', 'existingRighe'));
    }

    // ══════════════════════════════════════════
    //  UPDATE — Aggiornamento RDA e Righe
    // ══════════════════════════════════════════

    public function update(Request $request, Rda $rda)
    {
        $request->validate([
            'numero'                             => 'required|string|max:50|unique:rda,numero,' . $rda->id,
            'richiedente'                        => 'required|string|max:255',
            'data_richiesta'                     => 'required|date',
            'data_approvvigionamento'            => 'nullable|date',
            'stato'                              => 'required|in:bozza,in_attesa,approvata,ordinata,rifiutata',
            'note'                               => 'nullable|string',
            'righe'                              => 'required|array|min:1',
            'righe.*.descrizione_prodotto'       => 'required|string',
            'righe.*.codice_prodotto'            => 'nullable|string|max:100',
            'righe.*.quantita'                   => 'required|numeric|min:0.01',
            'righe.*.unita_misura'               => 'nullable|string|max:20',
            'righe.*.fornitore_id'               => 'nullable|exists:fornitori,id',
            'righe.*.fornitore_consigliato_testo'=> 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $rda) {
            $rda->update([
                'numero'                  => $request->numero,
                'richiedente'             => $request->richiedente,
                'data_richiesta'          => $request->data_richiesta,
                'data_approvvigionamento' => $request->data_approvvigionamento,
                'stato'                   => $request->stato,
                'note'                    => $request->note,
            ]);

            // Sincronizza righe: elimina vecchie e inserisci nuove
            $rda->righe()->delete();

            foreach ($request->righe as $index => $riga) {
                $rda->righe()->create([
                    'descrizione_prodotto'        => $riga['descrizione_prodotto'],
                    'codice_prodotto'             => $riga['codice_prodotto'] ?? null,
                    'quantita'                    => $riga['quantita'],
                    'unita_misura'                => $riga['unita_misura'] ?? 'pz',
                    'fornitore_id'                => !empty($riga['fornitore_id']) ? $riga['fornitore_id'] : null,
                    'fornitore_consigliato_testo' => $riga['fornitore_consigliato_testo'] ?? null,
                    'ordine'                      => $index + 1,
                    'note'                        => $riga['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('rda.show', $rda)
            ->with('success', "Richiesta di Acquisto {$rda->numero} aggiornata.");
    }

    // ══════════════════════════════════════════
    //  DESTROY — Eliminazione RDA
    // ══════════════════════════════════════════

    public function destroy(Rda $rda)
    {
        $numero = $rda->numero;
        $rda->delete();

        return redirect()->route('rda.index')
            ->with('success', "Richiesta di Acquisto {$numero} eliminata.");
    }

    // ══════════════════════════════════════════
    //  CAMBIA STATO RAPIDO
    // ══════════════════════════════════════════

    public function cambiaStato(Request $request, Rda $rda)
    {
        $request->validate([
            'stato' => 'required|in:bozza,in_attesa,approvata,ordinata,rifiutata',
        ]);

        $rda->update(['stato' => $request->stato]);

        return back()->with('success', "Stato della RDA {$rda->numero} aggiornato a " . ucfirst($request->stato) . ".");
    }

    // ══════════════════════════════════════════
    //  DOWNLOAD WORD (MD PG04/01-00)
    // ══════════════════════════════════════════

    public function downloadWord(Rda $rda)
    {
        $rda->load('righe.fornitore');

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'marginTop'    => 1000,
            'marginBottom' => 1000,
            'marginLeft'   => 1200,
            'marginRight'  => 1200,
        ]);

        // Header Documento MD PG04/01-00
        $header = $section->addHeader();
        $headerTable = $header->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '999999']);
        $headerTable->addRow();
        $headerTable->addCell(5000)->addText('TECNOLAB S.r.l.', ['bold' => true, 'size' => 11]);
        $headerTable->addCell(5000)->addText('MDPG 04/01-00    RICHIESTA DI ACQUISTO    n° RDA : ' . $rda->numero, ['bold' => true, 'size' => 10], ['alignment' => Jc::END]);

        // Titolo
        $section->addText('RICHIESTA DI ACQUISTO (RDA)', ['bold' => true, 'size' => 15, 'color' => 'D90429'], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        // ── TABELLA 1: RICHIEDENTE & DATE ──
        $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80, 'alignment' => JcTable::CENTER];
        $phpWord->addTableStyle('RdaTableStyle', $tableStyle);

        $t1 = $section->addTable('RdaTableStyle');
        $t1->addRow();
        $t1->addCell(4000, ['bgColor' => 'F2F2F2'])->addText('Richiedente:', ['bold' => true]);
        $t1->addCell(3000, ['bgColor' => 'F2F2F2'])->addText('Data richiesta:', ['bold' => true]);
        $t1->addCell(3000, ['bgColor' => 'F2F2F2'])->addText('Data approvvigionamento:', ['bold' => true]);

        $t1->addRow();
        $t1->addCell(4000)->addText($rda->richiedente ?: '—');
        $t1->addCell(3000)->addText($rda->data_richiesta ? $rda->data_richiesta->format('d/m/Y') : '—');
        $t1->addCell(3000)->addText($rda->data_approvvigionamento ? $rda->data_approvvigionamento->format('d/m/Y') : '—');

        $section->addTextBreak(1);

        // ── TABELLA 2: RIGHE PRODOTTI / SERVIZI ──
        $section->addText('PRODOTTI / SERVIZI RICHIESTI', ['bold' => true, 'size' => 11, 'color' => '333333']);
        $t2 = $section->addTable('RdaTableStyle');

        $t2->addRow();
        $t2->addCell(4200, ['bgColor' => 'D9D9D9'])->addText('Descrizione prodotto', ['bold' => true, 'size' => 9]);
        $t2->addCell(1800, ['bgColor' => 'D9D9D9'])->addText('Codice prodotto', ['bold' => true, 'size' => 9], ['alignment' => Jc::CENTER]);
        $t2->addCell(1400, ['bgColor' => 'D9D9D9'])->addText('Quantità', ['bold' => true, 'size' => 9], ['alignment' => Jc::CENTER]);
        $t2->addCell(2600, ['bgColor' => 'D9D9D9'])->addText('Fornitore Consigliato:', ['bold' => true, 'size' => 9]);

        foreach ($rda->righe as $r) {
            $t2->addRow();
            $t2->addCell(4200)->addText($r->descrizione_prodotto, ['size' => 9]);
            $t2->addCell(1800)->addText($r->codice_prodotto ?: '—', ['size' => 9], ['alignment' => Jc::CENTER]);
            $t2->addCell(1400)->addText(number_format($r->quantita, 2, ',', '') . ' ' . ($r->unita_misura ?: 'pz'), ['size' => 9], ['alignment' => Jc::CENTER]);
            $t2->addCell(2600)->addText($r->fornitore_nome, ['size' => 9]);
        }

        // Se ci sono meno di 10 righe, aggiungi righe vuote per riprodurre il layout del modello cartaceo/Word
        $righeMancanti = max(0, 10 - $rda->righe->count());
        for ($i = 0; $i < $righeMancanti; $i++) {
            $t2->addRow();
            $t2->addCell(4200)->addText(' ', ['size' => 9]);
            $t2->addCell(1800)->addText(' ', ['size' => 9]);
            $t2->addCell(1400)->addText(' ', ['size' => 9]);
            $t2->addCell(2600)->addText(' ', ['size' => 9]);
        }

        $section->addTextBreak(1);

        // Note in calce
        $section->addText('Note:', ['bold' => true, 'size' => 10]);
        $section->addText($rda->note ?: '____________________________________________________________________________________________________________________________________________________', ['italic' => true, 'size' => 9]);

        // Output download
        $cleanNum = str_replace(['/', '\\'], '_', $rda->numero);
        $filename = 'RDA_' . $cleanNum . '.docx';
        $tempPath = storage_path('app/temp_' . time() . '_' . $filename);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
