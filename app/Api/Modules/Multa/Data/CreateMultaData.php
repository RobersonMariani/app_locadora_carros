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
class CreateMultaData extends Data
{
    public function __construct(
        public int $locacaoId,
        public int $carroId,
        public int $clienteId,
        public float $valor,
        public string $dataInfracao,
        public string $descricao,
        public string $status,
        public ?string $codigoInfracao = null,
        public ?int $pontos = null,
        public ?string $dataPagamento = null,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'locacao_id' => ['required', 'integer', 'exists:locacoes,id'],
            'carro_id' => ['required', 'integer', 'exists:carros,id'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'data_infracao' => ['required', 'date'],
            'descricao' => ['required', 'string', 'max:255'],
            'codigo_infracao' => ['nullable', 'string', 'max:20'],
            'pontos' => ['nullable', 'integer', 'min:0', 'max:21'],
            'status' => ['required', 'string', Rule::in(MultaStatusEnum::values())],
            'data_pagamento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
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
        ];
    }
}
