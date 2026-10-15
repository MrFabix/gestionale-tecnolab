<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fornitori', function (Blueprint $table) {
            $table->id();
            $table->string('codice', 50)->nullable()->unique();
            $table->string('ragione_sociale');
            $table->string('indirizzo')->nullable();
            $table->string('cap', 20)->nullable();
            $table->string('citta', 100)->nullable();
            $table->string('provincia', 10)->nullable();
            $table->string('partita_iva', 50)->nullable();
            $table->string('codice_fiscale', 50)->nullable();
            $table->string('telefono', 100)->nullable();
            $table->string('cellulare', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('pec', 255)->nullable();
            $table->string('sito_web', 255)->nullable();
            $table->string('referente', 255)->nullable();
            $table->text('prodotti_acquistati')->nullable();
            $table->text('criteri_qualifica')->nullable();
            $table->string('stato_qualifica', 50)->default('qualificato'); // qualificato, in_valutazione, sospeso, non_qualificato
            $table->date('data_qualifica')->nullable();
            $table->date('data_scadenza_qualifica')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('fornitore_forniture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fornitore_id')->constrained('fornitori')->onDelete('cascade');
            $table->string('data_inizio_fornitura', 50)->nullable(); // es. "09/2024"
            $table->text('descrizione_prodotto_servizio');
            $table->string('numero_reclamo', 100)->nullable(); // N° Reclamo
            $table->string('numero_rnc', 100)->nullable();     // N° RNC (Rapporto Non Conformità)
            $table->string('numero_rac', 100)->nullable();     // N° RAC (Rapporto Azione Correttiva)
            $table->string('esito', 50)->default('positivo');  // positivo, reclamo, non_conforme
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fornitore_forniture');
        Schema::dropIfExists('fornitori');
    }
};
