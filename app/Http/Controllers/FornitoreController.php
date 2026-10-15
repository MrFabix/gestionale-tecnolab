<?php

namespace App\Http\Controllers;

use App\Models\Fornitore;
use App\Models\FornitoreFornitura;
use App\Services\FornitoreQualificaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;

class FornitoreController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX — Lista Fornitori
    // ══════════════════════════════════════════

    public function index(Request $request, FornitoreQualificaService $qualificaService)
    {
        // Esegui la verifica inattività per mantenere sincronizzati gli stati
        $qualificaService->verificaTuttiFornitori();

        $query = Fornitore::with('forniture')->orderBy('ragione_sociale');

        // Filtro ricerca testo
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('ragione_sociale', 'like', "%{$q}%")
                   ->orWhere('codice', 'like', "%{$q}%")
                   ->orWhere('partita_iva', 'like', "%{$q}%")
                   ->orWhere('citta', 'like', "%{$q}%")
                   ->orWhere('prodotti_acquistati', 'like', "%{$q}%");
            });
        }

        // Filtro stato qualifica
        if ($request->filled('stato')) {
            $query->where('stato_qualifica', $request->stato);
        }

        $fornitori = $query->paginate(15)->withQueryString();

        // Statistiche rapide
        $totaliFornitori     = Fornitore::count();
        $qualificatiCount    = Fornitore::where('stato_qualifica', 'qualificato')->count();
        $inValutazioneCount  = Fornitore::where('stato_qualifica', 'in_valutazione')->count();
        $conNonConformita    = Fornitore::whereHas('forniture', function ($q) {
            $q->where(function ($sub) {
                $sub->whereNotNull('numero_reclamo')->where('numero_reclamo', '!=', '-')
                    ->orWhere(function ($rnc) {
                        $rnc->whereNotNull('numero_rnc')->where('numero_rnc', '!=', '-');
                    });
            });
        })->count();

        return view('fornitori.index', compact(
            'fornitori',
            'totaliFornitori',
            'qualificatiCount',
            'inValutazioneCount',
            'conNonConformita'
        ));
    }

    // ══════════════════════════════════════════
    //  CREATE — Form Nuovo Fornitore
    // ══════════════════════════════════════════

    public function create()
    {
        // Suggerisci prossimo codice es. 002
        $ultimoCodice = Fornitore::whereRaw('codice REGEXP "^[0-9]+$"')
            ->selectRaw('MAX(CAST(codice AS UNSIGNED)) as max_cod')
            ->value('max_cod');
        $prossimoCodice = str_pad(($ultimoCodice ? $ultimoCodice + 1 : 1), 3, '0', STR_PAD_LEFT);

        return view('fornitori.create', compact('prossimoCodice'));
    }

    // ══════════════════════════════════════════
    //  STORE — Salvataggio Fornitore
    // ══════════════════════════════════════════

    public function store(Request $request)
    {
        $data = $request->validate([
            'codice'                  => 'nullable|string|max:50|unique:fornitori,codice',
            'ragione_sociale'         => 'required|string|max:255',
            'indirizzo'               => 'nullable|string|max:255',
            'cap'                     => 'nullable|string|max:20',
            'citta'                   => 'nullable|string|max:100',
            'provincia'               => 'nullable|string|max:10',
            'partita_iva'             => 'nullable|string|max:50',
            'codice_fiscale'          => 'nullable|string|max:50',
            'telefono'                => 'nullable|string|max:100',
            'cellulare'               => 'nullable|string|max:100',
            'email'                   => 'nullable|string|max:255',
            'pec'                     => 'nullable|string|max:255',
            'sito_web'                => 'nullable|string|max:255',
            'referente'               => 'nullable|string|max:255',
            'prodotti_acquistati'     => 'nullable|string',
            'criteri_qualifica'       => 'nullable|string',
            'stato_qualifica'         => 'required|in:qualificato,in_valutazione,sospeso,non_qualificato',
            'data_qualifica'          => 'nullable|date',
            'data_scadenza_qualifica' => 'nullable|date',
            'note'                    => 'nullable|string',
            'documenti.*'             => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:20480',
        ]);

        $fornitore = Fornitore::create($data);

        // Traccia nello storico di qualifica
        $fornitore->storicoQualifica()->create([
            'user_id'          => Auth::id(),
            'stato_precedente' => null,
            'stato_nuovo'      => $fornitore->stato_qualifica,
            'tipo_variazione'  => 'creazione',
            'motivo'           => 'Inserimento iniziale nel registro fornitori (MD PG04)',
            'data_variazione'  => now(),
        ]);

        // Upload documenti allegati
        if ($request->hasFile('documenti')) {
            foreach ($request->file('documenti') as $file) {
                $fornitore->addMedia($file)
                    ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    ->toMediaCollection('documenti');
            }
        }

        // Se è stata compilata anche la prima riga di fornitura/servizio
        if ($request->filled('descrizione_prodotto_servizio')) {
            $fornitore->forniture()->create([
                'data_inizio_fornitura'         => $request->data_inizio_fornitura,
                'descrizione_prodotto_servizio' => $request->descrizione_prodotto_servizio,
                'numero_reclamo'                => $request->numero_reclamo ?: '-',
                'numero_rnc'                    => $request->numero_rnc ?: '-',
                'numero_rac'                    => $request->numero_rac ?: '-',
                'esito'                         => $request->esito ?: 'positivo',
                'note'                          => $request->note_fornitura,
            ]);
        }

        return redirect()->route('fornitori.show', $fornitore)
            ->with('success', 'Fornitore registrato con successo.');
    }

    // ══════════════════════════════════════════
    //  SHOW — Scheda Fornitore Completa (MD PG04/04-00)
    // ══════════════════════════════════════════

    public function show(Fornitore $fornitore)
    {
        // Verifica inattività per mantenere aggiornato lo stato del singolo fornitore
        $fornitore->verificaESqualificaInattivita();

        $fornitore->load([
            'forniture.oda',
            'ordiniAcquisto.righe',
            'storicoQualifica.user'
        ]);
        $documenti = $fornitore->getMedia('documenti');
        $ultimaDataAcquisto = $fornitore->getUltimaDataAcquisto();

        return view('fornitori.show', compact('fornitore', 'documenti', 'ultimaDataAcquisto'));
    }

    // ══════════════════════════════════════════
    //  EDIT — Modifica Fornitore
    // ══════════════════════════════════════════

    public function edit(Fornitore $fornitore)
    {
        $documenti = $fornitore->getMedia('documenti');
        return view('fornitori.edit', compact('fornitore', 'documenti'));
    }

    // ══════════════════════════════════════════
    //  UPDATE — Aggiornamento Fornitore
    // ══════════════════════════════════════════

    public function update(Request $request, Fornitore $fornitore)
    {
        $data = $request->validate([
            'codice'                  => 'nullable|string|max:50|unique:fornitori,codice,' . $fornitore->id,
            'ragione_sociale'         => 'required|string|max:255',
            'indirizzo'               => 'nullable|string|max:255',
            'cap'                     => 'nullable|string|max:20',
            'citta'                   => 'nullable|string|max:100',
            'provincia'               => 'nullable|string|max:10',
            'partita_iva'             => 'nullable|string|max:50',
            'codice_fiscale'          => 'nullable|string|max:50',
            'telefono'                => 'nullable|string|max:100',
            'cellulare'               => 'nullable|string|max:100',
            'email'                   => 'nullable|string|max:255',
            'pec'                     => 'nullable|string|max:255',
            'sito_web'                => 'nullable|string|max:255',
            'referente'               => 'nullable|string|max:255',
            'prodotti_acquistati'     => 'nullable|string',
            'criteri_qualifica'       => 'nullable|string',
            'stato_qualifica'         => 'required|in:qualificato,in_valutazione,sospeso,non_qualificato',
            'motivo_variazione'       => 'nullable|string|max:1000',
            'data_qualifica'          => 'nullable|date',
            'data_scadenza_qualifica' => 'nullable|date',
            'note'                    => 'nullable|string',
            'documenti.*'             => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:20480',
        ]);

        $vecchioStato = $fornitore->stato_qualifica;
        $nuovoStato = $data['stato_qualifica'];
        unset($data['motivo_variazione']);

        $fornitore->update($data);

        // Se lo stato di qualifica è cambiato, traccia nello storico
        if ($vecchioStato !== $nuovoStato) {
            $fornitore->storicoQualifica()->create([
                'user_id'          => Auth::id(),
                'stato_precedente' => $vecchioStato,
                'stato_nuovo'      => $nuovoStato,
                'tipo_variazione'  => 'manuale',
                'motivo'           => $request->motivo_variazione ?: 'Aggiornamento manuale scheda fornitore',
                'data_variazione'  => now(),
            ]);
        }

        // Upload nuovi documenti
        if ($request->hasFile('documenti')) {
            foreach ($request->file('documenti') as $file) {
                $fornitore->addMedia($file)
                    ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    ->toMediaCollection('documenti');
            }
        }

        return redirect()->route('fornitori.show', $fornitore)
            ->with('success', 'Scheda fornitore aggiornata con successo.');
    }

    /**
     * Cambio rapido dello stato di qualifica con motivazione obbligatoria
     */
    public function cambiaStatoQualifica(Request $request, Fornitore $fornitore)
    {
        $request->validate([
            'stato_qualifica'   => 'required|in:qualificato,in_valutazione,sospeso,non_qualificato',
            'motivo_variazione' => 'required|string|max:1000',
        ]);

        $fornitore->cambiaStatoQualifica(
            nuovoStato: $request->stato_qualifica,
            motivo: $request->motivo_variazione,
            userId: Auth::id(),
            tipo: 'manuale'
        );

        return redirect()->back()
            ->with('success', "Stato qualifica aggiornato a '{$fornitore->stato_qualifica}' con tracciamento nello storico.");
    }

    // ══════════════════════════════════════════
    //  DESTROY — Cancellazione Fornitore
    // ══════════════════════════════════════════

    public function destroy(Fornitore $fornitore)
    {
        $fornitore->delete();
        return redirect()->route('fornitori.index')
            ->with('success', 'Fornitore eliminato.');
    }

    // ══════════════════════════════════════════
    //  FORNITURE & NON CONFORMITÀ — GESTIONE TABELLA 2
    // ══════════════════════════════════════════

    public function storeFornitura(Request $request, Fornitore $fornitore)
    {
        $data = $request->validate([
            'data_inizio_fornitura'         => 'nullable|string|max:50',
            'descrizione_prodotto_servizio' => 'required|string',
            'numero_reclamo'                => 'nullable|string|max:100',
            'numero_rnc'                    => 'nullable|string|max:100',
            'numero_rac'                    => 'nullable|string|max:100',
            'esito'                         => 'required|in:positivo,reclamo,non_conforme',
            'note'                          => 'nullable|string',
        ]);

        $data['numero_reclamo'] = $data['numero_reclamo'] ?: '-';
        $data['numero_rnc']     = $data['numero_rnc'] ?: '-';
        $data['numero_rac']     = $data['numero_rac'] ?: '-';

        $fornitore->forniture()->create($data);

        return back()->with('success', 'Nuova voce aggiunta allo storico forniture.');
    }

    public function updateFornitura(Request $request, Fornitore $fornitore, FornitoreFornitura $fornitura)
    {
        $data = $request->validate([
            'data_inizio_fornitura'         => 'nullable|string|max:50',
            'descrizione_prodotto_servizio' => 'required|string',
            'numero_reclamo'                => 'nullable|string|max:100',
            'numero_rnc'                    => 'nullable|string|max:100',
            'numero_rac'                    => 'nullable|string|max:100',
            'esito'                         => 'required|in:positivo,reclamo,non_conforme',
            'note'                          => 'nullable|string',
        ]);

        $data['numero_reclamo'] = $data['numero_reclamo'] ?: '-';
        $data['numero_rnc']     = $data['numero_rnc'] ?: '-';
        $data['numero_rac']     = $data['numero_rac'] ?: '-';

        $fornitura->update($data);

        return back()->with('success', 'Voce storico forniture aggiornata.');
    }

    public function destroyFornitura(Fornitore $fornitore, FornitoreFornitura $fornitura)
    {
        $fornitura->delete();
        return back()->with('success', 'Voce rimossa dallo storico forniture.');
    }

    // ══════════════════════════════════════════
    //  MEDIA — Cancellazione Allegato
    // ══════════════════════════════════════════

    public function destroyMedia(Fornitore $fornitore, $mediaId)
    {
        $fornitore->media()->findOrFail($mediaId)->delete();
        return back()->with('success', 'Documento allegato rimosso.');
    }

    // ══════════════════════════════════════════
    //  EXPORT WORD — Genera Scheda Fornitore (MD PG04/04-00)
    // ══════════════════════════════════════════

    public function downloadWord(Fornitore $fornitore)
    {
        $fornitore->load('forniture');

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'marginTop'    => 1000,
            'marginBottom' => 1000,
            'marginLeft'   => 1200,
            'marginRight'  => 1200,
        ]);

        // Header Documento
        $header = $section->addHeader();
        $headerTable = $header->addTable(['width' => 100 * 50, 'unit' => 'pct', 'borderSize' => 6, 'borderColor' => '999999']);
        $headerTable->addRow();
        $headerTable->addCell(5000)->addText('TECNOLAB S.r.l.', ['bold' => true, 'size' => 11]);
        $headerTable->addCell(5000)->addText('MD PG04/04-00   SCHEDA FORNITORE N° ' . ($fornitore->codice ?? $fornitore->id), ['bold' => true, 'size' => 10], ['alignment' => Jc::END]);

        // Titolo
        $section->addText('SCHEDA DI QUALIFICA E MONITORAGGIO FORNITORE', ['bold' => true, 'size' => 14, 'color' => 'D90429'], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        // ── TABELLA 1: ESTREMI DEL FORNITORE ──
        $section->addText('1. ESTREMI DEL FORNITORE', ['bold' => true, 'size' => 11, 'color' => '333333']);
        $tableStyle = ['borderSize' => 6, 'borderColor' => 'CCCCCC', 'cellMargin' => 80, 'alignment' => JcTable::CENTER];
        $phpWord->addTableStyle('EstremiTable', $tableStyle);
        $t1 = $section->addTable('EstremiTable');

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Fornitore:', ['bold' => true]);
        $t1->addCell(4500)->addText($fornitore->ragione_sociale, ['bold' => true]);
        $t1->addCell(1500, ['bgColor' => 'F2F2F2'])->addText('Codice:', ['bold' => true]);
        $t1->addCell(1500)->addText($fornitore->codice ?? '—');

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Indirizzo:');
        $t1->addCell(4500)->addText($fornitore->indirizzo_completo);
        $t1->addCell(1500, ['bgColor' => 'F2F2F2'])->addText('C.F. / P.I.:');
        $t1->addCell(1500)->addText($fornitore->partita_iva ?? $fornitore->codice_fiscale ?? '—');

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Telefono:');
        $t1->addCell(4500)->addText($fornitore->telefono ?: '—');
        $t1->addCell(1500, ['bgColor' => 'F2F2F2'])->addText('Cellulare:');
        $t1->addCell(1500)->addText($fornitore->cellulare ?: '—');

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Email:');
        $t1->addCell(4500)->addText($fornitore->email ?: '—');
        $t1->addCell(1500, ['bgColor' => 'F2F2F2'])->addText('PEC:');
        $t1->addCell(1500)->addText($fornitore->pec ?: '—');

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Sito Internet:');
        $t1->addCell(4500)->addText($fornitore->sito_web ?: '—');
        $t1->addCell(1500, ['bgColor' => 'F2F2F2'])->addText('Stato Qualifica:');
        $t1->addCell(1500)->addText(strtoupper($fornitore->stato_qualifica), ['bold' => true, 'color' => $fornitore->stato_qualifica === 'qualificato' ? '008000' : 'C00000']);

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Prodotti acquistati:', ['bold' => true]);
        $t1->addCell(7500, ['gridSpan' => 3])->addText($fornitore->prodotti_acquistati ?: '—');

        $t1->addRow();
        $t1->addCell(2500, ['bgColor' => 'F2F2F2'])->addText('Criteri di qualifica:', ['bold' => true]);
        $t1->addCell(7500, ['gridSpan' => 3])->addText($fornitore->criteri_qualifica ?: '—');

        $section->addTextBreak(1);

        // ── TABELLA 2: STORICO FORNITURE / NON CONFORMITÀ ──
        $section->addText('2. STORICO FORNITURE E MONITORAGGIO QUALITÀ (RECLAMI / RNC / RAC)', ['bold' => true, 'size' => 11, 'color' => '333333']);
        $t2 = $section->addTable('EstremiTable');

        $t2->addRow();
        $t2->addCell(1800, ['bgColor' => 'D9D9D9'])->addText('Data Inizio', ['bold' => true, 'size' => 9], ['alignment' => Jc::CENTER]);
        $t2->addCell(4600, ['bgColor' => 'D9D9D9'])->addText('Descrizione Prodotto / Servizio', ['bold' => true, 'size' => 9]);
        $t2->addCell(1200, ['bgColor' => 'D9D9D9'])->addText('N° Reclamo', ['bold' => true, 'size' => 9], ['alignment' => Jc::CENTER]);
        $t2->addCell(1200, ['bgColor' => 'D9D9D9'])->addText('N° RNC', ['bold' => true, 'size' => 9], ['alignment' => Jc::CENTER]);
        $t2->addCell(1200, ['bgColor' => 'D9D9D9'])->addText('N° RAC', ['bold' => true, 'size' => 9], ['alignment' => Jc::CENTER]);

        if ($fornitore->forniture->isNotEmpty()) {
            foreach ($fornitore->forniture as $fRow) {
                $t2->addRow();
                $t2->addCell(1800)->addText($fRow->data_inizio_fornitura ?: '—', ['size' => 9], ['alignment' => Jc::CENTER]);
                $t2->addCell(4600)->addText($fRow->descrizione_prodotto_servizio, ['size' => 9]);
                $t2->addCell(1200)->addText($fRow->numero_reclamo ?: '-', ['size' => 9], ['alignment' => Jc::CENTER]);
                $t2->addCell(1200)->addText($fRow->numero_rnc ?: '-', ['size' => 9], ['alignment' => Jc::CENTER]);
                $t2->addCell(1200)->addText($fRow->numero_rac ?: '-', ['size' => 9], ['alignment' => Jc::CENTER]);
            }
        } else {
            $t2->addRow();
            $t2->addCell(10000, ['gridSpan' => 5])->addText('Nessuna fornitura registrata al momento.', ['italic' => true, 'size' => 9]);
        }

        $section->addTextBreak(1);
        if ($fornitore->note) {
            $section->addText('Note: ' . $fornitore->note, ['italic' => true, 'size' => 9]);
        }

        // Output download
        $filename = 'Scheda_Fornitore_' . ($fornitore->codice ?: $fornitore->id) . '_' . \Str::slug($fornitore->ragione_sociale) . '.docx';
        $tempPath = storage_path('app/temp_' . time() . '_' . $filename);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}
