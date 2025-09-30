<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Commessa;
use Illuminate\Http\Request;
//usiamo maatwbiste/ecxel
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DefaultImport;
use PhpOffice\PhpWord\TemplateProcessor;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;


class ReportController extends Controller
{
    /* ===== Wizard ===== */

    // Step 1: scegli commessa
    public function createStep1()
    {
        $commesse = Commessa::with('cliente')->where('stato', '!=', 'chiusa')->orderBy('codice')->get();        // Calcola il rapporto_numero di default
        $maxRapporto = Report::max('rapporto_numero');
        // Se non ci sono report, parte da 1
        $rapporto_numero_default = $maxRapporto ? ((int)$maxRapporto + 1) : 1;
        return view('reports.wizard.step1', compact('commesse', 'rapporto_numero_default'));
    }

    public function postStep1(Request $request)
    {
        $request->validate([
            'commessa_id' => 'required|exists:commesse,id',
            'data' => 'required|date',
            'data_accettazione_materiale' => 'required|date',
            'rif_ordine' => 'required|string|max:255',
            'data_ordine' => 'required|date',
            'rapporto_numero' => 'required|string|max:255',
            'numero_revisione' => 'nullable|string|max:255',
        ]);

        session(['report_wizard.commessa_id' => $request->commessa_id]);
        session(['report_wizard.data' => $request->data]);
        session(['report_wizard.data_accettazione_materiale' => $request->data_accettamento_materiale]);
        session(['report_wizard.rif_ordine' => $request->rif_ordine]);
        session(['report_wizard.data_ordine' => $request->data_ordine]);
        session(['report_wizard.rapporto_numero' => $request->rapporto_numero]);
        session(['report_wizard.numero_revisione' => $request->numero_revisione]);
        return redirect()->route('reports.wizard.step2');
    }

    // Step 2: scegli tipo prova
    public function createStep2()
    {
        abort_unless(session()->has('report_wizard.commessa_id'), 302, '', ['Location' => route('reports.wizard.step1')]);
        return view('reports.wizard.step2');
    }


    public function postStep2(Request $request)
    {
        $request->validate([
            'tipo_prova' => 'required|in:resilienza,trazione,chimica',
            'oggetto' => 'required|string|max:255',
            'stato_fornitura' => 'required|string|max:255',
        ]);

        session(['report_wizard.tipo_prova' => $request->tipo_prova]);
        session(['report_wizard.oggetto' => $request->oggetto]);
        session(['report_wizard.stato_fornitura' => $request->stato_fornitura]);
        if ($request->hasFile('excel_file') && $request->file('excel_file')->isValid()) {
            $path = $request->file('excel_file')->store('reports/temp', 'public');
            session(['report_wizard.excel_file' => $path]);
        } else {
            session()->forget('report_wizard.excel_file');
        }

        if (session()->has('report_wizard.excel_file')) {
            $filePath = storage_path('app/public/' . session('report_wizard.excel_file'));
            $tipo = $request->tipo_prova;
            try {
                $datiEstratti = Excel::toArray(new DefaultImport, $filePath);
                if (!empty($datiEstratti) && isset($datiEstratti[0])) {
                    $foglio = $datiEstratti[0];
                    $estratti = [];
                    switch ($tipo) {
                        case 'resilienza':
                            foreach (array_slice($foglio, 1, 3) as $riga) {
                                $estratti[] = [
                                    'codice' => $riga[0] ?? null,
                                    'tipo' => $riga[1] ?? null,
                                    'direzione' => $riga[2] ?? null,
                                    'spessore_mm' => $riga[3] ?? null,
                                    'larghezza_mm' => $riga[4] ?? null,
                                    'lunghezza_mm' => $riga[5] ?? null,
                                    'temperatura_C' => $riga[6] ?? null,
                                    'energia_J' => $riga[7] ?? null,
                                    'media_J' => $riga[8] ?? null,
                                    'area_duttile_percent' => $riga[9] ?? null,
                                    'espansione_laterale_mm' => $riga[10] ?? null,
                                ];
                            }
                            break;
                        case 'trazione':
                            foreach (array_slice($foglio, 1) as $riga) {
                                $estratti[] = [
                                    'codice' => $riga[0] ?? null,
                                    'tipo' => $riga[1] ?? null,
                                    'spessore' => $riga[2] ?? null,
                                    'larghezza' => $riga[3] ?? null,
                                    'area' => $riga[4] ?? null,
                                    'lunghezza' => $riga[5] ?? null,
                                    'temperatura' => $riga[6] ?? null,
                                    'snervamento' => $riga[7] ?? null,
                                    'resistenza' => $riga[8] ?? null,
                                    'allungamento' => $riga[9] ?? null,
                                    'strizione' => $riga[10] ?? null,
                                ];
                            }
                            break;
                        case 'chimica':
                            // prendi la prima riga come intestazioni
                            $header = $foglio[0];

                            // elementi che ti servono davvero
                            $wanted = ['C', 'Si', 'Mn', 'P', 'S', 'Cr', 'Ni', 'Mo', 'Al', 'Cu', 'Ti', 'Nb', 'V'];

                            // costruisci la mappa colonna → nome elemento
                            $map = [];
                            foreach ($header as $i => $colName) {
                                $colName = trim($colName);
                                if (in_array($colName, $wanted)) {
                                    $map[$i] = $colName;
                                }
                            }

                            $somme = array_fill_keys($wanted, 0.0);
                            $count = 0;
                            $estratti = [];

                            $toFloat = function ($raw) {
                                if ($raw === null || $raw === '') return null;
                                $clean = str_replace(',', '.', (string)$raw);
                                return is_numeric($clean) ? (float)$clean : null;
                            };

                            foreach (array_slice($foglio, 1) as $riga) {
                                $campione = [];
                                foreach ($map as $colIndex => $colName) {
                                    $raw = $riga[$colIndex] ?? null;
                                    $val = $toFloat($raw);
                                    $campione[$colName] = $val;
                                    if ($val !== null) {
                                        $somme[$colName] += $val;
                                    }
                                }
                                // considera solo righe non vuote
                                if (array_filter($campione, fn($v) => $v !== null)) {
                                    $estratti[] = $campione;
                                    $count++;
                                }
                            }

                            // calcolo medie
                            $medie = [];
                            foreach ($wanted as $i => $colName) {
                                $media = $count > 0 ? $somme[$colName] / $count : null;
                                // Arrotondamento secondo regola
                                $decimali = [3, 3, 3, 4, 4, 3, 3, 3, 3, 3, 4, 4, 4][$i];
                                $medie[$colName] = $media !== null ? round($media, $decimali) : null;
                            }

                            // calcolo Ceq (formula IIW)
                            if ($count > 0) {
                                $medie['Ceq'] =
                                    ($medie['C'] ?? 0) +
                                    (($medie['Mn'] ?? 0) / 6) +
                                    ((($medie['Cr'] ?? 0) + ($medie['Mo'] ?? 0) + ($medie['V'] ?? 0)) / 5) +
                                    (($medie['Ni'] ?? 0) + ($medie['Cu'] ?? 0)) / 15;

                                //arrotondamento a 4 decimali
                                $medie['Ceq'] = round($medie['Ceq'], 4);
                            } else {
                                $medie['Ceq'] = null;
                            }

                            session([
                                'report_wizard.dati_estratti' => $estratti,
                                'report_wizard.medie_chimica' => $medie,
                            ]);
                            break;


                    }
                    session(['report_wizard.dati_estratti' => $estratti]);
                }
            } catch (\Exception $e) {

                return back()->withErrors(['excel_file' => 'Errore nella lettura del file Excel: ' . $e->getMessage()])->withInput();
            }
        }
        return redirect()->route('reports.wizard.step3');
    }


    // Step 3: inserisci dati prova (dinamico per tipo)
    public function createStep3()
    {
        abort_unless(session()->has('report_wizard.commessa_id') && session()->has('report_wizard.tipo_prova'),
            302, '', ['Location' => route('reports.wizard.step1')]);

        $tipo = session('report_wizard.tipo_prova');
        return view('reports.wizard.step3', compact('tipo'));
    }

    public function postStep3(Request $request)
    {
        $commessaId = session('report_wizard.commessa_id');
        $data = session('report_wizard.data');
        $tipo = session('report_wizard.tipo_prova');

        abort_unless($commessaId && $tipo, 302, '', ['Location' => route('reports.wizard.step1')]);

        $rules = [];
        switch ($tipo) {
            case 'resilienza':
                $rules = [
                    'provini' => 'required|array|min:1|max:3',
                    'provini.*.codice' => 'nullable|string|max:50',          // Test N°
                    'provini.*.tipo' => 'nullable|string|max:50',
                    'provini.*.direzione' => 'nullable|string|max:50',
                    'provini.*.spessore_mm' => 'nullable|numeric',           // B
                    'provini.*.larghezza_mm' => 'nullable|numeric',          // W
                    'provini.*.lunghezza_mm' => 'nullable|numeric',          // L
                    'provini.*.temperatura_C' => 'nullable|numeric',
                    'provini.*.energia_J' => 'nullable|numeric',
                    'provini.*.media_J' => 'nullable|numeric',
                    'provini.*.area_duttile_percent' => 'nullable|numeric',
                    'provini.*.espansione_laterale_mm' => 'nullable|numeric',
                    'note' => 'nullable|string|max:1000',
                ];
                break;

            case 'trazione':
                $rules = [
                    'trazione' => 'required|array',
                    'trazione.codice' => 'nullable|string|max:50',           // Test N°
                    'trazione.tipo' => 'nullable|string|max:50',
                    'trazione.spessore' => 'nullable|numeric',               // a₀
                    'trazione.larghezza' => 'nullable|numeric',              // b₀
                    'trazione.area' => 'nullable|numeric',                   // S₀
                    'trazione.lunghezza' => 'nullable|numeric',              // L₀
                    'trazione.temperatura' => 'nullable|numeric',
                    'trazione.snervamento' => 'nullable|numeric',            // RP0,2%
                    'trazione.resistenza' => 'nullable|numeric',             // Rm
                    'trazione.allungamento' => 'nullable|numeric',           // A%
                    'trazione.strizione' => 'nullable|numeric',              // Z%
                    'note' => 'nullable|string|max:1000',
                ];
                break;

            case 'chimica':
                $rules = [
                    'chimica' => 'required|array',
                    'chimica.codice' => 'nullable|string|max:50',            // Provino N°
                    'chimica.temperatura' => 'nullable|numeric',
                    'chimica.C' => 'nullable|numeric',
                    'chimica.Si' => 'nullable|numeric',
                    'chimica.Mn' => 'nullable|numeric',
                    'chimica.P' => 'nullable|numeric',
                    'chimica.S' => 'nullable|numeric',
                    'chimica.Cr' => 'nullable|numeric',
                    'chimica.Mo' => 'nullable|numeric',
                    'chimica.Ni' => 'nullable|numeric',
                    'chimica.Cu' => 'nullable|numeric',
                    'chimica.Al' => 'nullable|numeric',
                    'chimica.Nb' => 'nullable|numeric',
                    'chimica.Ti' => 'nullable|numeric',
                    'chimica.V' => 'nullable|numeric',
                    'chimica.Ceq' => 'nullable|numeric',
                    'note' => 'nullable|string|max:1000',
                ];
                break;
        }

        $validated = $request->validate($rules);

        $reportData = $validated;
        if ($tipo === 'chimica' && session()->has('report_wizard.medie_chimica')) {
            $reportData['medie_chimica'] = session('report_wizard.medie_chimica');
        }

        $report = Report::create([
            'commessa_id' => $commessaId,
            'tipo_prova' => $tipo,
            'data' => session('report_wizard.data'),
            'data_accettazione_materiale' => session('report_wizard.data_accettazione_materiale'),
            'rif_ordine' => session('report_wizard.rif_ordine'),
            'data_ordine' => session('report_wizard.data_ordine'),
            'oggetto' => session('report_wizard.oggetto'),
            'stato_fornitura' => session('report_wizard.stato_fornitura'),
            'rapporto_numero' => session('report_wizard.rapporto_numero'),
            'numero_revisione' => session('report_wizard.numero_revisione'),
            'dati' => $reportData,
        ]);

        session()->forget('report_wizard');
        // Conferma pulizia sessione per la view
        return redirect()->route('reports.show', $report)->with(['success' => 'Report creato con successo.', 'wizard_cleared' => true]);
    }

    /* ===== Index/Show (base) ===== */

    public function index()
    {
        $reports = Report::with('commessa.cliente')->latest()->get();
        return view('reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load('commessa.cliente');
        return view('reports.show', compact('report'));
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return back()->with('success', 'Report eliminato.');
    }

    public function downloadPdf(Report $report)
    {
        ini_set('memory_limit', '512M');
        // Seleziona il template in base al tipo di prova
        $templateMap = [
            'chimica' => resource_path('templates/template_analisi_chimica.docx'),
            'trazione' => resource_path('templates/template_trazione.docx'),
            'resilienza' => resource_path('templates/template_resilienza.docx'),
        ];
        $tipo = $report->tipo_prova;
        $templatePath = $templateMap[$tipo] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            abort(404, 'Template Word per la prova "' . $tipo . '" non trovato');
        }
        try {
            $templateProcessor = new TemplateProcessor($templatePath);
        } catch (\Exception $e) {
            abort(500, 'Errore nel caricamento del template Word: ' . $e->getMessage());
        }
        $dati = $report->dati;
        $commessa = $report->commessa;
        $cliente = $commessa->cliente ?? null;
        $templateProcessor->setValue('commessa_descrizione', $commessa->descrizione ?? '-');
        $templateProcessor->setValue('commessa_cliente', $cliente ? $cliente->ragione_sociale : '-');
        $templateProcessor->setValue('data', $report->data ? date('d/m/Y', strtotime($report->data)) : '-');
        $templateProcessor->setValue('data_accettazione_materiale', $report->data_accettazione_materiale ? date('d/m/Y', strtotime($report->data_accettamento_materiale)) : '-');
        $templateProcessor->setValue('rif_ordine', $report->rif_ordine ?? '-');
        $templateProcessor->setValue('data_ordine', $report->data_ordine ? date('d/m/Y', strtotime($report->data_ordine)) : '-');
        $templateProcessor->setValue('oggetto', $report->oggetto ?? '-');
        $templateProcessor->setValue('stato_fornitura', $report->stato_fornitura ?? '-');
        $templateProcessor->setValue('rapporto_numero', $report->rapporto_numero ?? '-');
        $templateProcessor->setValue('numero_revisione', $report->numero_revisione ?? '-');
        // Dati specifici per tipo di prova
        // Medie chimiche (solo se presenti)
        if (isset($dati['medie_chimica'])) {
            foreach ($dati['medie_chimica'] as $elemento => $valore) {
                $templateProcessor->setValue($elemento, $valore !== null ? $valore : '-');
            }
        }
        // Puoi aggiungere qui altri placeholder specifici per trazione/resilienza se servono
        $wordTemp = storage_path('app/temp_report.docx');
        try {
            $templateProcessor->saveAs($wordTemp);
        } catch (\Exception $e) {
            abort(500, 'Errore nel salvataggio del file Word temporaneo: ' . $e->getMessage());
        }
        if (!file_exists($wordTemp)) {
            abort(500, 'File Word temporaneo non creato.');
        }
        return response()->download($wordTemp, 'report_cliente_'.$report->id.'.docx')->deleteFileAfterSend(true);
    }


}
