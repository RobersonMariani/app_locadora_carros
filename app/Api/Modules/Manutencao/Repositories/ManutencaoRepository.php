<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Repositories;

use App\Api\Modules\Manutencao\Data\ManutencaoQueryData;
use App\Models\Manutencao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ManutencaoRepository
{
    public function create(array $data): Manutencao
    {
        return Manutencao::query()->create($data);
    }

    public function findById(int $id): ?Manutencao
    {
        return Manutencao::query()->with('carro')->find($id);
    }

    public function getAllPaginated(ManutencaoQueryData $query): LengthAwarePaginator
    {
        return Manutencao::query()
            ->with('carro')
            ->when($query->search, fn ($q, $v) => $q->where('descricao', 'like', "%{$v}%"))
            ->when($query->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($query->status, fn ($q, $v) => $q->where('status', $v))
            ->when($query->carroId !== null, fn ($q) => $q->where('carro_id', $query->carroId))
            ->orderBy('data_manutencao', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function getByCarroId(int $carroId, ManutencaoQueryData $query): LengthAwarePaginator
    {
        return Manutencao::query()
            ->with('carro')
            ->where('carro_id', $carroId)
            ->when($query->search, fn ($q, $v) => $q->where('descricao', 'like', "%{$v}%"))
            ->when($query->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($query->status, fn ($q, $v) => $q->where('status', $v))
            ->orderBy('data_manutencao', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function getProximas(int $dias = 7): Collection
    {
        $limite = now()->addDays($dias)->endOfDay()->toDateString();

        return Manutencao::query()
            ->with('carro')
            ->whereNotNull('data_proxima')
            ->where('data_proxima', '<=', $limite)
            ->whereNotIn('status', ['concluida', 'cancelada'])
            ->orderBy('data_proxima', 'asc')
            ->get();
    }

    public function update(Manutencao $manutencao, array $data): Manutencao
    {
        $manutencao->update($data);

        return $manutencao->refresh();
    }

    public function delete(Manutencao $manutencao): bool
    {
        return $manutencao->delete();
    }
}
