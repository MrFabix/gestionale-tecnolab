<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documenti';

    protected $fillable = ['titolo', 'categoria', 'descrizione', 'stato'];

    public function revisioni()
    {
        return $this->hasMany(DocumentoRevisione::class)->orderByDesc('numero_revisione');
    }

    public function ultimaRevisione()
    {
        return $this->hasOne(DocumentoRevisione::class)->ofMany('numero_revisione', 'max');
    }
}
