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
class UpdateManutencaoData extends Data
{
    public function __construct(
        public ?int $carroId = null,
        public ?string $tipo = null,
        public ?string $descricao = null,
        public ?float $valor = null,
        public ?int $kmManutencao = null,
        public ?string $dataManutencao = null,
        public ?string $dataProxima = null,
        public ?string $fornecedor = null,
        public ?string $status = null,
        public ?string $observacoes = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $payload = $context->payload;
        $dataProximaRules = ['nullable', 'date'];

        if (isset($payload['data_manutencao'])) {
            $dataProximaRules[] = 'after:data_manutencao';
        }

        return [
            'carro_id' => ['nullable', 'integer', 'exists:carros,id'],
            'tipo' => ['nullable', 'string', 'max:20', Rule::in(ManutencaoTipoEnum::values())],
            'descricao' => ['nullable', 'string', 'max:255'],
            'valor' => ['nullable', 'numeric', 'min:0'],
            'km_manutencao' => ['nullable', 'integer', 'min:0'],
            'data_manutencao' => ['nullable', 'date'],
            'data_proxima' => $dataProximaRules,
            'fornecedor' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:20', Rule::in(ManutencaoStatusEnum::values())],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function toArrayModel(): array
    {
        return collect([
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
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
