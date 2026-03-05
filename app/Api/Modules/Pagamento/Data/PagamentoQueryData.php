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
class PagamentoQueryData extends Data
{
    public function __construct(
        public ?int $locacaoId = null,
        public ?string $tipo = null,
        public ?string $metodoPagamento = null,
        public ?string $dataPagamentoInicio = null,
        public ?string $dataPagamentoFim = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) (config('pagination.entities_per_page') ?? 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'locacao_id' => ['nullable', 'integer', 'exists:locacoes,id'],
            'tipo' => ['nullable', 'string', Rule::in(PagamentoTipoEnum::values())],
            'metodo_pagamento' => ['nullable', 'string', Rule::in(MetodoPagamentoEnum::values())],
            'data_pagamento_inicio' => ['nullable', 'date'],
            'data_pagamento_fim' => ['nullable', 'date', 'after_or_equal:data_pagamento_inicio'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
