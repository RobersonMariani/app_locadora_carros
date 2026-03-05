<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use App\Models\Locacao;
use App\Models\Pagamento;
use Illuminate\Database\Seeder;

class PagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $locacoesFinalizadas = Locacao::finalizada()->get();

        foreach ($locacoesFinalizadas as $locacao) {
            $valorTotal = (float) $locacao->valor_total;
            $quantidadePagamentos = fake()->numberBetween(1, 3);

            if ($valorTotal <= 0) {
                continue;
            }

            $valorPorPagamento = round($valorTotal / $quantidadePagamentos, 2);
            $resto = round($valorTotal - ($valorPorPagamento * $quantidadePagamentos), 2);

            for ($i = 0; $i < $quantidadePagamentos; $i++) {
                $valor = $valorPorPagamento + ($i === 0 ? $resto : 0);

                Pagamento::factory()->create([
                    'locacao_id' => $locacao->id,
                    'valor' => $valor,
                    'tipo' => PagamentoTipoEnum::DIARIA,
                    'data_pagamento' => fake()->dateTimeBetween(
                        $locacao->data_inicio_periodo,
                        $locacao->data_final_realizado_periodo ?? 'now',
                    ),
                ]);
            }
        }
    }
}
