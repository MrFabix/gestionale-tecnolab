<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartella extends Model
{
    protected $table = 'cartelle';

    protected $fillable = ['nome', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Cartella::class, 'parent_id');
    }

    public function sottocartelle()
    {
        return $this->hasMany(Cartella::class, 'parent_id')->orderBy('nome');
    }

    public function documenti()
    {
        return $this->hasMany(Documento::class, 'cartella_id')->orderBy('titolo');
    }

    /** Restituisce la catena di antenati dalla radice fino a questa cartella */
    public function breadcrumb(): array
    {
        $chain = [];
        $current = $this;
        while ($current) {
            array_unshift($chain, $current);
            $current = $current->parent;
        }
        return $chain;
    }
}
