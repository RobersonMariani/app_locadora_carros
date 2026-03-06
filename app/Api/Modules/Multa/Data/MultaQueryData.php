<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Data;

use App\Api\Modules\Multa\Enums\MultaStatusEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class MultaQueryData extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?string $status = null,
        public ?string $dataInfracaoDe = null,
        public ?string $dataInfracaoAte = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) (config('pagination.entities_per_page') ?? 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(MultaStatusEnum::values())],
            'data_infracao_de' => ['nullable', 'date'],
            'data_infracao_ate' => ['nullable', 'date'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
