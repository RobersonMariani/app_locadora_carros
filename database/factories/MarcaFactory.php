<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarcaFactory extends Factory
{
    protected $model = Marca::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->company(),
            'imagem' => 'imagens/marcas/'.fake()->uuid().'.png',
        ];
    }
}
