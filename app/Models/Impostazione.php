<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Impostazione extends Model
{
    use HasFactory;

    protected $table = 'impostazioni';

    protected $fillable = [
        'chiave',
        'valore',
        'tipo',
        'gruppo',
        'etichetta',
        'descrizione',
    ];

    /**
     * Recupera il valore tipizzato di un'impostazione globale.
     */
    public static function get(string $chiave, mixed $default = null): mixed
    {
        $impostazione = static::where('chiave', $chiave)->first();

        if (!$impostazione) {
            return $default;
        }

        return match ($impostazione->tipo) {
            'integer', 'int' => (int) $impostazione->valore,
            'boolean', 'bool' => filter_var($impostazione->valore, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($impostazione->valore, true) ?? $default,
            'float' => (float) $impostazione->valore,
            default => $impostazione->valore ?? $default,
        };
    }

    /**
     * Salva o aggiorna un'impostazione globale.
     */
    public static function set(string $chiave, mixed $valore, ?string $tipo = null, ?string $gruppo = null, ?string $etichetta = null, ?string $descrizione = null): static
    {
        $impostazione = static::firstOrNew(['chiave' => $chiave]);

        if (is_bool($valore)) {
            $impostazione->valore = $valore ? '1' : '0';
            $impostazione->tipo = $tipo ?? 'boolean';
        } elseif (is_array($valore) || is_object($valore)) {
            $impostazione->valore = json_encode($valore);
            $impostazione->tipo = $tipo ?? 'json';
        } else {
            $impostazione->valore = (string) $valore;
            if ($tipo) {
                $impostazione->tipo = $tipo;
            } elseif (is_int($valore)) {
                $impostazione->tipo = 'integer';
            }
        }

        if ($gruppo) {
            $impostazione->gruppo = $gruppo;
        }
        if ($etichetta) {
            $impostazione->etichetta = $etichetta;
        }
        if ($descrizione) {
            $impostazione->descrizione = $descrizione;
        }

        $impostazione->save();
        return $impostazione;
    }
}
