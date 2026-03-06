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
            ->when($query->cor, fn ($q, $v) => $q->where('cor', $v))
            ->when($query->anoFabricacao !== null, fn ($q) => $q->where('ano_fabricacao', $query->anoFabricacao))
            ->when($query->disponivel !== null, fn ($q) => $q->where('disponivel', $query->disponivel))
            ->when($query->combustivel, fn ($q, $v) => $q->where('combustivel', $v))
            ->when($query->cambio, fn ($q, $v) => $q->where('cambio', $v))
            ->when($query->categoria, fn ($q, $v) => $q->where('categoria', $v))
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

    public function marcarDisponivel(int $id): void
    {
        Carro::query()->where('id', $id)->update(['disponivel' => true]);
    }

    public function marcarIndisponivel(int $id): void
    {
        Carro::query()->where('id', $id)->update(['disponivel' => false]);
    }
}
