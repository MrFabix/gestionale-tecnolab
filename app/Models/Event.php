<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    protected $fillable = [
        'titolo',
        'descrizione',
        'inizio',
        'fine',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mostra solo eventi globali o personali
    public static function visibiliPerUtente($utenteId)
    {
        return static::whereNull('user_id')
            ->orWhere('user_id', $utenteId);
    }
}
