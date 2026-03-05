<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Repositories;

use App\Api\Modules\Locacao\Data\LocacaoQueryData;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
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
            ->with(['cliente', 'carro.modelo.marca'])
            ->find($id);
    }

    public function getAllPaginated(LocacaoQueryData $query): LengthAwarePaginator
    {
        return Locacao::query()
            ->with(['cliente', 'carro.modelo.marca'])
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

    public function hasConflitoPeriodo(int $carroId, string $dataInicio, string $dataFim, ?int $excludeId = null): bool
    {
        $query = Locacao::query()
            ->where('carro_id', $carroId)
            ->whereNotIn('status', [LocacaoStatusEnum::FINALIZADA->value, LocacaoStatusEnum::CANCELADA->value])
            ->where(function ($q) use ($dataInicio, $dataFim) {
                $q->where(function ($sub) use ($dataInicio, $dataFim) {
                    $sub->where('data_inicio_periodo', '<=', $dataFim)
                        ->where('data_final_previsto_periodo', '>=', $dataInicio);
                });
            });

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function updateStatus(int $id, LocacaoStatusEnum $status): Locacao
    {
        $locacao = Locacao::query()->findOrFail($id);
        $locacao->update(['status' => $status->value]);

        return $locacao->refresh();
    }

    public function finalizar(int $id, array $dados): Locacao
    {
        $locacao = Locacao::query()->findOrFail($id);
        $locacao->update(array_merge($dados, ['status' => LocacaoStatusEnum::FINALIZADA->value]));

        return $locacao->refresh();
    }
}
