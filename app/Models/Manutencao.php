<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Api\Modules\Manutencao\Enums\ManutencaoTipoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manutencao extends Model
{
    use HasFactory;

    protected $table = 'manutencoes';

    protected $fillable = [
        'carro_id',
        'tipo',
        'descricao',
        'valor',
        'km_manutencao',
        'data_manutencao',
        'data_proxima',
        'fornecedor',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_manutencao' => 'date',
        'data_proxima' => 'date',
        'tipo' => ManutencaoTipoEnum::class,
        'status' => ManutencaoStatusEnum::class,
    ];

    public function carro(): BelongsTo
    {
        return $this->belongsTo(Carro::class);
    }
}
