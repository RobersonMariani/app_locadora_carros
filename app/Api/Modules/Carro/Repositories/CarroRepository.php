<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\Repositories;

use App\Api\Modules\Carro\Data\CarroQueryData;
use App\Models\Carro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarroRepository
{
    public function create(array $data): Carro
    {
        return Carro::query()->create($data);
    }

    public function findById(int $id): ?Carro
    {
        return Carro::query()->with('modelo')->find($id);
    }

    public function getAllPaginated(CarroQueryData $query): LengthAwarePaginator
    {
        return Carro::query()
            ->with('modelo')
            ->when($query->search, fn ($q, $v) => $q->where('placa', 'like', "%{$v}%"))
            ->orderBy('id', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function update(Carro $carro, array $data): Carro
    {
        $carro->update($data);

        return $carro->refresh();
    }

    public function delete(Carro $carro): bool
    {
        return $carro->delete();
    }
}
