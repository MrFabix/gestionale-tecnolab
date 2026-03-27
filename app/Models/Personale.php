<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Personale extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'personale';

    protected $fillable = [
        'user_id', 'nome', 'cognome', 'codice_fiscale',
        'data_nascita', 'luogo_nascita', 'indirizzo',
        'telefono', 'email', 'qualifica', 'reparto',
        'data_assunzione', 'data_fine_rapporto', 'stato', 'note',
    ];

    protected $casts = [
        'data_nascita'       => 'date',
        'data_assunzione'    => 'date',
        'data_fine_rapporto' => 'date',
    ];

    // ── Media collections ──────────────────────────────────────

    public function registerMediaCollections(): void
    {
        // Foto profilo (una sola)
        $this->addMediaCollection('foto')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        // Documenti generici: contratto, patente, carta identità, ecc.
        $this->addMediaCollection('documenti')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(120)->height(120)
            ->sharpen(5)
            ->performOnCollections('foto');
    }

    // ── Relazioni ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formazioni()
    {
        return $this->hasMany(PersonaleFormazione::class)->orderByDesc('data_conseguimento');
    }

    // ── Accessor ───────────────────────────────────────────────

    public function getNomeCompletoAttribute(): string
    {
        return "{$this->cognome} {$this->nome}";
    }

    /** Formazioni scadute */
    public function formazioniScadute()
    {
        return $this->formazioni()
            ->whereNotNull('data_scadenza')
            ->where('data_scadenza', '<', now()->toDateString());
    }

    /** Formazioni in scadenza entro N giorni */
    public function formazioniInScadenza(int $giorni = 30)
    {
        return $this->formazioni()
            ->whereNotNull('data_scadenza')
            ->where('data_scadenza', '>=', now()->toDateString())
            ->where('data_scadenza', '<=', now()->addDays($giorni)->toDateString());
    }

    /** Documenti in scadenza entro N giorni (da Spatie custom_properties) */
    public function hasAllerte(int $giorni = 30): bool
    {
        return $this->formazioniScadute()->count() > 0
            || $this->formazioniInScadenza($giorni)->count() > 0;
    }
}
