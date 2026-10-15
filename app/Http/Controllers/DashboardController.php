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
use App\Models\Documento;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $oggi = now()->toDateString();
        $tra30 = now()->addDays(30)->toDateString();

        // ── 1. TARATURE ATTREZZATURE ────────────────────────────────
        $taratureScadute = AttrezzaturaTaratura::whereNotNull('data_scadenza')
            ->where('data_scadenza', '<', $oggi)
            ->whereIn('id', function ($q) {
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

        // ── 2. MANUTENZIONI ATTREZZATURE ────────────────────────────
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

        // ── 3. CORSI & FORMAZIONE PERSONALE ─────────────────────────
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

        // ── 4. OFFERTE IN SCADENZA DI VALIDITÀ ──────────────────────
        $offerteInScadenza = Offerta::where('stato', 'inviata')
            ->whereNotNull('validita_fino')
            ->where('validita_fino', '<=', now()->addDays(15))
            ->with('cliente')
            ->orderBy('validita_fino')
            ->get();

        // Conteggi Alert
        $totaleScaduti    = $taratureScadute->count() + $manutenzioniScadute->count() + $formazioniScadute->count();
        $totaleInScadenza = $taratureInScadenza->count() + $manutenzioniInScadenza->count() + $formazioniInScadenza->count();
        $nAlert           = $totaleScaduti + $totaleInScadenza;

        // ── 5. PROSSIMI EVENTI & APPUNTAMENTI IN AGENDA ─────────────
        $prossimiEventi = Event::visibiliPerUtente(Auth::id())
            ->where('inizio', '>=', now()->startOfDay())
            ->where('inizio', '<=', now()->addDays(10)->endOfDay())
            ->orderBy('inizio')
            ->limit(4)
            ->get();

        // ── 6. CONTEGGI RAPIDI PER I BOTTONI DI ACCESSO VELOCE ──────
        $countReport    = Report::count();
        $countCommesse  = Commessa::where('stato', '!=', 'chiusa')->count(); // aperte o in lavorazione
        $countOfferte   = Offerta::whereIn('stato', ['bozza', 'inviata'])->count(); // in corso
        $countClienti   = Cliente::count();
        $countFornitori = \App\Models\Fornitore::count();
        $countRdaInAttesa = \App\Models\Rda::where('stato', 'in_attesa')->count();
        $countRdaTotali = \App\Models\Rda::count();
        $countOda = \App\Models\Oda::count();
        $countMacchine  = Attrezzatura::count();
        $countPersonale = Personale::count();
        $countDocumenti = Documento::count();

        return view('dashboard', compact(
            'taratureScadute', 'taratureInScadenza',
            'manutenzioniScadute', 'manutenzioniInScadenza',
            'formazioniScadute', 'formazioniInScadenza',
            'offerteInScadenza',
            'totaleScaduti', 'totaleInScadenza', 'nAlert',
            'prossimiEventi',
            'countReport', 'countCommesse', 'countOfferte',
            'countClienti', 'countFornitori', 'countRdaInAttesa', 'countRdaTotali', 'countOda', 'countMacchine', 'countPersonale', 'countDocumenti'
        ));
    }
}
