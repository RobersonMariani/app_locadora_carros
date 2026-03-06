<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Data;

use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Api\Modules\Manutencao\Enums\ManutencaoTipoEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateManutencaoData extends Data
{
    public function __construct(
        public int $carroId,
        public string $tipo,
        public string $descricao,
        public float $valor,
        public int $kmManutencao,
        public string $dataManutencao,
        public string $status,
        public ?string $dataProxima = null,
        public ?string $fornecedor = null,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'carro_id' => ['required', 'integer', 'exists:carros,id'],
            'tipo' => ['required', 'string', 'max:20', Rule::in(ManutencaoTipoEnum::values())],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'km_manutencao' => ['required', 'integer', 'min:0'],
            'data_manutencao' => ['required', 'date'],
            'data_proxima' => ['nullable', 'date', 'after:data_manutencao'],
            'fornecedor' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:20', Rule::in(ManutencaoStatusEnum::values())],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'carro_id' => $this->carroId,
            'tipo' => $this->tipo,
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'km_manutencao' => $this->kmManutencao,
            'data_manutencao' => $this->dataManutencao,
            'data_proxima' => $this->dataProxima,
            'fornecedor' => $this->fornecedor,
            'status' => $this->status,
            'observacoes' => $this->observacoes,
        ];
    }
}
