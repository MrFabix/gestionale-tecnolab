<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OdaRiga extends Model
{
    use HasFactory;

    protected $table = 'oda_righe';

    protected $fillable = [
        'oda_id',
        'rda_riga_id',
        'descrizione_bene',
        'codice_articolo',
        'unita_misura',
        'quantita',
        'costo_unitario',
        'totale_riga',
        'ordine',
        'note',
    ];

    protected $casts = [
        'quantita'       => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'totale_riga'    => 'decimal:2',
    ];

    public function oda()
    {
        return $this->belongsTo(Oda::class, 'oda_id');
    }

    public function rdaRiga()
    {
        return $this->belongsTo(RdaRiga::class, 'rda_riga_id');
    }

    protected static function booted()
    {
        static::saving(function ($riga) {
            $qta = (float) ($riga->quantita ?? 1);
            $costo = (float) ($riga->costo_unitario ?? 0);
            $riga->totale_riga = round($qta * $costo, 2);
        });
    }
}
