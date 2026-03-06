<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Data;

use App\Api\Modules\Multa\Enums\MultaStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateMultaData extends Data
{
    public function __construct(
        public ?int $locacaoId = null,
        public ?int $carroId = null,
        public ?int $clienteId = null,
        public ?float $valor = null,
        public ?string $dataInfracao = null,
        public ?string $descricao = null,
        public ?string $codigoInfracao = null,
        public ?int $pontos = null,
        public ?string $status = null,
        public ?string $dataPagamento = null,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'locacao_id' => ['nullable', 'integer', 'exists:locacoes,id'],
            'carro_id' => ['nullable', 'integer', 'exists:carros,id'],
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'valor' => ['nullable', 'numeric', 'min:0.01'],
            'data_infracao' => ['nullable', 'date'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'codigo_infracao' => ['nullable', 'string', 'max:20'],
            'pontos' => ['nullable', 'integer', 'min:0', 'max:21'],
            'status' => ['nullable', 'string', Rule::in(MultaStatusEnum::values())],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function toArrayModel(): array
    {
        return collect([
            'locacao_id' => $this->locacaoId,
            'carro_id' => $this->carroId,
            'cliente_id' => $this->clienteId,
            'valor' => $this->valor,
            'data_infracao' => $this->dataInfracao,
            'descricao' => $this->descricao,
            'codigo_infracao' => $this->codigoInfracao,
            'pontos' => $this->pontos,
            'status' => $this->status,
            'data_pagamento' => $this->dataPagamento,
            'observacoes' => $this->observacoes,
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
