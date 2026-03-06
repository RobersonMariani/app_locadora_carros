<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Manutencao\Enums\ManutencaoStatusEnum;
use App\Api\Modules\Manutencao\Enums\ManutencaoTipoEnum;
use App\Models\Carro;
use App\Models\Manutencao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Manutencao>
 */
class ManutencaoFactory extends Factory
{
    protected $model = Manutencao::class;

    public function definition(): array
    {
        $dataManutencao = fake()->dateTimeBetween('-6 months', 'now');
        $dataProxima = fake()->optional(0.7)->dateTimeBetween($dataManutencao, '+1 year');

        return [
            'carro_id' => Carro::factory(),
            'tipo' => fake()->randomElement(ManutencaoTipoEnum::values()),
            'descricao' => fake()->sentence(4),
            'valor' => fake()->randomFloat(2, 100, 5000),
            'km_manutencao' => fake()->numberBetween(0, 200000),
            'data_manutencao' => $dataManutencao,
            'data_proxima' => $dataProxima,
            'fornecedor' => fake()->optional(0.8)->company(),
            'status' => fake()->randomElement(ManutencaoStatusEnum::values()),
            'observacoes' => fake()->optional(0.5)->paragraph(),
        ];
    }
}
