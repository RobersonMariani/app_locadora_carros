<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateClienteData extends Data
{
    public function __construct(
        public string $nome,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'nome' => ['required', 'string', 'max:30'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'nome' => $this->nome,
        ];
    }
}
