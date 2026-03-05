<?php

namespace App\Api\Modules\Locacao\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class LocacaoQueryData extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) (config('pagination.entities_per_page') ?? 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
