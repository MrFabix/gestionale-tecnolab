<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('colore', 20)->default('#3b82f6')->after('fine');
            // tipo: 'manuale' oppure 'sistema' (scadenze auto — non modificabili)
            $table->string('tipo', 30)->default('manuale')->after('colore');
            // Riferimento opzionale all'entità origine (es: attrezzatura_id, personale_id...)
            $table->string('ref_type', 50)->nullable()->after('tipo');
            $table->unsignedBigInteger('ref_id')->nullable()->after('ref_type');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['colore', 'tipo', 'ref_type', 'ref_id']);
        });
    }
};
