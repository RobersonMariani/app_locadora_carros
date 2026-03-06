<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Repositories;

use App\Api\Modules\Multa\Data\MultaQueryData;
use App\Models\Multa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MultaRepository
{
    public function create(array $data): Multa
    {
        return Multa::query()->create($data);
    }

    public function findById(int $id): ?Multa
    {
        return Multa::query()
            ->with(['locacao', 'carro', 'cliente'])
            ->find($id);
    }

    public function getAllPaginated(MultaQueryData $query): LengthAwarePaginator
    {
        return Multa::query()
            ->with(['locacao:id,cliente_id,carro_id', 'carro:id,placa', 'cliente:id,nome'])
            ->when($query->search, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('descricao', 'like', "%{$v}%")
                    ->orWhere('codigo_infracao', 'like', "%{$v}%");
            }))
            ->when($query->status, fn ($q, $v) => $q->where('status', $v))
            ->when($query->dataInfracaoDe, fn ($q, $v) => $q->whereDate('data_infracao', '>=', $v))
            ->when($query->dataInfracaoAte, fn ($q, $v) => $q->whereDate('data_infracao', '<=', $v))
            ->orderBy('data_infracao', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function getByLocacao(int $locacaoId, ?MultaQueryData $query = null): LengthAwarePaginator|Collection
    {
        $builder = Multa::query()
            ->with(['carro:id,placa', 'cliente:id,nome'])
            ->where('locacao_id', $locacaoId)
            ->orderBy('data_infracao', 'desc');

        if ($query !== null) {
            $builder->when($query->search, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('descricao', 'like', "%{$v}%")
                    ->orWhere('codigo_infracao', 'like', "%{$v}%");
            }));
            $builder->when($query->status, fn ($q, $v) => $q->where('status', $v));
            $builder->when($query->dataInfracaoDe, fn ($q, $v) => $q->whereDate('data_infracao', '>=', $v));
            $builder->when($query->dataInfracaoAte, fn ($q, $v) => $q->whereDate('data_infracao', '<=', $v));

            return $builder->paginate(perPage: $query->perPage, page: $query->page);
        }

        return $builder->limit(100)->get();
    }

    public function getByCliente(int $clienteId, ?MultaQueryData $query = null): LengthAwarePaginator|Collection
    {
        $builder = Multa::query()
            ->with(['locacao:id,cliente_id,carro_id', 'carro:id,placa'])
            ->where('cliente_id', $clienteId)
            ->orderBy('data_infracao', 'desc');

        if ($query !== null) {
            $builder->when($query->search, fn ($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('descricao', 'like', "%{$v}%")
                    ->orWhere('codigo_infracao', 'like', "%{$v}%");
            }));
            $builder->when($query->status, fn ($q, $v) => $q->where('status', $v));
            $builder->when($query->dataInfracaoDe, fn ($q, $v) => $q->whereDate('data_infracao', '>=', $v));
            $builder->when($query->dataInfracaoAte, fn ($q, $v) => $q->whereDate('data_infracao', '<=', $v));

            return $builder->paginate(perPage: $query->perPage, page: $query->page);
        }

        return $builder->limit(100)->get();
    }

    public function update(Multa $multa, array $data): Multa
    {
        $multa->update($data);

        return $multa->refresh();
    }

    public function delete(Multa $multa): bool
    {
        return $multa->delete();
    }
}
