<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personale', function (Blueprint $table) {
            $table->id();
            // Collegamento opzionale all'utente del sistema
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Anagrafica
            $table->string('nome');
            $table->string('cognome');
            $table->string('codice_fiscale')->nullable()->unique();
            $table->date('data_nascita')->nullable();
            $table->string('luogo_nascita')->nullable();
            $table->string('indirizzo')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();

            // Ruolo aziendale
            $table->string('qualifica')->nullable();       // es: Tecnico di laboratorio, Responsabile...
            $table->string('reparto')->nullable();
            $table->date('data_assunzione')->nullable();
            $table->date('data_fine_rapporto')->nullable();
            $table->enum('stato', ['attivo', 'in_ferie', 'dimesso', 'sospeso'])->default('attivo');

            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('personale_formazioni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personale_id')->constrained('personale')->cascadeOnDelete();
            $table->string('titolo');                         // es: Corso sicurezza D.Lgs 81, HACCP...
            $table->string('ente_erogatore')->nullable();
            $table->date('data_conseguimento');
            $table->date('data_scadenza')->nullable();
            $table->text('note')->nullable();
            // Attestato gestito da Spatie (collezione: 'attestato')
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personale_formazioni');
        Schema::dropIfExists('personale');
    }
};
