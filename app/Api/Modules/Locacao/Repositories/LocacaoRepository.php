<?php

namespace App\Api\Modules\Locacao\Repositories;

use App\Api\Modules\Locacao\Data\LocacaoQueryData;
use App\Models\Locacao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocacaoRepository
{
    public function create(array $data): Locacao
    {
        return Locacao::query()->create($data);
    }

    public function findById(int $id): ?Locacao
    {
        return Locacao::query()
            ->with(['cliente', 'carro'])
            ->find($id);
    }

    public function getAllPaginated(LocacaoQueryData $query): LengthAwarePaginator
    {
        return Locacao::query()
            ->with(['cliente', 'carro'])
            ->when($query->search, function ($q, $v) {
                $q->where(function ($query) use ($v) {
                    $query->whereHas('cliente', fn ($cliente) => $cliente->where('nome', 'like', "%{$v}%"))
                        ->orWhereHas('carro', fn ($carro) => $carro->where('placa', 'like', "%{$v}%"));
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function update(Locacao $locacao, array $data): Locacao
    {
        $locacao->update($data);

        return $locacao->refresh();
    }

    public function delete(Locacao $locacao): bool
    {
        return $locacao->delete();
    }
}
