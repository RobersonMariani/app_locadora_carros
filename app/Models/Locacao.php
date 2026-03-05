<?php

declare(strict_types=1);

namespace App\Models;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locacao extends Model
{
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'cliente_id',
        'carro_id',
        'status',
        'data_inicio_periodo',
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'valor_total',
        'km_inicial',
        'km_final',
        'observacoes',
    ];

    protected $casts = [
        'status' => LocacaoStatusEnum::class,
        'data_inicio_periodo' => 'date',
        'data_final_previsto_periodo' => 'date',
        'data_final_realizado_periodo' => 'date',
        'valor_total' => 'decimal:2',
    ];

    public function scopeAtiva(Builder $query): Builder
    {
        return $query->where('status', LocacaoStatusEnum::ATIVA);
    }

    public function scopeFinalizada(Builder $query): Builder
    {
        return $query->where('status', LocacaoStatusEnum::FINALIZADA);
    }

    public function scopeReservada(Builder $query): Builder
    {
        return $query->where('status', LocacaoStatusEnum::RESERVADA);
    }

    public function scopePorPeriodo(Builder $query, string $inicio, string $fim): Builder
    {
        return $query->whereDate('data_inicio_periodo', '>=', $inicio)
            ->whereDate('data_inicio_periodo', '<=', $fim);
    }

    public function getDuracaoEmDias(): int
    {
        $inicio = $this->data_inicio_periodo;
        $fim = $this->data_final_realizado_periodo ?? now();

        return (int) $inicio->diffInDays($fim);
    }

    public function getValorPrevisto(): float
    {
        $duracaoPrevista = (int) $this->data_inicio_periodo->diffInDays($this->data_final_previsto_periodo);

        return (float) ($duracaoPrevista * (float) $this->valor_diaria);
    }

    public function isFinalizada(): bool
    {
        return $this->status === LocacaoStatusEnum::FINALIZADA;
    }

    public function isAtiva(): bool
    {
        return $this->status === LocacaoStatusEnum::ATIVA;
    }

    public function isReservada(): bool
    {
        return $this->status === LocacaoStatusEnum::RESERVADA;
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function carro(): BelongsTo
    {
        return $this->belongsTo(Carro::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }
}
