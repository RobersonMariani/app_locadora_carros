<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoStatusEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'locacao_id',
        'valor',
        'tipo',
        'status',
        'metodo_pagamento',
        'data_pagamento',
        'observacoes',
    ];

    protected $casts = [
        'tipo' => PagamentoTipoEnum::class,
        'status' => PagamentoStatusEnum::class,
        'metodo_pagamento' => MetodoPagamentoEnum::class,
        'valor' => 'decimal:2',
        'data_pagamento' => 'date',
    ];

    public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class);
    }
}
