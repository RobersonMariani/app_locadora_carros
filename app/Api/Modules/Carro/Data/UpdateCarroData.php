<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateCarroData extends Data
{
    public function __construct(
        public ?int $modeloId = null,
        public ?string $placa = null,
        public ?bool $disponivel = null,
        public ?int $km = null,
        public ?string $cor = null,
        public ?int $anoFabricacao = null,
        public ?int $anoModelo = null,
        public ?string $renavam = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $carroId = request()->route('carro');
        $currentYear = (int) date('Y');

        return [
            'modelo_id' => ['nullable', 'integer', 'exists:modelos,id'],
            'placa' => ['nullable', 'string', 'max:10', Rule::unique('carros', 'placa')->ignore($carroId)],
            'disponivel' => ['nullable', 'boolean'],
            'km' => ['nullable', 'integer'],
            'cor' => ['nullable', 'string', 'max:30'],
            'ano_fabricacao' => ['nullable', 'integer', 'min:1900', 'max:'.($currentYear + 1)],
            'ano_modelo' => ['nullable', 'integer', 'min:1900', 'max:'.($currentYear + 2)],
            'renavam' => ['nullable', 'string', 'max:30', Rule::unique('carros', 'renavam')->ignore($carroId)],
        ];
    }

    public function toArrayModel(): array
    {
        return collect([
            'modelo_id' => $this->modeloId,
            'placa' => $this->placa,
            'disponivel' => $this->disponivel,
            'km' => $this->km,
            'cor' => $this->cor,
            'ano_fabricacao' => $this->anoFabricacao,
            'ano_modelo' => $this->anoModelo,
            'renavam' => $this->renavam,
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
