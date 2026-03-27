<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerte', function (Blueprint $table) {
            $table->id();

            // Riferimenti
            $table->string('numero')->unique();           // es: OFF-2025-001
            $table->date('data');
            $table->date('validita_fino')->nullable();    // default +60gg dalla data
            $table->foreignId('cliente_id')->constrained('clienti')->cascadeOnDelete();
            $table->string('attenzione')->nullable();     // "Alla cortese attenzione di:"
            $table->string('riferimento')->nullable();    // rif. interno cliente

            // Condizioni
            $table->string('consegna')->nullable();       // es: 3 settimane dalla ricezione
            $table->string('pagamento')->nullable();      // es: BB 30gg FM
            $table->string('commessa_rif')->nullable();   // commessa di riferimento (testo libero)

            // Stato e flusso
            $table->enum('stato', ['bozza','inviata','accettata','rifiutata'])->default('bozza');

            // Collegamento commessa (quando offerta accettata → commessa creata)
            $table->foreignId('commessa_id')->nullable()->constrained('commesse')->nullOnDelete();

            $table->text('note')->nullable();
            $table->text('note_interne')->nullable();     // non stampate nel documento
            $table->timestamps();
        });

        Schema::create('offerta_righe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerta_id')->constrained('offerte')->cascadeOnDelete();
            $table->unsignedSmallInteger('ordine')->default(0);
            $table->text('descrizione');
            $table->string('um', 20)->default('PZ');      // unità di misura
            $table->decimal('quantita', 10, 2)->default(1);
            $table->decimal('prezzo_unitario', 12, 2)->default(0);
            // totale_riga = quantita * prezzo_unitario (calcolato in PHP, non salvato)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerta_righe');
        Schema::dropIfExists('offerte');
    }
};
