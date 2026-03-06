<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class ClienteQueryData extends Data
{
    public function __construct(
        public ?string $search = null,
        public ?string $cpf = null,
        public ?string $email = null,
        public ?string $cidade = null,
        public ?string $estado = null,
        public ?bool $bloqueado = null,
        public ?int $page = 1,
        public ?int $perPage = null,
    ) {
        $this->perPage ??= (int) config('pagination.entities_per_page', 15);
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'email' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:2'],
            'bloqueado' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
