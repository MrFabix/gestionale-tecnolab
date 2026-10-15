<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rda', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique(); // es. "001/26"
            $table->string('richiedente');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('data_richiesta');
            $table->date('data_approvvigionamento')->nullable();
            $table->string('stato', 50)->default('in_attesa'); // bozza, in_attesa, approvata, ordinata, rifiutata
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('rda_righe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rda_id')->constrained('rda')->onDelete('cascade');
            $table->text('descrizione_prodotto');
            $table->string('codice_prodotto', 100)->nullable();
            $table->decimal('quantita', 10, 2)->default(1);
            $table->string('unita_misura', 20)->default('pz')->nullable();
            $table->foreignId('fornitore_id')->nullable()->constrained('fornitori')->onDelete('set null');
            $table->string('fornitore_consigliato_testo', 255)->nullable();
            $table->integer('ordine')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rda_righe');
        Schema::dropIfExists('rda');
    }
};
