<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'cpf' => $this->maskCpf($this->cpf),
            'email' => $this->email,
            'telefone' => $this->telefone,
            'data_nascimento' => $this->data_nascimento?->format('Y-m-d'),
            'cnh' => $this->maskCnh($this->cnh),
            'endereco' => $this->endereco,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'cep' => $this->cep,
            'bloqueado' => $this->bloqueado ?? false,
            'motivo_bloqueio' => $this->motivo_bloqueio,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function maskCpf(?string $cpf): ?string
    {
        if ($cpf === null) {
            return null;
        }

        return '***.'.substr($cpf, 4, 7).'-**';
    }

    private function maskCnh(?string $cnh): ?string
    {
        if ($cnh === null) {
            return null;
        }

        return '***'.substr($cnh, 3, 5).'***';
    }
}
