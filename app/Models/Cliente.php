<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'telefone',
        'data_nascimento',
        'cnh',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'bloqueado',
        'motivo_bloqueio',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'bloqueado' => 'boolean',
    ];

    public function isBloqueado(): bool
    {
        return (bool) $this->bloqueado;
    }

    public function scopeBloqueado(Builder $query): Builder
    {
        return $query->where('bloqueado', true);
    }

    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('bloqueado', false);
    }

    public function locacoes(): HasMany
    {
        return $this->hasMany(Locacao::class);
    }

    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class);
    }
}
