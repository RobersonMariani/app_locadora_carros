<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::factory(12)->create();

        Cliente::factory(3)->create([
            'bloqueado' => true,
            'motivo_bloqueio' => 'Inadimplência de locações anteriores.',
        ]);
    }
}
