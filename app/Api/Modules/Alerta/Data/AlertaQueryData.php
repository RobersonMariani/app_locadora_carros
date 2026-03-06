<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Data;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class AlertaQueryData extends Data
{
    public function __construct(
        public ?string $tipo = null,
        public ?bool $lido = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) (config('pagination.entities_per_page') ?? 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'tipo' => ['nullable', 'string', Rule::in(AlertaTipoEnum::values())],
            'lido' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
