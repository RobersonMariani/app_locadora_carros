<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Data;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreatePagamentoData extends Data
{
    public function __construct(
        public int $locacaoId,
        public float $valor,
        public string $tipo,
        public string $metodoPagamento,
        public string $dataPagamento,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'locacao_id' => ['required', 'integer', 'exists:locacoes,id'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'tipo' => ['required', 'string', Rule::in(PagamentoTipoEnum::values())],
            'metodo_pagamento' => ['required', 'string', Rule::in(MetodoPagamentoEnum::values())],
            'data_pagamento' => ['required', 'date'],
            'observacoes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'locacao_id' => $this->locacaoId,
            'valor' => $this->valor,
            'tipo' => $this->tipo,
            'metodo_pagamento' => $this->metodoPagamento,
            'data_pagamento' => $this->dataPagamento,
            'observacoes' => $this->observacoes,
        ];
    }
}
