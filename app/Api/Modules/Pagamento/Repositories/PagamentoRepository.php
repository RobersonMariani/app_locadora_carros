<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Repositories;

use App\Api\Modules\Pagamento\Data\PagamentoQueryData;
use App\Models\Pagamento;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PagamentoRepository
{
    public function create(array $data): Pagamento
    {
        return Pagamento::query()->create($data);
    }

    public function findById(int $id): ?Pagamento
    {
        return Pagamento::query()
            ->with('locacao:id,cliente_id,carro_id,status')
            ->find($id);
    }

    public function getAll(PagamentoQueryData $query): LengthAwarePaginator
    {
        return Pagamento::query()
            ->with('locacao:id,cliente_id,carro_id,status')
            ->when($query->locacaoId, fn ($q, $v) => $q->where('locacao_id', $v))
            ->when($query->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($query->status, fn ($q, $v) => $q->where('status', $v))
            ->when($query->metodoPagamento, fn ($q, $v) => $q->where('metodo_pagamento', $v))
            ->when($query->dataPagamentoInicio, fn ($q, $v) => $q->whereDate('data_pagamento', '>=', $v))
            ->when($query->dataPagamentoFim, fn ($q, $v) => $q->whereDate('data_pagamento', '<=', $v))
            ->orderBy('id', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function getByLocacao(int $locacaoId): Collection
    {
        return Pagamento::query()
            ->where('locacao_id', $locacaoId)
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();
    }

    public function update(int $id, array $data): Pagamento
    {
        $pagamento = Pagamento::query()->findOrFail($id);
        $pagamento->update($data);

        return $pagamento->refresh();
    }

    public function delete(int $id): void
    {
        Pagamento::query()->findOrFail($id)->delete();
    }
}
