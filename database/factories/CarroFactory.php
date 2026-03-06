<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Carro\Enums\CambioEnum;
use App\Api\Modules\Carro\Enums\CategoriaCarroEnum;
use App\Api\Modules\Carro\Enums\CombustivelEnum;
use App\Models\Carro;
use App\Models\Modelo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Carro>
 */
class CarroFactory extends Factory
{
    protected $model = Carro::class;

    public function definition(): array
    {
        $anoFabricacao = fake()->numberBetween(2018, 2025);

        return [
            'modelo_id' => Modelo::factory(),
            'placa' => strtoupper(fake()->unique()->bothify('???#?##')),
            'disponivel' => true,
            'km' => fake()->numberBetween(0, 150000),
            'cor' => fake()->randomElement(['Branco', 'Preto', 'Prata', 'Vermelho', 'Azul', 'Cinza']),
            'ano_fabricacao' => $anoFabricacao,
            'ano_modelo' => $anoFabricacao + fake()->randomElement([0, 1]),
            'renavam' => fake()->unique()->numerify('###########'),
            'combustivel' => fake()->randomElement(CombustivelEnum::values()),
            'cambio' => fake()->randomElement(CambioEnum::values()),
            'categoria' => fake()->randomElement(CategoriaCarroEnum::values()),
            'ar_condicionado' => true,
            'diaria_padrao' => fake()->randomFloat(2, 80, 500),
        ];
    }

    public function indisponivel(): static
    {
        return $this->state(fn (array $attributes) => [
            'disponivel' => false,
        ]);
    }
}
