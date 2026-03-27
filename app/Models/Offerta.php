<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Offerta extends Model implements HasMedia
{
    use InteractsWithMedia;

    //nome tabella
    protected $table = 'offerte';

    protected $fillable = [
        'numero', 'data', 'validita_fino', 'cliente_id',
        'attenzione', 'riferimento', 'consegna', 'pagamento',
        'commessa_rif', 'stato', 'commessa_id', 'note', 'note_interne',
    ];

    protected $casts = [
        'data'          => 'date',
        'validita_fino' => 'date',
    ];

    // ── Media ──────────────────────────────────────────────────
    // Allegati firmati dal cliente (PDF offerta firmata, ordine, ecc.)
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('allegati')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    // ── Relazioni ──────────────────────────────────────────────

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function righe()
    {
        return $this->hasMany(OffertaRiga::class)->orderBy('ordine');
    }

    public function commessa()
    {
        return $this->belongsTo(Commessa::class);
    }

    // ── Accessor ───────────────────────────────────────────────

    /** Totale imponibile (somma righe) */
    public function getTotaleAttribute(): float
    {
        return $this->righe->sum(fn($r) => $r->quantita * $r->prezzo_unitario);
    }

    /** True se la validità è scaduta */
    public function isScaduta(): bool
    {
        return $this->validita_fino && $this->validita_fino->isPast()
            && !in_array($this->stato, ['accettata', 'rifiutata']);
    }

    /** Genera il prossimo numero offerta (es: OFF-2025-042) */
    public static function prossimoNumero(): string
    {
        $anno = now()->year;
        $ultimo = static::whereYear('created_at', $anno)
            ->orderByDesc('id')->first();
        $seq = $ultimo
            ? ((int) substr($ultimo->numero, -3)) + 1
            : 1;
        return sprintf('OFF-%d-%03d', $anno, $seq);
    }
}
