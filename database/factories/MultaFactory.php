<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Multa\Enums\MultaStatusEnum;
use App\Models\Locacao;
use App\Models\Multa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Multa>
 */
class MultaFactory extends Factory
{
    protected $model = Multa::class;

    public function definition(): array
    {
        $locacao = Locacao::factory()->create();

        return [
            'locacao_id' => $locacao->id,
            'carro_id' => $locacao->carro_id,
            'cliente_id' => $locacao->cliente_id,
            'valor' => fake()->randomFloat(2, 50, 500),
            'data_infracao' => fake()->dateTimeBetween('-6 months', 'now'),
            'descricao' => fake()->sentence(6),
            'codigo_infracao' => fake()->optional(0.7)->numerify('########'),
            'pontos' => fake()->optional(0.6)->numberBetween(1, 21),
            'status' => fake()->randomElement(MultaStatusEnum::cases()),
            'data_pagamento' => fake()->optional(0.3)->dateTimeBetween('-3 months', 'now'),
            'observacoes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function forLocacao(Locacao $locacao): static
    {
        return $this->state(fn () => [
            'locacao_id' => $locacao->id,
            'carro_id' => $locacao->carro_id,
            'cliente_id' => $locacao->cliente_id,
        ]);
    }

    public function pendente(): static
    {
        return $this->state(fn () => ['status' => MultaStatusEnum::PENDENTE]);
    }

    public function paga(): static
    {
        return $this->state(fn () => ['status' => MultaStatusEnum::PAGA]);
    }
}
