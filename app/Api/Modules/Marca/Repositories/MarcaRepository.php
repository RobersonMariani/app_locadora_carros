<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Repositories;

use App\Api\Modules\Marca\Data\MarcaQueryData;
use App\Models\Marca;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarcaRepository
{
    public function create(array $data): Marca
    {
        return Marca::query()->create($data);
    }

    public function findById(int $id): ?Marca
    {
        return Marca::query()->with('modelos')->find($id);
    }

    public function getAllPaginated(MarcaQueryData $query): LengthAwarePaginator
    {
        return Marca::query()
            ->when($query->search, fn ($q, $v) => $q->where('nome', 'like', "%{$v}%"))
            ->orderBy('id', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function update(Marca $marca, array $data): Marca
    {
        $marca->update($data);

        return $marca->refresh();
    }

    public function delete(Marca $marca): bool
    {
        return $marca->delete();
    }
}
