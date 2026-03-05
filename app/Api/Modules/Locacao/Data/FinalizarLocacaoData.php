<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class FinalizarLocacaoData extends Data
{
    public function __construct(
        public int $kmFinal,
        public string $dataFinalRealizadoPeriodo,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'km_final' => ['required', 'integer', 'min:0'],
            'data_final_realizado_periodo' => ['required', 'date'],
        ];
    }

    public function toArray(): array
    {
        return [
            'km_final' => $this->kmFinal,
            'data_final_realizado_periodo' => $this->dataFinalRealizadoPeriodo,
        ];
    }
}
