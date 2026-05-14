<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartelle', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('parent_id')->nullable()->constrained('cartelle')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('documenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cartella_id')->nullable()->constrained('cartelle')->cascadeOnDelete();
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->enum('stato', ['bozza', 'attivo', 'obsoleto'])->default('bozza');
            $table->timestamps();
        });

        Schema::create('documento_revisioni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documenti')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero_revisione')->default(0);
            $table->date('data_revisione');
            $table->string('redatto_da')->nullable();
            $table->text('motivo')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_revisioni');
        Schema::dropIfExists('documenti');
        Schema::dropIfExists('cartelle');
    }
};
