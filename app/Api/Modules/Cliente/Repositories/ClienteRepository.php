<?php

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
