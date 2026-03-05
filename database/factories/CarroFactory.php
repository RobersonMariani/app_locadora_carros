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

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'modelo_id' => Modelo::factory(),
            'placa' => strtoupper(fake()->unique()->regexify('[A-Z]{3}[0-9]{4}')),
            'disponivel' => fake()->boolean(),
            'km' => fake()->numberBetween(0, 999999),
        ];
    }
}
