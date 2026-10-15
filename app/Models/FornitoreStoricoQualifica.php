<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FornitoreStoricoQualifica extends Model
{
    use HasFactory;

    protected $table = 'fornitore_storico_qualifica';

    protected $fillable = [
        'fornitore_id',
        'user_id',
        'stato_precedente',
        'stato_nuovo',
        'tipo_variazione', // automatica, manuale, creazione
        'motivo',
        'data_variazione',
        'dettagli',
    ];

    protected $casts = [
        'data_variazione' => 'datetime',
        'dettagli'        => 'array',
    ];

    public function fornitore()
    {
        return $this->belongsTo(Fornitore::class, 'fornitore_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatoNuovoLabelAttribute(): string
    {
        return match ($this->stato_nuovo) {
            'qualificato'     => 'Qualificato',
            'in_valutazione'  => 'In Valutazione',
            'sospeso'         => 'Sospeso',
            'non_qualificato' => 'Non Qualificato',
            default           => ucfirst(str_replace('_', ' ', $this->stato_nuovo ?? 'N/D')),
        };
    }

    public function getStatoPrecedenteLabelAttribute(): string
    {
        if (!$this->stato_precedente) {
            return '—';
        }
        return match ($this->stato_precedente) {
            'qualificato'     => 'Qualificato',
            'in_valutazione'  => 'In Valutazione',
            'sospeso'         => 'Sospeso',
            'non_qualificato' => 'Non Qualificato',
            default           => ucfirst(str_replace('_', ' ', $this->stato_precedente)),
        };
    }

    public function getBadgeClass(string $stato): string
    {
        return match ($stato) {
            'qualificato'     => 'bg-success',
            'in_valutazione'  => 'bg-warning text-dark',
            'sospeso'         => 'bg-secondary',
            'non_qualificato' => 'bg-danger',
            default           => 'bg-light text-dark',
        };
    }

    public function getTipoVariazioneLabelAttribute(): string
    {
        return match ($this->tipo_variazione) {
            'automatica' => 'Automatica (Inattività)',
            'manuale'    => 'Modifica Manuale',
            'creazione'  => 'Inserimento Iniziale',
            default      => ucfirst($this->tipo_variazione ?? 'Altro'),
        };
    }
}
