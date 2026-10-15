<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impostazioni', function (Blueprint $table) {
            $table->id();
            $table->string('chiave', 100)->unique();
            $table->text('valore')->nullable();
            $table->string('tipo', 30)->default('string'); // string, integer, boolean, json
            $table->string('gruppo', 50)->default('generale'); // fornitori, sistema, commerciale, ecc.
            $table->string('etichetta')->nullable();
            $table->text('descrizione')->nullable();
            $table->timestamps();
        });

        // Inserimento impostazioni predefinite
        $now = now();
        DB::table('impostazioni')->insert([
            [
                'chiave'      => 'fornitori_inattivita_mesi',
                'valore'      => '12',
                'tipo'        => 'integer',
                'gruppo'      => 'fornitori',
                'etichetta'   => 'Mesi di inattività per squalifica automatica',
                'descrizione' => 'Numero di mesi senza ordini o acquisti trascorsi i quali un fornitore perde la qualifica (predefinito: 12 mesi = 1 anno).',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'chiave'      => 'fornitori_squalifica_automatica_attiva',
                'valore'      => '1',
                'tipo'        => 'boolean',
                'gruppo'      => 'fornitori',
                'etichetta'   => 'Squalifica automatica per inattività attiva',
                'descrizione' => 'Se abilitato, il gestionale imposta automaticamente lo stato a non qualificato se non vi sono acquisti entro il periodo configurato.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'chiave'      => 'fornitori_stato_dopo_inattivita',
                'valore'      => 'non_qualificato',
                'tipo'        => 'string',
                'gruppo'      => 'fornitori',
                'etichetta'   => 'Nuovo stato da applicare per inattività',
                'descrizione' => 'Stato assegnato al fornitore inattivo (non_qualificato oppure sospeso).',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('impostazioni');
    }
};
