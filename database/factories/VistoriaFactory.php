<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Vistoria\Enums\CombustivelNivelEnum;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Models\Locacao;
use App\Models\User;
use App\Models\Vistoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vistoria>
 */
class VistoriaFactory extends Factory
{
    protected $model = Vistoria::class;

    public function definition(): array
    {
        return [
            'locacao_id' => Locacao::factory(),
            'tipo' => fake()->randomElement(VistoriaTipoEnum::values()),
            'combustivel_nivel' => fake()->randomElement(CombustivelNivelEnum::values()),
            'km_registrado' => fake()->numberBetween(0, 200000),
            'observacoes' => fake()->optional(0.3)->sentence(),
            'realizado_por' => User::factory(),
            'data_vistoria' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
