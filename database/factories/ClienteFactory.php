<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->numerify('(##) #####-####'),
            'data_nascimento' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'cnh' => fake()->unique()->numerify('###########'),
            'endereco' => fake()->streetAddress(),
            'cidade' => fake()->city(),
            'estado' => fake()->randomElement(['SP', 'RJ', 'MG', 'BA', 'PR', 'RS', 'SC', 'PE', 'CE', 'DF', 'GO', 'ES', 'MT', 'MS', 'PA', 'PB', 'RN', 'AL', 'SE', 'TO', 'MA', 'PI', 'RO', 'RR', 'AM', 'AP', 'AC']),
            'cep' => fake()->numerify('#####-###'),
            'bloqueado' => false,
            'motivo_bloqueio' => null,
        ];
    }
}
