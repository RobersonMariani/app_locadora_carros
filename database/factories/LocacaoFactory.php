<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Locacao>
 */
class LocacaoFactory extends Factory
{
    protected $model = Locacao::class;

    public function definition(): array
    {
        $kmInicial = fake()->numberBetween(10000, 100000);
        $inicio = fake()->dateTimeBetween('-3 months', '-1 week');
        $previstoFim = fake()->dateTimeBetween($inicio->format('Y-m-d').' +1 day', $inicio->format('Y-m-d').' +30 days');

        return [
            'cliente_id' => Cliente::factory(),
            'carro_id' => Carro::factory(),
            'data_inicio_periodo' => $inicio,
            'data_final_previsto_periodo' => $previstoFim,
            'data_final_realizado_periodo' => null,
            'valor_diaria' => fake()->randomFloat(2, 80, 500),
            'km_inicial' => $kmInicial,
            'km_final' => null,
        ];
    }

    public function finalizada(): static
    {
        return $this->state(function (array $attributes) {
            $previstoFim = $attributes['data_final_previsto_periodo'];
            $realizadoFim = fake()->dateTimeBetween($previstoFim, '+5 days');
            $kmFinal = $attributes['km_inicial'] + fake()->numberBetween(100, 5000);

            return [
                'data_final_realizado_periodo' => $realizadoFim,
                'km_final' => $kmFinal,
            ];
        });
    }
}
