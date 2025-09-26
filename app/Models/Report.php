<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['commessa_id','tipo_prova','dati'];
    protected $casts = ['dati' => 'array'];

    public function commessa()
    {
        return $this->belongsTo(\App\Models\Commessa::class);
    }
}
