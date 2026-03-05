<?php

namespace App\Api\Modules\Modelo\Repositories;

use App\Api\Modules\Modelo\Data\ModeloQueryData;
use App\Models\Modelo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModeloRepository
{
    public function create(array $data): Modelo
    {
        return Modelo::query()->create($data);
    }

    public function findById(int $id): ?Modelo
    {
        return Modelo::query()
            ->with('marca:id,nome,imagem')
            ->find($id);
    }

    public function getAllPaginated(ModeloQueryData $query): LengthAwarePaginator
    {
        return Modelo::query()
            ->with('marca:id,nome,imagem')
            ->when($query->search, fn ($q, $v) => $q->where('nome', 'like', "%{$v}%"))
            ->when($query->marcaId, fn ($q, $v) => $q->where('marca_id', $v))
            ->orderBy('id', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function update(Modelo $modelo, array $data): Modelo
    {
        $modelo->update($data);

        return $modelo->refresh();
    }

    public function delete(Modelo $modelo): bool
    {
        return $modelo->delete();
    }
}
