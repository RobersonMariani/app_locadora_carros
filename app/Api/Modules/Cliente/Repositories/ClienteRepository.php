<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\Repositories;

use App\Api\Modules\Cliente\Data\ClienteQueryData;
use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClienteRepository
{
    public function create(array $data): Cliente
    {
        return Cliente::query()->create($data);
    }

    public function findById(int $id): ?Cliente
    {
        return Cliente::query()->find($id);
    }

    public function getAllPaginated(ClienteQueryData $query): LengthAwarePaginator
    {
        return Cliente::query()
            ->when($query->search, fn ($q, $v) => $q->where('nome', 'like', "%{$v}%"))
            ->when($query->cpf, fn ($q, $v) => $q->where('cpf', 'like', "%{$v}%"))
            ->when($query->email, fn ($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->when($query->cidade, fn ($q, $v) => $q->where('cidade', 'like', "%{$v}%"))
            ->when($query->estado, fn ($q, $v) => $q->where('estado', $v))
            ->when($query->bloqueado !== null, fn ($q) => $q->where('bloqueado', $query->bloqueado))
            ->orderBy('id', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);

        return $cliente->refresh();
    }

    public function delete(Cliente $cliente): bool
    {
        return $cliente->delete();
    }
}
