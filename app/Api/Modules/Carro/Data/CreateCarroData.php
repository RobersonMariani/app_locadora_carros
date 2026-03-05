<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateCarroData extends Data
{
    public function __construct(
        public int $modeloId,
        public string $placa,
        public bool $disponivel,
        public int $km,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'modelo_id' => ['required', 'integer', 'exists:modelos,id'],
            'placa' => ['required', 'string', 'max:10', 'unique:carros,placa'],
            'disponivel' => ['required', 'boolean'],
            'km' => ['required', 'integer'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'modelo_id' => $this->modeloId,
            'placa' => $this->placa,
            'disponivel' => $this->disponivel,
            'km' => $this->km,
        ];
    }
}
