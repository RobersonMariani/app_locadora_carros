<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Alerta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'titulo',
        'descricao',
        'referencia_type',
        'referencia_id',
        'lido',
        'data_alerta',
    ];

    protected $casts = [
        'tipo' => AlertaTipoEnum::class,
        'lido' => 'boolean',
        'data_alerta' => 'datetime',
    ];

    public function referencia(): MorphTo
    {
        return $this->morphTo();
    }
}
