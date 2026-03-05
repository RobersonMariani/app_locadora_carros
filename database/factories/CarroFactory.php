<?php

declare(strict_types=1);

namespace Database\Factories;

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
        return [
            'modelo_id' => Modelo::factory(),
            'placa' => strtoupper(fake()->unique()->bothify('???#?##')),
            'disponivel' => true,
            'km' => fake()->numberBetween(0, 150000),
        ];
    }

    public function indisponivel(): static
    {
        return $this->state(fn (array $attributes) => [
            'disponivel' => false,
        ]);
    }
}
