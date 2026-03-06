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
        public ?string $endereco = null,
        public ?string $cidade = null,
        public ?string $estado = null,
        public ?string $cep = null,
        public ?bool $bloqueado = null,
        public ?string $motivoBloqueio = null,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        $clienteId = request()->route('cliente');

        return [
            'nome' => ['nullable', 'string', 'min:3', 'max:100'],
            'cpf' => ['nullable', 'string', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', Rule::unique('clientes', 'cpf')->ignore($clienteId)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($clienteId)],
            'telefone' => ['nullable', 'string', 'regex:/^\(\d{2}\)\s?\d{4,5}-\d{4}$/', 'max:20'],
            'data_nascimento' => ['nullable', 'date', 'before:today'],
            'cnh' => ['nullable', 'string', 'regex:/^\d{11}$/', Rule::unique('clientes', 'cnh')->ignore($clienteId)],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'regex:/^[A-Z]{2}$/'],
            'cep' => ['nullable', 'string', 'regex:/^\d{5}-\d{3}$/'],
            'bloqueado' => ['nullable', 'boolean'],
            'motivo_bloqueio' => ['nullable', 'string', 'max:255', 'required_if:bloqueado,true'],
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
            'endereco' => $this->endereco,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'cep' => $this->cep,
            'bloqueado' => $this->bloqueado,
            'motivo_bloqueio' => $this->motivoBloqueio,
        ])->filter(fn ($value) => $value !== null)->toArray();
    }
}
