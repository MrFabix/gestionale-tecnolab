<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DocumentoRevisione extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'documento_revisioni';

    protected $fillable = [
        'documento_id', 'numero_revisione', 'data_revisione',
        'redatto_da', 'motivo', 'note',
    ];

    protected $casts = [
        'data_revisione' => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}
