<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fornitore;

class FornitoreSeeder extends Seeder
{
    public function run(): void
    {
        $f = Fornitore::updateOrCreate(
            ['codice' => '001'],
            [
                'ragione_sociale' => 'CERMAC S.r.l',
                'indirizzo' => 'Via del Lavoro, 8',
                'cap' => '20060',
                'citta' => "Pozzo d'Adda",
                'provincia' => 'MI',
                'partita_iva' => '05035660967',
                'codice_fiscale' => '05035660967',
                'telefono' => '02-90981113',
                'cellulare' => null,
                'email' => 'orteschi@cermacsrl.com - info@cermacsrl.com',
                'pec' => 'cermacsrl@pec.cermacsrl.com',
                'sito_web' => 'www.cermacsrl.com',
                'prodotti_acquistati' => 'Apparecchiature/accessori ausiliari per prove',
                'criteri_qualifica' => 'Requisiti tecnici richiesti dal Metodo di prova',
                'stato_qualifica' => 'qualificato',
                'data_qualifica' => '2024-09-01',
                'note' => 'Fornitore qualificato per fornitura accessori ausiliari e apparecchiature di prova.',
            ]
        );

        $f->forniture()->updateOrCreate(
            [
                'data_inizio_fornitura' => '09/2024',
                'descrizione_prodotto_servizio' => 'accessori ausiliari per prove'
            ],
            [
                'numero_reclamo' => '-',
                'numero_rnc' => '-',
                'numero_rac' => '-',
                'esito' => 'positivo',
                'note' => 'Consegna regolare e conforme ai requisiti.'
            ]
        );
    }
}
