<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Tests\Data;

use App\Api\Modules\Multa\Data\MultaQueryData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('multa')]
class MultaQueryDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validData(): array
    {
        return [
            'empty_payload' => [[]],
            'with_search' => [['search' => 'excesso']],
            'with_status' => [['status' => 'pendente']],
            'with_data_infracao_de' => [['data_infracao_de' => '2024-01-01']],
            'with_data_infracao_ate' => [['data_infracao_ate' => '2024-12-31']],
            'with_page' => [['page' => 2]],
            'with_per_page' => [['per_page' => 20]],
            'search_max_length' => [['search' => str_repeat('a', 100)]],
            'all_filters' => [
                [
                    'search' => 'velocidade',
                    'status' => 'paga',
                    'data_infracao_de' => '2024-01-01',
                    'data_infracao_ate' => '2024-06-30',
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
            'status_invalid' => [['status' => 'invalido'], 'status'],
            'data_infracao_de_invalid' => [['data_infracao_de' => 'invalid'], 'data_infracao_de'],
            'data_infracao_ate_invalid' => [['data_infracao_ate' => 'invalid'], 'data_infracao_ate'],
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
        $result = MultaQueryData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(MultaQueryData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            MultaQueryData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
