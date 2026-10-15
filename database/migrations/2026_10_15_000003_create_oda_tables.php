<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── TABELLA ODA (ORDINI DI ACQUISTO - MD PG 04/02-00) ──────
        Schema::create('oda', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique(); // es. 011/26
            $table->date('data_ordine');
            $table->foreignId('fornitore_id')->constrained('fornitori')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('rda_id')->nullable()->constrained('rda')->nullOnDelete();
            $table->foreignId('commessa_id')->nullable()->constrained('commesse')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Dati logistici e commerciali (MD PG 04/02-00)
            $table->string('destinazione')->default("VIA DELL'INFORMATICA 3  - S. PALOMBA POMEZIA  (RM)");
            $table->date('data_consegna_prevista')->nullable();
            $table->string('resa', 100)->default('VS CURA');
            $table->string('trasporto', 100)->default('VS CURA');
            $table->string('imballo', 100)->default('VS CURA');
            $table->string('condizioni_pagamento', 150)->default('BB   30GG FM');
            $table->string('riferimento_offerta', 255)->nullable(); // es. e-mail del 18/05/2026
            $table->string('riferimento_interno', 150)->nullable(); // es. Martino Malgieri

            // Importi
            $table->decimal('totale_imponibile', 10, 2)->default(0.00);
            $table->decimal('aliquota_iva', 5, 2)->default(22.00);
            $table->decimal('totale_iva', 10, 2)->default(0.00);
            $table->decimal('totale_ordine', 10, 2)->default(0.00);

            // Stato ODA
            $table->string('stato', 50)->default('inviato'); // bozza, inviato, confermato, consegnato, annullato
            $table->text('note_aggiuntive')->nullable();

            $table->timestamps();
        });

        // ── TABELLA ODA RIGHE ──────────────────────────────────────
        Schema::create('oda_righe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oda_id')->constrained('oda')->cascadeOnDelete();
            $table->foreignId('rda_riga_id')->nullable()->constrained('rda_righe')->nullOnDelete();
            $table->text('descrizione_bene');
            $table->string('codice_articolo', 100)->nullable();
            $table->string('unita_misura', 20)->default('PZ');
            $table->decimal('quantita', 10, 2)->default(1.00);
            $table->decimal('costo_unitario', 10, 2)->default(0.00);
            $table->decimal('totale_riga', 10, 2)->default(0.00);
            $table->unsignedInteger('ordine')->default(1);
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });

        // ── AGGIUNTA ODA_ID A FORNITORE_FORNITURE PER COLLEGAMENTO DIRETTO ──
        if (Schema::hasTable('fornitore_forniture')) {
            Schema::table('fornitore_forniture', function (Blueprint $table) {
                $table->foreignId('oda_id')->nullable()->after('fornitore_id')->constrained('oda')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('fornitore_forniture') && Schema::hasColumn('fornitore_forniture', 'oda_id')) {
            Schema::table('fornitore_forniture', function (Blueprint $table) {
                $table->dropForeign(['oda_id']);
                $table->dropColumn('oda_id');
            });
        }

        Schema::dropIfExists('oda_righe');
        Schema::dropIfExists('oda');
    }
};
