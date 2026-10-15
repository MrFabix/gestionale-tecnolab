<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Fornitore extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'fornitori';

    protected $fillable = [
        'codice',
        'ragione_sociale',
        'indirizzo',
        'cap',
        'citta',
        'provincia',
        'partita_iva',
        'codice_fiscale',
        'telefono',
        'cellulare',
        'email',
        'pec',
        'sito_web',
        'referente',
        'prodotti_acquistati',
        'criteri_qualifica',
        'stato_qualifica',
        'data_qualifica',
        'data_scadenza_qualifica',
        'note',
    ];

    protected $casts = [
        'data_qualifica'          => 'date',
        'data_scadenza_qualifica' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documenti')
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'image/webp'
            ]);
    }

    public function forniture()
    {
        return $this->hasMany(FornitoreFornitura::class, 'fornitore_id')->orderByDesc('id');
    }

    public function ordiniAcquisto()
    {
        return $this->hasMany(Oda::class, 'fornitore_id')->orderByDesc('id');
    }

    /**
     * Ritorna l'indirizzo formattato completo
     */
    public function getIndirizzoCompletoAttribute(): string
    {
        $parti = array_filter([
            $this->indirizzo,
            $this->cap,
            $this->citta,
            $this->provincia ? "({$this->provincia})" : null
        ]);
        return implode(' ', $parti) ?: '—';
    }
}
