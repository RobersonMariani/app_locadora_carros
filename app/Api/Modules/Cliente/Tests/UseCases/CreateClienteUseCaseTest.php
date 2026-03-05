<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Tests\UseCases;

use App\Api\Modules\Cliente\Data\CreateClienteData;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Cliente\UseCases\CreateClienteUseCase;
use App\Models\Cliente;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('cliente')]
class CreateClienteUseCaseTest extends TestCase
{
    public function testExecuteShouldReturnClienteWhenDataIsValid(): void
    {
        // Arrange
        $data = new CreateClienteData(
            nome: 'João Silva',
            cpf: '123.456.789-00',
        );
        $expectedResult = new Cliente(['id' => 1, 'nome' => 'João Silva']);

        $this->instance(
            ClienteRepository::class,
            Mockery::mock(ClienteRepository::class, function (MockInterface $mock) use ($expectedResult) {
                $mock->shouldReceive('create')
                    ->once()
                    ->andReturn($expectedResult);
            }),
        );

        // Act
        $useCase = app()->make(CreateClienteUseCase::class);
        $result = $useCase->execute($data);

        // Assert
        $this->assertInstanceOf(Cliente::class, $result);
        $this->assertEquals($expectedResult, $result);
    }
}
