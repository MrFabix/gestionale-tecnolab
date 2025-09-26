<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clienti';

    protected $fillable = [
        'id',
        'ragione_sociale',
        'indirizzo',
        'cap',
        'citta',
        'provincia',
        'partita_iva',
        'codice_fiscale',
        'telefono',
        'email',
        'referente'
    ];
}
