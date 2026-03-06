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
class ManutencaoQueryData extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?string $tipo = null,
        public ?string $status = null,
        public ?int $carroId = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) (config('pagination.entities_per_page') ?? 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'string', Rule::in(ManutencaoTipoEnum::values())],
            'status' => ['nullable', 'string', Rule::in(ManutencaoStatusEnum::values())],
            'carro_id' => ['nullable', 'integer', 'exists:carros,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
