<?php

namespace App\Services;

use App\Models\Fornitore;
use App\Models\Impostazione;
use Illuminate\Support\Collection;

class FornitoreQualificaService
{
    /**
     * Esegue la verifica di inattività su tutti i fornitori attualmente qualificati.
     * Ritorna un array con le statistiche e l'elenco dei fornitori squalificati.
     */
    public function verificaTuttiFornitori(?int $userId = null): array
    {
        $attiva = Impostazione::get('fornitori_squalifica_automatica_attiva', true);
        $mesiSoglia = (int) Impostazione::get('fornitori_inattivita_mesi', 12);

        $fornitoriQualificati = Fornitore::where('stato_qualifica', 'qualificato')->get();
        $squalificati = [];

        if ($attiva) {
            foreach ($fornitoriQualificati as $fornitore) {
                if ($fornitore->verificaESqualificaInattivita($userId)) {
                    $squalificati[] = [
                        'id'              => $fornitore->id,
                        'codice'          => $fornitore->codice,
                        'ragione_sociale' => $fornitore->ragione_sociale,
                        'nuovo_stato'     => $fornitore->stato_qualifica,
                        'ultima_data'     => $fornitore->getUltimaDataAcquisto()?->format('d/m/Y'),
                    ];
                }
            }
        }

        return [
            'attiva'             => $attiva,
            'mesi_soglia'        => $mesiSoglia,
            'totale_verificati'  => $fornitoriQualificati->count(),
            'totale_squalificati'=> count($squalificati),
            'squalificati'       => $squalificati,
        ];
    }
}
