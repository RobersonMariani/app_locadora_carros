<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Tests\Data;

use App\Api\Modules\Carro\Data\CarroQueryData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('carro')]
class CarroQueryDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validData(): array
    {
        return [
            'empty_payload' => [[]],
            'with_search' => [['search' => 'test']],
            'with_cor' => [['cor' => 'Branco']],
            'with_ano_fabricacao' => [['ano_fabricacao' => 2023]],
            'with_disponivel' => [['disponivel' => true]],
            'with_combustivel' => [['combustivel' => 'flex']],
            'with_cambio' => [['cambio' => 'automatico']],
            'with_categoria' => [['categoria' => 'sedan']],
            'with_page' => [['page' => 2]],
            'with_per_page' => [['per_page' => 20]],
            'search_max_length' => [['search' => str_repeat('a', 100)]],
            'all_filters' => [
                [
                    'search' => 'ABC',
                    'cor' => 'Preto',
                    'ano_fabricacao' => 2022,
                    'disponivel' => false,
                    'combustivel' => 'gasolina',
                    'cambio' => 'manual',
                    'categoria' => 'suv',
                    'page' => 1,
                    'per_page' => 10,
                ],
            ],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'search_too_long' => [['search' => str_repeat('a', 101)], 'search'],
            'cor_too_long' => [['cor' => str_repeat('a', 31)], 'cor'],
            'ano_fabricacao_below_min' => [['ano_fabricacao' => 1899], 'ano_fabricacao'],
            'disponivel_not_boolean' => [['disponivel' => 'yes'], 'disponivel'],
            'combustivel_invalid' => [['combustivel' => 'invalido'], 'combustivel'],
            'cambio_invalid' => [['cambio' => 'invalido'], 'cambio'],
            'categoria_invalid' => [['categoria' => 'invalido'], 'categoria'],
            'page_zero' => [['page' => 0], 'page'],
            'page_negative' => [['page' => -1], 'page'],
            'per_page_zero' => [['per_page' => 0], 'per_page'],
            'per_page_exceeds_max' => [['per_page' => 101], 'per_page'],
        ];
    }

    #[DataProvider('validData')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange & Act
        $result = CarroQueryData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(CarroQueryData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            CarroQueryData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
