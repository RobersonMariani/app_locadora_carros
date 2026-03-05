<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Tests\Data;

use App\Api\Modules\Locacao\Data\LocacaoQueryData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('locacao')]
class LocacaoQueryDataTest extends TestCase
{
    use RefreshDatabase;

    public static function validData(): array
    {
        return [
            'empty_payload' => [[]],
            'with_search' => [['search' => 'test']],
            'with_page' => [['page' => 2]],
            'with_per_page' => [['per_page' => 20]],
            'search_max_length' => [['search' => str_repeat('a', 100)]],
        ];
    }

    public static function invalidData(): array
    {
        return [
            'search_too_long' => [['search' => str_repeat('a', 101)], 'search'],
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
        $result = LocacaoQueryData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(LocacaoQueryData::class, $result);
    }

    #[DataProvider('invalidData')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Act & Assert
        $this->expectException(ValidationException::class);

        try {
            LocacaoQueryData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
