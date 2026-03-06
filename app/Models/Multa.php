<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Multa\Enums\MultaStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Multa extends Model
{
    use HasFactory;

    protected $table = 'multas';

    protected $fillable = [
        'locacao_id',
        'carro_id',
        'cliente_id',
        'valor',
        'data_infracao',
        'descricao',
        'codigo_infracao',
        'pontos',
        'status',
        'data_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_infracao' => 'date',
        'data_pagamento' => 'date',
        'status' => MultaStatusEnum::class,
    ];

    public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class);
    }

    public function carro(): BelongsTo
    {
        return $this->belongsTo(Carro::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
