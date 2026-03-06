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
class UpdateCarroData extends Data
{
    public function __construct(
        public ?int $modeloId = null,
        public ?string $placa = null,
        public ?bool $disponivel = null,
        public ?int $km = null,
        public ?string $cor = null,
        public ?int $anoFabricacao = null,
        public ?int $anoModelo = null,
        public ?string $renavam = null,
        public ?string $combustivel = null,
        public ?string $cambio = null,
        public ?string $categoria = null,
        public ?bool $arCondicionado = null,
        public ?float $diariaPadrao = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $carroId = request()->route('carro');
        $currentYear = (int) date('Y');

        return [
            'modelo_id' => ['nullable', 'integer', 'exists:modelos,id'],
            'placa' => ['nullable', 'string', 'max:10', Rule::unique('carros', 'placa')->ignore($carroId)],
            'disponivel' => ['nullable', 'boolean'],
            'km' => ['nullable', 'integer'],
            'cor' => ['nullable', 'string', 'max:30'],
            'ano_fabricacao' => ['nullable', 'integer', 'min:1900', 'max:'.($currentYear + 1)],
            'ano_modelo' => ['nullable', 'integer', 'min:1900', 'max:'.($currentYear + 2)],
            'renavam' => ['nullable', 'string', 'max:30', Rule::unique('carros', 'renavam')->ignore($carroId)],
            'combustivel' => ['nullable', 'string', Rule::in(CombustivelEnum::values())],
            'cambio' => ['nullable', 'string', Rule::in(CambioEnum::values())],
            'categoria' => ['nullable', 'string', Rule::in(CategoriaCarroEnum::values())],
            'ar_condicionado' => ['nullable', 'boolean'],
            'diaria_padrao' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function toArrayModel(): array
    {
        return collect([
            'modelo_id' => $this->modeloId,
            'placa' => $this->placa,
            'disponivel' => $this->disponivel,
            'km' => $this->km,
            'cor' => $this->cor,
            'ano_fabricacao' => $this->anoFabricacao,
            'ano_modelo' => $this->anoModelo,
            'renavam' => $this->renavam,
            'combustivel' => $this->combustivel,
            'cambio' => $this->cambio,
            'categoria' => $this->categoria,
            'ar_condicionado' => $this->arCondicionado,
            'diaria_padrao' => $this->diariaPadrao,
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
