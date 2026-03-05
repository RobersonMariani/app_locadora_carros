<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class CreateClienteData extends Data
{
    public function __construct(
        public string $nome,
        public string $cpf,
        public ?string $email = null,
        public ?string $telefone = null,
        public ?string $dataNascimento = null,
        public ?string $cnh = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'nome' => ['required', 'string', 'max:30'],
            'cpf' => ['required', 'string', 'max:14', 'unique:clientes,cpf'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:clientes,email'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'cnh' => ['nullable', 'string', 'max:20', 'unique:clientes,cnh'],
        ];
    }

    public function toArrayModel(): array
    {
        return [
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'data_nascimento' => $this->dataNascimento,
            'cnh' => $this->cnh,
        ];
    }
}
