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
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $carroId = request()->route('carro');

        return [
            'modelo_id' => ['nullable', 'integer', 'exists:modelos,id'],
            'placa' => ['nullable', 'string', 'max:10', Rule::unique('carros', 'placa')->ignore($carroId)],
            'disponivel' => ['nullable', 'boolean'],
            'km' => ['nullable', 'integer'],
        ];
    }

    public function toArrayModel(): array
    {
        $data = [];

        if ($this->modeloId !== null) {
            $data['modelo_id'] = $this->modeloId;
        }

        if ($this->placa !== null) {
            $data['placa'] = $this->placa;
        }

        if ($this->disponivel !== null) {
            $data['disponivel'] = $this->disponivel;
        }

        if ($this->km !== null) {
            $data['km'] = $this->km;
        }

        return $data;
    }
}
