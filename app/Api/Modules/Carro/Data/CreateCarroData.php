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
        public string $cor,
        public int $anoFabricacao,
        public int $anoModelo,
        public ?string $renavam = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $currentYear = (int) date('Y');

        return [
            'modelo_id' => ['required', 'integer', 'exists:modelos,id'],
            'placa' => ['required', 'string', 'max:10', 'unique:carros,placa'],
            'disponivel' => ['required', 'boolean'],
            'km' => ['required', 'integer'],
            'cor' => ['required', 'string', 'max:30'],
            'ano_fabricacao' => ['required', 'integer', 'min:1900', 'max:'.($currentYear + 1)],
            'ano_modelo' => ['required', 'integer', 'min:1900', 'max:'.($currentYear + 2)],
            'renavam' => ['nullable', 'string', 'max:30', 'unique:carros,renavam'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'modelo_id' => $this->modeloId,
            'placa' => $this->placa,
            'disponivel' => $this->disponivel,
            'km' => $this->km,
            'cor' => $this->cor,
            'ano_fabricacao' => $this->anoFabricacao,
            'ano_modelo' => $this->anoModelo,
            'renavam' => $this->renavam,
        ];
    }
}
