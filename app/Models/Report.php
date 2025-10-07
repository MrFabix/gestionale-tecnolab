<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'commessa_id',
        'tipo_prova',
        'dati',
        'data',
        'data_accettazione_materiale',
        'rif_ordine',
        'data_ordine',
        'oggetto',
        'stato_fornitura',
        'rapporto_numero',
        'numero_revisione',
        'stato',
        'data_inizio',
        'data_fine'
    ];
    protected $casts = [
        'dati' => 'array',
        'data' => 'date',
        'data_accettazione_materiale' => 'date',
        'data_ordine' => 'date',
        'data_inizio' => 'date',
        'data_fine' => 'date',
    ];

    public function commessa()
    {
        return $this->belongsTo(\App\Models\Commessa::class);
    }
}
