<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateLocacaoData extends Data
{
    public function __construct(
        public int $clienteId,
        public int $carroId,
        public string $dataInicioPeriodo,
        public string $dataFinalPrevistoPeriodo,
        public ?string $dataFinalRealizadoPeriodo,
        public float $valorDiaria,
        public int $kmInicial,
        public ?int $kmFinal = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'carro_id' => ['required', 'integer', 'exists:carros,id'],
            'data_inicio_periodo' => ['required', 'date'],
            'data_final_previsto_periodo' => ['required', 'date', 'after:data_inicio_periodo'],
            'data_final_realizado_periodo' => ['nullable', 'date'],
            'valor_diaria' => ['required', 'numeric', 'min:0'],
            'km_inicial' => ['required', 'integer'],
            'km_final' => ['nullable', 'integer'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'cliente_id' => $this->clienteId,
            'carro_id' => $this->carroId,
            'data_inicio_periodo' => $this->dataInicioPeriodo,
            'data_final_previsto_periodo' => $this->dataFinalPrevistoPeriodo,
            'data_final_realizado_periodo' => $this->dataFinalRealizadoPeriodo,
            'valor_diaria' => $this->valorDiaria,
            'km_inicial' => $this->kmInicial,
            'km_final' => $this->kmFinal,
        ];
    }
}
