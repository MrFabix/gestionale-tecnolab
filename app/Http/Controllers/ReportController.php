<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Commessa;
use Illuminate\Http\Request;
//usiamo maatwbiste/ecxel
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    /* ===== Wizard ===== */

    // Step 1: scegli commessa
    public function createStep1()
    {
        $commesse = Commessa::with('cliente')->orderBy('codice')->get();
        return view('reports.wizard.step1', compact('commesse'));
    }

    public function postStep1(Request $request)
    {
        $request->validate([
            'commessa_id' => 'required|exists:commesse,id',
            'data'       => 'required|date',
            'data_accettazione_materiale' => 'required|date',
            'rif_ordine' => 'required|string|max:255',
            'data_ordine'       => 'required|date',
        ]);

        session(['report_wizard.commessa_id' => $request->commessa_id]);
        session(['report_wizard.data' => $request->data]);
        session(['report_wizard.data_accettazione_materiale' => $request->data_accettazione_materiale]);
        session(['report_wizard.rif_ordine' => $request->rif_ordine]);
        session(['report_wizard.data_ordine' => $request->data_ordine]);
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
        //prendo exel se caricato
        if ($request->hasFile('excel_file')) {
            $file = $request->file('excel_file');
            $path = $file->store('reports_excel');
            session(['report_wizard.excel_file' => $path]);
        } else {
            session()->forget('report_wizard.excel_file');
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
        $dati = [];
        switch ($tipo) {
            case 'resilienza':
                $rules = [
                    'provini' => 'required|array|min:1|max:3',
                    'provini.*.codice' => 'nullable|string|max:50',
                    'provini.*.tipo' => 'nullable|string|max:50',
                    'provini.*.direzione' => 'nullable|string|max:50',
                    'provini.*.spessore_mm' => 'nullable|numeric',
                    'provini.*.larghezza_mm' => 'nullable|numeric',
                    'provini.*.lunghezza_mm' => 'nullable|numeric',
                    'provini.*.temperatura_C' => 'nullable|numeric',
                    'provini.*.energia_J' => 'nullable|numeric',
                    'provini.*.media_J' => 'nullable|numeric',
                    'provini.*.area_duttile_percent' => 'nullable|numeric',
                    'provini.*.espansione_laterale_mm' => 'nullable|numeric',
                ];
                $rules['note'] = 'nullable|string|max:1000';
                break;
            case 'trazione':
                $rules = [
                    'trazione' => 'required|array',
                    'trazione.codice' => 'nullable|string|max:50',
                    'trazione.tipo' => 'nullable|string|max:50',
                    'trazione.spessore' => 'nullable|numeric',
                    'trazione.larghezza' => 'nullable|numeric',
                    'trazione.area' => 'nullable|numeric',
                    'trazione.lunghezza' => 'nullable|numeric',
                    'trazione.temperatura' => 'nullable|numeric',
                    'trazione.snervamento' => 'nullable|numeric',
                    'trazione.resistenza' => 'nullable|numeric',
                    'trazione.allungamento' => 'nullable|numeric',
                    'trazione.strizione' => 'nullable|numeric',
                ];
                $rules['note'] = 'nullable|string|max:1000';
                break;
            case 'chimica':
                $rules = [
                    'chimica' => 'required|array',
                    'chimica.codice' => 'nullable|string|max:50',
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
                ];
                $rules['note'] = 'nullable|string|max:1000';
                break;
        }
        $validated = $request->validate($rules);

        $report = Report::create([
            'commessa_id' => $commessaId,
            'tipo_prova'  => $tipo,
            'dati'        => $validated,
        ]);

        session()->forget('report_wizard');

        return redirect()->route('reports.show', $report)->with('success', 'Report creato con successo.');
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
}
