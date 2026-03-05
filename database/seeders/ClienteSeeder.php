<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            'João Silva',
            'Maria Oliveira',
            'Carlos Santos',
            'Ana Costa',
            'Pedro Souza',
            'Juliana Lima',
            'Fernando Alves',
            'Patrícia Ferreira',
            'Lucas Pereira',
            'Camila Rocha',
        ];

        foreach ($clientes as $nome) {
            Cliente::factory()->create(['nome' => $nome]);
        }
    }
}
