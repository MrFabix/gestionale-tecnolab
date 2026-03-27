<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PersonaleFormazione extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'personale_formazioni';

    protected $fillable = [
        'personale_id', 'titolo', 'ente_erogatore',
        'data_conseguimento', 'data_scadenza', 'note',
    ];

    protected $casts = [
        'data_conseguimento' => 'date',
        'data_scadenza'      => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attestato')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function personale()
    {
        return $this->belongsTo(Personale::class);
    }

    public function isScaduta(): bool
    {
        return $this->data_scadenza && $this->data_scadenza->isPast();
    }

    public function isInScadenza(int $giorni = 30): bool
    {
        return $this->data_scadenza
            && !$this->isScaduta()
            && $this->data_scadenza->diffInDays(now()) <= $giorni;
    }
}
