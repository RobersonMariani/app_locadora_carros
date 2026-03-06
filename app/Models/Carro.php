<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Carro\Enums\CambioEnum;
use App\Api\Modules\Carro\Enums\CategoriaCarroEnum;
use App\Api\Modules\Carro\Enums\CombustivelEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carro extends Model
{
    use HasFactory;

    protected $fillable = [
        'modelo_id',
        'placa',
        'cor',
        'ano_fabricacao',
        'ano_modelo',
        'renavam',
        'disponivel',
        'km',
        'combustivel',
        'cambio',
        'categoria',
        'ar_condicionado',
        'diaria_padrao',
    ];

    protected $casts = [
        'disponivel' => 'boolean',
        'ar_condicionado' => 'boolean',
        'combustivel' => CombustivelEnum::class,
        'cambio' => CambioEnum::class,
        'categoria' => CategoriaCarroEnum::class,
        'diaria_padrao' => 'decimal:2',
    ];

    public function scopeDisponivel(Builder $query): Builder
    {
        return $query->where('disponivel', true);
    }

    public function scopeIndisponivel(Builder $query): Builder
    {
        return $query->where('disponivel', false);
    }

    public function isDisponivel(): bool
    {
        return (bool) $this->disponivel;
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class);
    }

    public function locacoes(): HasMany
    {
        return $this->hasMany(Locacao::class);
    }

    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class);
    }

    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class);
    }
}
