<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attrezzature', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codice')->unique()->nullable();
            $table->string('marca')->nullable();
            $table->string('modello')->nullable();
            $table->string('matricola')->nullable();
            $table->string('ubicazione')->nullable();
            $table->enum('stato', ['attivo', 'in_manutenzione', 'dismesso'])->default('attivo');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('attrezzatura_tarature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attrezzatura_id')->constrained('attrezzature')->cascadeOnDelete();
            $table->date('data_taratura');
            $table->date('data_scadenza')->nullable();
            $table->string('eseguita_da')->nullable();
            $table->text('note')->nullable();
            // Il certificato PDF viene gestito da Spatie Media Library (collezione: 'certificati_taratura')
            $table->timestamps();
        });

        Schema::create('attrezzatura_manutenzioni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attrezzatura_id')->constrained('attrezzature')->cascadeOnDelete();
            $table->date('data_intervento');
            $table->date('prossima_scadenza')->nullable();
            $table->enum('tipo', ['ordinaria', 'straordinaria'])->default('ordinaria');
            $table->string('eseguita_da')->nullable();
            $table->text('descrizione')->nullable();
            // Il documento viene gestito da Spatie Media Library (collezione: 'documenti_manutenzione')
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attrezzatura_manutenzioni');
        Schema::dropIfExists('attrezzatura_tarature');
        Schema::dropIfExists('attrezzature');
    }
};
