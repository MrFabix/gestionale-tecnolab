<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rda extends Model
{
    use HasFactory;

    protected $table = 'rda';

    protected $fillable = [
        'numero',
        'richiedente',
        'user_id',
        'data_richiesta',
        'data_approvvigionamento',
        'stato',
        'note',
    ];

    protected $casts = [
        'data_richiesta'          => 'date',
        'data_approvvigionamento' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function righe()
    {
        return $this->hasMany(RdaRiga::class, 'rda_id')->orderBy('ordine');
    }

    public function ordiniAcquisto()
    {
        return $this->hasMany(Oda::class, 'rda_id')->orderByDesc('id');
    }

    public function getStatoLabelAttribute(): string
    {
        return match ($this->stato) {
            'bozza'     => 'Bozza',
            'in_attesa' => 'In Attesa',
            'approvata' => 'Approvata',
            'ordinata'  => 'Ordinata',
            'rifiutata' => 'Rifiutata',
            default     => ucfirst($this->stato ?? 'N/D'),
        };
    }

    /**
     * Calcola il prossimo numero RDA nel formato 001/26 (dove 26 è l'anno corrente a 2 cifre)
     */
    public static function prossimoNumero(): string
    {
        $anno = date('y'); // es. "26"
        $ultimo = static::where('numero', 'like', "%/{$anno}")
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($ultimo && preg_match('/^(\d+)\/' . $anno . '$/', $ultimo->numero, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return sprintf('%03d/%s', $seq, $anno);
    }
}
