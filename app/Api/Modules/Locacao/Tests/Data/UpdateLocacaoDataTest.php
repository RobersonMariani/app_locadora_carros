<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Data;

use App\Api\Modules\Locacao\Data\UpdateLocacaoData;
use App\Models\Carro;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class UpdateLocacaoDataTest extends TestCase
{
    use RefreshDatabase;

    private static function validPayload(Cliente $cliente, Carro $carro): array
    {
        return [
            'cliente_id' => $cliente->id,
            'carro_id' => $carro->id,
            'data_inicio_periodo' => '2024-01-01',
            'data_final_previsto_periodo' => '2024-01-10',
            'data_final_realizado_periodo' => '2024-01-09',
            'valor_diaria' => 150.50,
            'km_inicial' => 1000,
            'km_final' => 1500,
        ];
    }

    public static function validData(): array
    {
        return [
            'empty_payload' => ['empty_payload'],
            'single_field' => ['single_field'],
            'all_fields' => ['all_fields'],
            'valor_diaria_zero' => ['valor_diaria_zero'],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'cliente_id_not_exists' => ['cliente_id_not_exists', 'cliente_id'],
            'carro_id_not_exists' => ['carro_id_not_exists', 'carro_id'],
            'valor_diaria_negative' => ['valor_diaria_negative', 'valor_diaria'],
            'data_invalid_format' => ['data_invalid_format', 'data_inicio_periodo'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(string $case): void
    {
        // Arrange
        $cliente = Cliente::factory()->create();
        $marca = Marca::factory()->create();
        $modelo = Modelo::create([
            'marca_id' => $marca->id,
            'nome' => 'Modelo Test',
            'imagem' => 'imagem.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);
        $carro = Carro::create([
            'modelo_id' => $modelo->id,
            'placa' => 'ABC-'.fake()->unique()->numerify('####'),
            'disponivel' => true,
            'km' => 0,
            'cor' => 'Branco',
            'ano_fabricacao' => 2023,
            'ano_modelo' => 2024,
        ]);

        $payload = match ($case) {
            'empty_payload' => [],
            'single_field' => ['valor_diaria' => 200.00],
            'all_fields' => self::validPayload($cliente, $carro),
            'valor_diaria_zero' => ['valor_diaria' => 0],
            default => [],
        };

        // Act
        $result = UpdateLocacaoData::validateAndCreate($payload);

        // Assert
        $this->assertInstanceOf(UpdateLocacaoData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(string $case, string $expectedField): void
    {
        // Arrange
        $cliente = Cliente::factory()->create();
        $marca = Marca::factory()->create();
        $modelo = Modelo::create([
            'marca_id' => $marca->id,
            'nome' => 'Modelo Test',
            'imagem' => 'imagem.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);
        $carro = Carro::create([
            'modelo_id' => $modelo->id,
            'placa' => 'XYZ-'.fake()->unique()->numerify('####'),
            'disponivel' => true,
            'km' => 0,
            'cor' => 'Preto',
            'ano_fabricacao' => 2023,
            'ano_modelo' => 2024,
        ]);

        $invalidPayload = match ($case) {
            'cliente_id_not_exists' => ['cliente_id' => 99999],
            'carro_id_not_exists' => ['carro_id' => 99999],
            'valor_diaria_negative' => ['valor_diaria' => -1],
            'data_invalid_format' => ['data_inicio_periodo' => 'invalid-date'],
            default => [],
        };

        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            UpdateLocacaoData::validateAndCreate($invalidPayload);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
