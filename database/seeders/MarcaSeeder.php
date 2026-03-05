<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            'Toyota',
            'Volkswagen',
            'Chevrolet',
            'Fiat',
            'Honda',
            'Hyundai',
            'Ford',
            'Renault',
            'Nissan',
            'Jeep',
        ];

        foreach ($marcas as $nome) {
            Marca::factory()->create(['nome' => $nome]);
        }
    }
}
