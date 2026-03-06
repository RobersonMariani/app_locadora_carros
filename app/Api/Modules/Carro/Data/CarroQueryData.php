<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Data;

use App\Api\Modules\Carro\Enums\CambioEnum;
use App\Api\Modules\Carro\Enums\CategoriaCarroEnum;
use App\Api\Modules\Carro\Enums\CombustivelEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CarroQueryData extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?string $cor = null,
        public ?int $anoFabricacao = null,
        public ?bool $disponivel = null,
        public ?string $combustivel = null,
        public ?string $cambio = null,
        public ?string $categoria = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) (config('pagination.entities_per_page') ?? 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'cor' => ['nullable', 'string', 'max:30'],
            'ano_fabricacao' => ['nullable', 'integer', 'min:1900'],
            'disponivel' => ['nullable', 'boolean'],
            'combustivel' => ['nullable', 'string', Rule::in(CombustivelEnum::values())],
            'cambio' => ['nullable', 'string', Rule::in(CambioEnum::values())],
            'categoria' => ['nullable', 'string', Rule::in(CategoriaCarroEnum::values())],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
