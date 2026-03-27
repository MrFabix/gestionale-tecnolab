<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AttrezzaturaManutenzione extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'attrezzatura_manutenzioni';

    protected $fillable = [
        'attrezzatura_id', 'data_intervento', 'prossima_scadenza',
        'tipo', 'eseguita_da', 'descrizione',
    ];

    protected $casts = [
        'data_intervento'  => 'date',
        'prossima_scadenza' => 'date',
    ];

    /**
     * Ogni intervento di manutenzione può avere un documento allegato (rapporto, fattura...).
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documento')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function attrezzatura()
    {
        return $this->belongsTo(Attrezzatura::class);
    }
}
