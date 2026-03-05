<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
class UpdateClienteData extends Data
{
    public function __construct(
        public ?string $nome = null,
        public ?string $cpf = null,
        public ?string $email = null,
        public ?string $telefone = null,
        public ?string $dataNascimento = null,
        public ?string $cnh = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $clienteId = request()->route('cliente');

        return [
            'nome' => ['nullable', 'string', 'max:30'],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('clientes', 'cpf')->ignore($clienteId)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($clienteId)],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'cnh' => ['nullable', 'string', 'max:20', Rule::unique('clientes', 'cnh')->ignore($clienteId)],
        ];
    }

    public function toArrayModel(): array
    {
        return collect([
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'data_nascimento' => $this->dataNascimento,
            'cnh' => $this->cnh,
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
