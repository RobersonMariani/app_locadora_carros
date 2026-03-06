<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Data;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoStatusEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdatePagamentoData extends Data
{
    public function __construct(
        public ?int $locacaoId = null,
        public ?float $valor = null,
        public ?string $tipo = null,
        public ?string $status = null,
        public ?string $metodoPagamento = null,
        public ?string $dataPagamento = null,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'locacao_id' => ['nullable', 'integer', 'exists:locacoes,id'],
            'valor' => ['nullable', 'numeric', 'min:0.01'],
            'tipo' => ['nullable', 'string', Rule::in(PagamentoTipoEnum::values())],
            'status' => ['nullable', 'string', Rule::in(PagamentoStatusEnum::values())],
            'metodo_pagamento' => ['nullable', 'string', Rule::in(MetodoPagamentoEnum::values())],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toArrayModel(): array
    {
        $data = [];

        if ($this->locacaoId !== null) {
            $data['locacao_id'] = $this->locacaoId;
        }

        if ($this->valor !== null) {
            $data['valor'] = $this->valor;
        }

        if ($this->tipo !== null) {
            $data['tipo'] = $this->tipo;
        }

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        if ($this->metodoPagamento !== null) {
            $data['metodo_pagamento'] = $this->metodoPagamento;
        }

        if ($this->dataPagamento !== null) {
            $data['data_pagamento'] = $this->dataPagamento;
        }

        if ($this->observacoes !== null) {
            $data['observacoes'] = $this->observacoes;
        }

        return $data;
    }
}
