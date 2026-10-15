<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdaRiga extends Model
{
    use HasFactory;

    protected $table = 'rda_righe';

    protected $fillable = [
        'rda_id',
        'descrizione_prodotto',
        'codice_prodotto',
        'quantita',
        'unita_misura',
        'fornitore_id',
        'fornitore_consigliato_testo',
        'ordine',
        'note',
    ];

    protected $casts = [
        'quantita' => 'float',
    ];

    public function rda()
    {
        return $this->belongsTo(Rda::class, 'rda_id');
    }

    public function fornitore()
    {
        return $this->belongsTo(Fornitore::class, 'fornitore_id');
    }

    public function getFornitoreNomeAttribute(): string
    {
        if ($this->fornitore) {
            return $this->fornitore->ragione_sociale;
        }
        return $this->fornitore_consigliato_testo ?: '—';
    }
}
