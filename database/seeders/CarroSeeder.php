<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Carro;
use App\Models\Modelo;
use Illuminate\Database\Seeder;

class CarroSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = Modelo::all();

        foreach ($modelos as $modelo) {
            $quantidade = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $quantidade; $i++) {
                Carro::factory()->create([
                    'modelo_id' => $modelo->id,
                ]);
            }
        }
    }
}
