<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
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

        // 5 locações finalizadas (status FINALIZADA, com valor_total)
        Locacao::factory(5)->finalizada()->create([
            'cliente_id' => fn () => $clientes->random()->id,
            'carro_id' => fn () => $carros->random()->id,
        ]);

        // 3 locações ativas (status ATIVA, carro indisponível)
        $carrosDisponiveis = Carro::where('disponivel', true)->take(3)->get();

        foreach ($carrosDisponiveis as $carro) {
            Locacao::factory()->ativa()->create([
                'cliente_id' => $clientes->random()->id,
                'carro_id' => $carro->id,
            ]);
            $carro->update(['disponivel' => false]);
        }

        // 2 locações reservadas (status RESERVADA)
        $carrosDisponiveis = Carro::where('disponivel', true)->take(2)->get();

        foreach ($carrosDisponiveis as $carro) {
            Locacao::factory()->create([
                'cliente_id' => $clientes->random()->id,
                'carro_id' => $carro->id,
                'status' => LocacaoStatusEnum::RESERVADA,
            ]);
        }
    }
}
