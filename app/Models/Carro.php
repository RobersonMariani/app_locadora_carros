<?php

declare(strict_types=1);

namespace App\Models;

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
    ];

    protected $casts = [
        'disponivel' => 'boolean',
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
}
