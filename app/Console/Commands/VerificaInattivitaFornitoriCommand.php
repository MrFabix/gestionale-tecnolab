<?php

namespace App\Console\Commands;

use App\Services\FornitoreQualificaService;
use Illuminate\Console\Command;

class VerificaInattivitaFornitoriCommand extends Command
{
    protected $signature = 'fornitori:verifica-inattivita';
    protected $description = 'Verifica i fornitori qualificati e squalifica automaticamente chi non ha acquisti entro i mesi configurati';

    public function handle(FornitoreQualificaService $service): int
    {
        $this->info('Avvio verifica inattività fornitori...');

        $res = $service->verificaTuttiFornitori();

        if (!$res['attiva']) {
            $this->warn('La squalifica automatica per inattività è disabilitata nelle impostazioni globali.');
            return self::SUCCESS;
        }

        $this->info("Fornitori qualificati esaminati: {$res['totale_verificati']} (Soglia: {$res['mesi_soglia']} mesi)");

        if ($res['totale_squalificati'] > 0) {
            $this->warn("Fornitori squalificati per inattività: {$res['totale_squalificati']}");
            foreach ($res['squalificati'] as $item) {
                $this->line(" - [ID {$item['id']}] {$item['ragione_sociale']} (Ultimo acquisto: " . ($item['ultima_data'] ?? 'nessuno') . ")");
            }
        } else {
            $this->info('Nessun fornitore da squalificare. Tutti i fornitori qualificati risultano attivi.');
        }

        return self::SUCCESS;
    }
}
