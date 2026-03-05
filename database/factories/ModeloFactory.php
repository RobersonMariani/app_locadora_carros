<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Modelo>
 */
class ModeloFactory extends Factory
{
    protected $model = Modelo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marca_id' => Marca::factory(),
            'nome' => fake()->unique()->words(2, true),
            'imagem' => 'imagens/modelos/'.fake()->uuid().'.png',
            'numero_portas' => fake()->numberBetween(2, 5),
            'lugares' => fake()->numberBetween(2, 7),
            'air_bag' => fake()->boolean(),
            'abs' => fake()->boolean(),
        ];
    }
}
