<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Data;

use App\Api\Modules\Vistoria\Enums\CombustivelNivelEnum;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateVistoriaData extends Data
{
    public function __construct(
        public int $locacaoId,
        public string $tipo,
        public string $combustivelNivel,
        public int $kmRegistrado,
        public ?string $observacoes = null,
        public string $dataVistoria = '',
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'locacao_id' => ['required', 'integer', 'exists:locacoes,id'],
            'tipo' => ['required', 'string', Rule::in(VistoriaTipoEnum::values())],
            'combustivel_nivel' => ['required', 'string', Rule::in(CombustivelNivelEnum::values())],
            'km_registrado' => ['required', 'integer', 'min:0'],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'data_vistoria' => ['required', 'date'],
        ];
    }

    public function toArrayModel(int $realizadoPor): array
    {
        return [
            'locacao_id' => $this->locacaoId,
            'tipo' => $this->tipo,
            'combustivel_nivel' => $this->combustivelNivel,
            'km_registrado' => $this->kmRegistrado,
            'observacoes' => $this->observacoes,
            'realizado_por' => $realizadoPor,
            'data_vistoria' => $this->dataVistoria,
        ];
    }
}
