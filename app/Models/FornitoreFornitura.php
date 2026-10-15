<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FornitoreFornitura extends Model
{
    use HasFactory;

    protected $table = 'fornitore_forniture';

    protected $fillable = [
        'fornitore_id',
        'oda_id',
        'data_inizio_fornitura',
        'descrizione_prodotto_servizio',
        'numero_reclamo',
        'numero_rnc',
        'numero_rac',
        'esito',
        'note',
    ];

    public function fornitore()
    {
        return $this->belongsTo(Fornitore::class, 'fornitore_id');
    }

    public function oda()
    {
        return $this->belongsTo(Oda::class, 'oda_id');
    }
}
