<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locacao extends Model
{
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'cliente_id',
        'carro_id',
        'data_inicio_periodo',
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final',
    ];

    protected $casts = [
        'data_inicio_periodo' => 'date',
        'data_final_previsto_periodo' => 'date',
        'data_final_realizado_periodo' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function carro(): BelongsTo
    {
        return $this->belongsTo(Carro::class);
    }
}
