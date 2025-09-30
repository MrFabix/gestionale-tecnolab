<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->date('data')->nullable();
            $table->date('data_accettazione_materiale')->nullable();
            $table->string('rif_ordine')->nullable();
            $table->date('data_ordine')->nullable();
            $table->string('oggetto')->nullable();
            $table->string('stato_fornitura')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'data',
                'data_accettazione_materiale',
                'rif_ordine',
                'data_ordine',
                'oggetto',
                'stato_fornitura',
            ]);
        });
    }
};

