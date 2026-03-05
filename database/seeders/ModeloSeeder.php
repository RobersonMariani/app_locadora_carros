<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Database\Seeder;

class ModeloSeeder extends Seeder
{
    /** @var array<string, list<array{nome: string, portas: int, lugares: int, air_bag: bool, abs: bool}>> */
    private array $modelosPorMarca = [
        'Toyota' => [
            ['nome' => 'Corolla', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Hilux', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Yaris', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Volkswagen' => [
            ['nome' => 'Gol', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Polo', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'T-Cross', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Chevrolet' => [
            ['nome' => 'Onix', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Tracker', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'S10', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Fiat' => [
            ['nome' => 'Argo', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Pulse', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Strada', 'portas' => 2, 'lugares' => 2, 'air_bag' => true, 'abs' => true],
        ],
        'Honda' => [
            ['nome' => 'Civic', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'HR-V', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Hyundai' => [
            ['nome' => 'HB20', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Creta', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Ford' => [
            ['nome' => 'Ranger', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Territory', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Renault' => [
            ['nome' => 'Kwid', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => false],
            ['nome' => 'Duster', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Nissan' => [
            ['nome' => 'Kicks', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Frontier', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
        'Jeep' => [
            ['nome' => 'Renegade', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
            ['nome' => 'Compass', 'portas' => 4, 'lugares' => 5, 'air_bag' => true, 'abs' => true],
        ],
    ];

    public function run(): void
    {
        foreach ($this->modelosPorMarca as $marcaNome => $modelos) {
            $marca = Marca::where('nome', $marcaNome)->first();

            if (! $marca) {
                continue;
            }

            foreach ($modelos as $modelo) {
                Modelo::factory()->create([
                    'marca_id' => $marca->id,
                    'nome' => $modelo['nome'],
                    'numero_portas' => $modelo['portas'],
                    'lugares' => $modelo['lugares'],
                    'air_bag' => $modelo['air_bag'],
                    'abs' => $modelo['abs'],
                ]);
            }
        }
    }
}
