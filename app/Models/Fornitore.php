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

    public function storicoQualifica()
    {
        return $this->hasMany(FornitoreStoricoQualifica::class, 'fornitore_id')->orderByDesc('data_variazione')->orderByDesc('id');
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

    /**
     * Calcola la data dell'ultimo acquisto registrato (da ODA o storico forniture).
     */
    public function getUltimaDataAcquisto(): ?\Illuminate\Support\Carbon
    {
        // 1. Data ultimo ODA non annullato
        $ultimoOda = $this->ordiniAcquisto()
            ->where('stato', '!=', 'annullato')
            ->whereNotNull('data_ordine')
            ->orderByDesc('data_ordine')
            ->first();

        $dataOda = $ultimoOda?->data_ordine ? \Illuminate\Support\Carbon::parse($ultimoOda->data_ordine) : null;

        // 2. Data ultima fornitura
        $dataFornitura = null;
        $ultimaFornitura = $this->forniture()->whereNotNull('data_inizio_fornitura')->orderByDesc('id')->first();
        if ($ultimaFornitura && $ultimaFornitura->data_inizio_fornitura) {
            $raw = trim($ultimaFornitura->data_inizio_fornitura);
            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
                    $dataFornitura = \Illuminate\Support\Carbon::parse($raw);
                } elseif (preg_match('/^(\d{1,2})\/(\d{4})$/', $raw, $matches)) {
                    $dataFornitura = \Illuminate\Support\Carbon::createFromDate((int) $matches[2], (int) $matches[1], 1);
                } elseif (preg_match('/^\d{4}$/', $raw)) {
                    $dataFornitura = \Illuminate\Support\Carbon::createFromDate((int) $raw, 1, 1);
                }
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        if ($dataOda && $dataFornitura) {
            return $dataOda->greaterThan($dataFornitura) ? $dataOda : $dataFornitura;
        }

        return $dataOda ?? $dataFornitura;
    }

    /**
     * Modifica lo stato di qualifica e registra l'evento nello storico tracciato.
     */
    public function cambiaStatoQualifica(
        string $nuovoStato,
        ?string $motivo = null,
        ?int $userId = null,
        string $tipo = 'manuale',
        array $dettagli = []
    ): bool {
        $vecchioStato = $this->stato_qualifica;

        $this->stato_qualifica = $nuovoStato;
        if ($nuovoStato === 'qualificato' && !$this->data_qualifica) {
            $this->data_qualifica = now();
        }
        $saved = $this->save();

        if ($saved) {
            $this->storicoQualifica()->create([
                'user_id'          => $userId,
                'stato_precedente' => $vecchioStato,
                'stato_nuovo'      => $nuovoStato,
                'tipo_variazione'  => $tipo,
                'motivo'           => $motivo,
                'data_variazione'  => now(),
                'dettagli'         => !empty($dettagli) ? $dettagli : null,
            ]);
        }

        return $saved;
    }

    /**
     * Verifica se il fornitore è inattivo oltre la soglia configurata.
     * Ritorna true se è stato squalificato durante questo controllo.
     */
    public function verificaESqualificaInattivita(?int $userId = null): bool
    {
        // Se non è attualmente qualificato, non facciamo nulla
        if ($this->stato_qualifica !== 'qualificato') {
            return false;
        }

        $attiva = Impostazione::get('fornitori_squalifica_automatica_attiva', true);
        if (!$attiva) {
            return false;
        }

        $mesiSoglia = (int) Impostazione::get('fornitori_inattivita_mesi', 12);
        if ($mesiSoglia <= 0) {
            $mesiSoglia = 12;
        }

        $dataLimite = now()->subMonths($mesiSoglia);
        $ultimaData = $this->getUltimaDataAcquisto();

        // Se non ci sono mai stati acquisti registrati, controlliamo la data di qualifica o di creazione
        $dataRiferimento = $ultimaData ?? ($this->data_qualifica ? \Illuminate\Support\Carbon::parse($this->data_qualifica) : $this->created_at);

        if ($dataRiferimento && $dataRiferimento->lessThan($dataLimite)) {
            $nuovoStato = Impostazione::get('fornitori_stato_dopo_inattivita', 'non_qualificato');
            $dataUltimoStr = $ultimaData ? $ultimaData->format('d/m/Y') : 'nessun acquisto registrato (data qualifica: ' . ($this->data_qualifica ? $this->data_qualifica->format('d/m/Y') : $this->created_at->format('d/m/Y')) . ')';

            $motivo = "Squalifica automatica per inattività: nessun ordine/acquisto effettuato negli ultimi {$mesiSoglia} mesi. Riferimento ultimo acquisto: {$dataUltimoStr}.";

            $this->cambiaStatoQualifica(
                nuovoStato: $nuovoStato,
                motivo: $motivo,
                userId: $userId,
                tipo: 'automatica',
                dettagli: [
                    'soglia_mesi'      => $mesiSoglia,
                    'data_limite'      => $dataLimite->toIso8601String(),
                    'data_riferimento' => $dataRiferimento->toIso8601String(),
                    'aveva_acquisti'   => (bool) $ultimaData,
                ]
            );

            return true;
        }

        return false;
    }
}

