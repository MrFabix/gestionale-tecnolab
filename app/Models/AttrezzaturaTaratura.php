<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AttrezzaturaTaratura extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'attrezzatura_tarature';

    protected $fillable = [
        'attrezzatura_id', 'data_taratura', 'data_scadenza',
        'eseguita_da', 'note',
    ];

    protected $casts = [
        'data_taratura' => 'date',
        'data_scadenza' => 'date',
    ];

    /**
     * Ogni record di taratura può avere il proprio certificato PDF allegato.
     * La collezione è singola: un certificato per taratura.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificato')
            ->singleFile() // sostituisce il file se ne carichi uno nuovo
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function attrezzatura()
    {
        return $this->belongsTo(Attrezzatura::class);
    }

    public function isScaduta(): bool
    {
        return $this->data_scadenza && $this->data_scadenza->isPast();
    }

    public function isInScadenza(int $giorniAvviso = 30): bool
    {
        return $this->data_scadenza
            && !$this->isScaduta()
            && $this->data_scadenza->diffInDays(now()) <= $giorniAvviso;
    }
}
