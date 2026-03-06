<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Api\Modules\Alerta\Enums\AlertaTipoEnum;
use App\Models\Alerta;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alerta>
 */
class AlertaFactory extends Factory
{
    protected $model = Alerta::class;

    public function definition(): array
    {
        return [
            'tipo' => fake()->randomElement(AlertaTipoEnum::values()),
            'titulo' => fake()->sentence(3),
            'descricao' => fake()->sentence(6),
            'referencia_type' => 'App\Models\Locacao',
            'referencia_id' => Locacao::factory(),
            'lido' => fake()->boolean(30),
            'data_alerta' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function naoLido(): static
    {
        return $this->state(fn () => ['lido' => false]);
    }

    public function lido(): static
    {
        return $this->state(fn () => ['lido' => true]);
    }
}
