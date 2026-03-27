<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Commessa;
use App\Models\Report;
use App\Models\Offerta;
use App\Models\Attrezzatura;
use App\Models\AttrezzaturaTaratura;
use App\Models\AttrezzaturaManutenzione;
use App\Models\Personale;
use App\Models\PersonaleFormazione;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Periodo filtro ────────────────────────────────────────
        $periodo = $request->get('periodo', 'mese'); // mese | anno | custom
        $dal     = null;
        $al      = null;

        switch ($periodo) {
            case 'mese':
                $dal = now()->startOfMonth();
                $al  = now()->endOfMonth();
                break;
            case 'anno':
                $dal = now()->startOfYear();
                $al  = now()->endOfYear();
                break;
            case 'custom':
                $dal = $request->filled('dal') ? Carbon::parse($request->dal)->startOfDay() : now()->startOfMonth();
                $al  = $request->filled('al')  ? Carbon::parse($request->al)->endOfDay()   : now()->endOfMonth();
                break;
            default: // tutto
                $dal = null;
                $al  = null;
        }

        // ── CLIENTI ───────────────────────────────────────────────
        $clientiTotali = Cliente::count();
        $clientiNuovi  = $dal
            ? Cliente::whereBetween('created_at', [$dal, $al])->count()
            : $clientiTotali;

        // ── COMMESSE ──────────────────────────────────────────────
        $commesseAperte      = Commessa::where('stato', 'aperta')->count();
        $commesseInLavoraz   = Commessa::where('stato', 'in_lavorazione')->count();
        $commesseChiuse      = Commessa::where('stato', 'chiusa')->count();
        $commessePeriodo     = $dal
            ? Commessa::whereBetween('created_at', [$dal, $al])->count()
            : Commessa::count();

        // ── REPORT ────────────────────────────────────────────────
        $reportPeriodo = $dal
            ? Report::whereBetween('created_at', [$dal, $al])->count()
            : Report::count();
        $reportTotali  = Report::count();

        // Report per mese (ultimi 6 mesi) per grafico
        $reportPerMese = collect(range(5, 0))->map(function ($i) {
            $m = now()->subMonths($i);
            return [
                'mese'  => $m->translatedFormat('M Y'),
                'count' => Report::whereYear('created_at', $m->year)
                    ->whereMonth('created_at', $m->month)
                    ->count(),
            ];
        });

        // ── OFFERTE ───────────────────────────────────────────────
        $queryOff = $dal ? Offerta::whereBetween('created_at', [$dal, $al]) : Offerta::query();

        $offerteBozza     = (clone $queryOff)->where('stato', 'bozza')->count();
        $offerteInviate   = (clone $queryOff)->where('stato', 'inviata')->count();
        $offerteAccettate = (clone $queryOff)->where('stato', 'accettata')->count();
        $offerteRifiutate = (clone $queryOff)->where('stato', 'rifiutata')->count();

        // Valore totale offerte accettate nel periodo
        $valoreAccettate = Offerta::where('stato', 'accettata')
            ->when($dal, fn($q) => $q->whereBetween('created_at', [$dal, $al]))
            ->with('righe')
            ->get()
            ->sum(fn($o) => $o->totale);

        // ── TARATURE ATTREZZATURE ─────────────────────────────────
        $oggi = now()->toDateString();
        $tra30 = now()->addDays(30)->toDateString();

        // Ultima taratura per ogni attrezzatura
        $taratureScadute = AttrezzaturaTaratura::whereNotNull('data_scadenza')
            ->where('data_scadenza', '<', $oggi)
            ->whereIn('id', function ($q) {
                // Solo la più recente per attrezzatura
                $q->selectRaw('MAX(id)')
                    ->from('attrezzatura_tarature')
                    ->groupBy('attrezzatura_id');
            })
            ->with('attrezzatura')
            ->orderBy('data_scadenza')
            ->get();

        $taratureInScadenza = AttrezzaturaTaratura::whereNotNull('data_scadenza')
            ->whereBetween('data_scadenza', [$oggi, $tra30])
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('attrezzatura_tarature')
                    ->groupBy('attrezzatura_id');
            })
            ->with('attrezzatura')
            ->orderBy('data_scadenza')
            ->get();

        // ── MANUTENZIONI IN SCADENZA ──────────────────────────────
        $manutenzioniScadute = AttrezzaturaManutenzione::whereNotNull('prossima_scadenza')
            ->where('prossima_scadenza', '<', $oggi)
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('attrezzatura_manutenzioni')
                    ->groupBy('attrezzatura_id');
            })
            ->with('attrezzatura')
            ->orderBy('prossima_scadenza')
            ->get();

        $manutenzioniInScadenza = AttrezzaturaManutenzione::whereNotNull('prossima_scadenza')
            ->whereBetween('prossima_scadenza', [$oggi, $tra30])
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('attrezzatura_manutenzioni')
                    ->groupBy('attrezzatura_id');
            })
            ->with('attrezzatura')
            ->orderBy('prossima_scadenza')
            ->get();

        // ── FORMAZIONE PERSONALE ──────────────────────────────────
        $formazioniScadute = PersonaleFormazione::whereNotNull('data_scadenza')
            ->where('data_scadenza', '<', $oggi)
            ->with('personale')
            ->orderBy('data_scadenza')
            ->get();

        $formazioniInScadenza = PersonaleFormazione::whereNotNull('data_scadenza')
            ->whereBetween('data_scadenza', [$oggi, $tra30])
            ->with('personale')
            ->orderBy('data_scadenza')
            ->get();

        // ── ULTIMI RECORD (feed attività) ─────────────────────────
        $ultimiReport  = Report::with('commessa.cliente')->orderByDesc('created_at')->limit(5)->get();
        $ultimiOfferte = Offerta::with('cliente')->orderByDesc('created_at')->limit(5)->get();

        return view('dashboard', compact(
            'periodo', 'dal', 'al',
            // clienti
            'clientiTotali', 'clientiNuovi',
            // commesse
            'commesseAperte', 'commesseInLavoraz', 'commesseChiuse', 'commessePeriodo',
            // report
            'reportPeriodo', 'reportTotali', 'reportPerMese',
            // offerte
            'offerteBozza', 'offerteInviate', 'offerteAccettate', 'offerteRifiutate', 'valoreAccettate',
            // tarature
            'taratureScadute', 'taratureInScadenza',
            // manutenzioni
            'manutenzioniScadute', 'manutenzioniInScadenza',
            // formazione
            'formazioniScadute', 'formazioniInScadenza',
            // feed
            'ultimiReport', 'ultimiOfferte'
        ));
    }
}
