<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Tests\Data;

use App\Api\Modules\Auth\Data\LoginData;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('auth')]
class LoginDataTest extends TestCase
{
    private static function validPayload(): array
    {
        return [
            'email' => 'user@example.com',
            'password' => 'password123',
        ];
    }

    public static function validDataProvider(): array
    {
        return [
            'all_required_fields' => [self::validPayload()],
            'email_valid_format' => [array_merge(self::validPayload(), ['email' => 'test@domain.com.br'])],
            'password_long' => [array_merge(self::validPayload(), ['password' => str_repeat('a', 100)])],
        ];
    }

    public static function invalidDataProvider(): array
    {
        return [
            'email_null' => [array_merge(self::validPayload(), ['email' => null]), 'email'],
            'email_empty' => [array_merge(self::validPayload(), ['email' => '']), 'email'],
            'email_invalid' => [array_merge(self::validPayload(), ['email' => 'not-an-email']), 'email'],
            'email_not_string' => [array_merge(self::validPayload(), ['email' => 123]), 'email'],
            'password_null' => [array_merge(self::validPayload(), ['password' => null]), 'password'],
            'password_empty' => [array_merge(self::validPayload(), ['password' => '']), 'password'],
            'password_not_string' => [array_merge(self::validPayload(), ['password' => 123]), 'password'],
            'email_missing' => [['password' => 'password123'], 'email'],
            'password_missing' => [['email' => 'user@example.com'], 'password'],
        ];
    }

    #[DataProvider('validDataProvider')]
    public function testShouldPassValidationWhenDataIsValid(array $validItem): void
    {
        // Arrange & Act
        $result = LoginData::validateAndCreate($validItem);

        // Assert
        $this->assertInstanceOf(LoginData::class, $result);
    }

    #[DataProvider('invalidDataProvider')]
    public function testShouldFailValidationWhenDataIsInvalid(array $invalidItem, string $expectedField): void
    {
        // Arrange & Act & Assert
        $this->expectException(ValidationException::class);

        try {
            LoginData::validateAndCreate($invalidItem);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($expectedField, $e->errors());

            throw $e;
        }
    }
}
