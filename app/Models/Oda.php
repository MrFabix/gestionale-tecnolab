<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oda extends Model
{
    use HasFactory;

    protected $table = 'oda';

    protected $fillable = [
        'numero',
        'data_ordine',
        'fornitore_id',
        'rda_id',
        'commessa_id',
        'user_id',
        'destinazione',
        'data_consegna_prevista',
        'resa',
        'trasporto',
        'imballo',
        'condizioni_pagamento',
        'riferimento_offerta',
        'riferimento_interno',
        'totale_imponibile',
        'aliquota_iva',
        'totale_iva',
        'totale_ordine',
        'stato',
        'note_aggiuntive',
    ];

    protected $casts = [
        'data_ordine'            => 'date',
        'data_consegna_prevista' => 'date',
        'totale_imponibile'      => 'decimal:2',
        'aliquota_iva'           => 'decimal:2',
        'totale_iva'             => 'decimal:2',
        'totale_ordine'          => 'decimal:2',
    ];

    public function fornitore()
    {
        return $this->belongsTo(Fornitore::class, 'fornitore_id');
    }

    public function rda()
    {
        return $this->belongsTo(Rda::class, 'rda_id');
    }

    public function commessa()
    {
        return $this->belongsTo(Commessa::class, 'commessa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function righe()
    {
        return $this->hasMany(OdaRiga::class, 'oda_id')->orderBy('ordine');
    }

    public function fornitura()
    {
        return $this->hasOne(FornitoreFornitura::class, 'oda_id');
    }

    /**
     * Calcola il prossimo numero ODA progressivo annuale (es. 001/26)
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

    /**
     * Ricalcola totale imponibile, IVA e totale ordine
     */
    public function ricalcolaTotali(): void
    {
        $imponibile = $this->righe()->sum('totale_riga');
        $aliquota = $this->aliquota_iva ?? 22.00;
        $iva = round($imponibile * ($aliquota / 100), 2);
        $totale = $imponibile + $iva;

        $this->updateQuietly([
            'totale_imponibile' => $imponibile,
            'totale_iva'        => $iva,
            'totale_ordine'     => $totale,
        ]);
    }

    /**
     * Sincronizza automaticamente la scheda del fornitore (Tabella 2 MD PG04/04-00)
     */
    public function sincronizzaSchedaFornitore(): void
    {
        if (!$this->fornitore_id) {
            return;
        }

        $elencoBeni = $this->righe->pluck('descrizione_bene')->filter()->implode(', ');
        $descrizione = "ODA {$this->numero}" . ($elencoBeni ? ": {$elencoBeni}" : '');

        $fornitura = FornitoreFornitura::firstOrNew(['oda_id' => $this->id]);
        $fornitura->fornitore_id = $this->fornitore_id;
        $fornitura->data_inizio_fornitura = $this->data_ordine ? $this->data_ordine->format('Y-m-d') : now()->format('Y-m-d');
        $fornitura->descrizione_prodotto_servizio = substr($descrizione, 0, 65000);
        $fornitura->esito = 'positivo';
        $fornitura->note = "Ordine di Acquisto emesso il " . ($this->data_ordine ? $this->data_ordine->format('d/m/Y') : date('d/m/Y')) . 
                           " - Imponibile: € " . number_format($this->totale_imponibile, 2, ',', '.');
        $fornitura->save();
    }

    public function getStatoLabelAttribute(): string
    {
        return match ($this->stato) {
            'bozza'      => 'Bozza',
            'inviato'    => 'Inviato al Fornitore',
            'confermato' => 'Confermato dal Fornitore',
            'consegnato' => 'Merce Consegnata / Chiuso',
            'annullato'  => 'Annullato',
            default      => ucfirst($this->stato ?? 'N/D'),
        };
    }
}
