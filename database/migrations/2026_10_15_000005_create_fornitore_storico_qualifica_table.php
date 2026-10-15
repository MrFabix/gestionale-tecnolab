<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fornitore_storico_qualifica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fornitore_id')->constrained('fornitori')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('stato_precedente', 50)->nullable();
            $table->string('stato_nuovo', 50);
            $table->string('tipo_variazione', 50)->default('manuale'); // automatica, manuale, creazione
            $table->text('motivo')->nullable();
            $table->timestamp('data_variazione')->useCurrent();
            $table->json('dettagli')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fornitore_storico_qualifica');
    }
};
