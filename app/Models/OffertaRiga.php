<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffertaRiga extends Model
{
    protected $table = 'offerta_righe';

    protected $fillable = [
        'offerta_id', 'ordine', 'descrizione',
        'um', 'quantita', 'prezzo_unitario',
    ];

    protected $casts = [
        'quantita'        => 'float',
        'prezzo_unitario' => 'float',
    ];

    public function offerta()
    {
        return $this->belongsTo(Offerta::class);
    }

    public function getTotaleRigaAttribute(): float
    {
        return $this->quantita * $this->prezzo_unitario;
    }
}
