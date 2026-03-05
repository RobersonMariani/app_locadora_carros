<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use Illuminate\Database\Seeder;

class LocacaoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();
        $carros = Carro::all();

        Locacao::factory(8)->finalizada()->create([
            'cliente_id' => fn () => $clientes->random()->id,
            'carro_id' => fn () => $carros->random()->id,
        ]);

        $carrosDisponiveis = Carro::where('disponivel', true)->take(3)->get();

        foreach ($carrosDisponiveis as $carro) {
            Locacao::factory()->create([
                'cliente_id' => $clientes->random()->id,
                'carro_id' => $carro->id,
            ]);

            $carro->update(['disponivel' => false]);
        }
    }
}
