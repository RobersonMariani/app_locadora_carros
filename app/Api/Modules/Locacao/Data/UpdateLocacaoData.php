<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateLocacaoData extends Data
{
    public function __construct(
        public ?int $clienteId = null,
        public ?int $carroId = null,
        public ?string $dataInicioPeriodo = null,
        public ?string $dataFinalPrevistoPeriodo = null,
        public ?string $dataFinalRealizadoPeriodo = null,
        public ?float $valorDiaria = null,
        public ?int $kmInicial = null,
        public ?int $kmFinal = null,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'carro_id' => ['nullable', 'integer', 'exists:carros,id'],
            'data_inicio_periodo' => ['nullable', 'date'],
            'data_final_previsto_periodo' => ['nullable', 'date'],
            'data_final_realizado_periodo' => ['nullable', 'date'],
            'valor_diaria' => ['nullable', 'numeric', 'min:0'],
            'km_inicial' => ['nullable', 'integer'],
            'km_final' => ['nullable', 'integer'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    public function toArrayModel(): array
    {
        $data = [];

        if ($this->clienteId !== null) {
            $data['cliente_id'] = $this->clienteId;
        }

        if ($this->carroId !== null) {
            $data['carro_id'] = $this->carroId;
        }

        if ($this->dataInicioPeriodo !== null) {
            $data['data_inicio_periodo'] = $this->dataInicioPeriodo;
        }

        if ($this->dataFinalPrevistoPeriodo !== null) {
            $data['data_final_previsto_periodo'] = $this->dataFinalPrevistoPeriodo;
        }

        if ($this->dataFinalRealizadoPeriodo !== null) {
            $data['data_final_realizado_periodo'] = $this->dataFinalRealizadoPeriodo;
        }

        if ($this->valorDiaria !== null) {
            $data['valor_diaria'] = $this->valorDiaria;
        }

        if ($this->kmInicial !== null) {
            $data['km_inicial'] = $this->kmInicial;
        }

        if ($this->kmFinal !== null) {
            $data['km_final'] = $this->kmFinal;
        }

        if ($this->observacoes !== null) {
            $data['observacoes'] = $this->observacoes;
        }

        return $data;
    }
}
