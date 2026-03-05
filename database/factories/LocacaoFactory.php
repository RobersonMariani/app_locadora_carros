<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Locacao;
use Carbon\Carbon;
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
            'status' => LocacaoStatusEnum::RESERVADA,
            'data_inicio_periodo' => $inicio,
            'data_final_previsto_periodo' => $previstoFim,
            'data_final_realizado_periodo' => null,
            'valor_diaria' => fake()->randomFloat(2, 80, 500),
            'valor_total' => null,
            'km_inicial' => $kmInicial,
            'km_final' => null,
            'observacoes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function ativa(): static
    {
        return $this->state(fn () => [
            'status' => LocacaoStatusEnum::ATIVA,
        ]);
    }

    public function finalizada(): static
    {
        return $this->state(function (array $attributes) {
            $previstoFim = $attributes['data_final_previsto_periodo'];
            $realizadoFim = fake()->dateTimeBetween($previstoFim, '+5 days');
            $kmFinal = $attributes['km_inicial'] + fake()->numberBetween(100, 5000);
            $valorDiaria = (float) $attributes['valor_diaria'];
            $inicio = Carbon::parse($attributes['data_inicio_periodo']);
            $duracaoDias = (int) $inicio->diffInDays(Carbon::parse($realizadoFim));
            $valorTotal = round($duracaoDias * $valorDiaria, 2);

            return [
                'status' => LocacaoStatusEnum::FINALIZADA,
                'data_final_realizado_periodo' => $realizadoFim,
                'km_final' => $kmFinal,
                'valor_total' => $valorTotal,
            ];
        });
    }
}
