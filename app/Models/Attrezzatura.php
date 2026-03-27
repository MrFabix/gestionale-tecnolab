<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Attrezzatura extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table='attrezzature';
    protected $fillable = [
        'nome', 'codice', 'marca', 'modello', 'matricola',
        'ubicazione', 'stato', 'note',
    ];

    /**
     * Definisce le collezioni media disponibili per questa entità.
     * Spatie gestirà automaticamente upload, path e cancellazione.
     */
    public function registerMediaCollections(): void
    {
        // Foto/immagini della macchina (multiple)
        $this->addMediaCollection('immagini')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp']);

        // Documenti generici: manuali, schede tecniche, certificati CE...
        $this->addMediaCollection('documenti')
            ->acceptsMimeTypes(['application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

        // Certificati di taratura (uno per ogni record taratura, gestito via TaraturaObserver)
        $this->addMediaCollection('certificati_taratura')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);

        // Documenti allegati alle manutenzioni
        $this->addMediaCollection('documenti_manutenzione')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    /**
     * Genera automaticamente una thumbnail per le immagini.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(5)
            ->performOnCollections('immagini');
    }

    // ── Relazioni ──────────────────────────────────────────────

    public function tarature()
    {
        return $this->hasMany(AttrezzaturaTaratura::class)->orderByDesc('data_taratura');
    }

    public function manutenzioni()
    {
        return $this->hasMany(AttrezzaturaManutenzione::class)->orderByDesc('data_intervento');
    }

    // ── Accessori utili ────────────────────────────────────────

    /** Restituisce l'ultima taratura eseguita */
    public function ultimaTaratura()
    {
        return $this->tarature()->first();
    }

    /** Restituisce la prossima manutenzione programmata */
    public function prossimaManutenzione()
    {
        return $this->manutenzioni()
            ->whereNotNull('prossima_scadenza')
            ->where('prossima_scadenza', '>=', now())
            ->orderBy('prossima_scadenza')
            ->first();
    }

    /** true se la taratura è scaduta o mancante */
    public function taraturaScaduta(): bool
    {
        $ultima = $this->ultimaTaratura();
        if (!$ultima || !$ultima->data_scadenza) return false;
        return $ultima->data_scadenza < now()->toDateString();
    }
}
