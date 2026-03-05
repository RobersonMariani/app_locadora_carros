<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Pagamento\Enums\MetodoPagamentoEnum;
use App\Api\Modules\Pagamento\Enums\PagamentoTipoEnum;
use App\Models\Locacao;
use App\Models\Pagamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pagamento>
 */
class PagamentoFactory extends Factory
{
    protected $model = Pagamento::class;

    public function definition(): array
    {
        return [
            'locacao_id' => Locacao::factory(),
            'valor' => fake()->randomFloat(2, 50, 1000),
            'tipo' => fake()->randomElement(PagamentoTipoEnum::values()),
            'metodo_pagamento' => fake()->randomElement(MetodoPagamentoEnum::values()),
            'data_pagamento' => fake()->dateTimeBetween('-3 months', 'now'),
            'observacoes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
