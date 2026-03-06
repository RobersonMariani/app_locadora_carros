<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Vistoria\Enums\CombustivelNivelEnum;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vistoria extends Model
{
    use HasFactory;

    protected $table = 'vistorias';

    protected $fillable = [
        'locacao_id',
        'tipo',
        'combustivel_nivel',
        'km_registrado',
        'observacoes',
        'realizado_por',
        'data_vistoria',
    ];

    protected $casts = [
        'tipo' => VistoriaTipoEnum::class,
        'combustivel_nivel' => CombustivelNivelEnum::class,
        'data_vistoria' => 'datetime',
    ];

    public function locacao(): BelongsTo
    {
        return $this->belongsTo(Locacao::class);
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}
