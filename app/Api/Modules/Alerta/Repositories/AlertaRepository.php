<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Repositories;

use App\Api\Modules\Alerta\Data\AlertaQueryData;
use App\Models\Alerta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AlertaRepository
{
    public function getAll(AlertaQueryData $query): LengthAwarePaginator
    {
        return Alerta::query()
            ->when($query->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($query->lido !== null, fn ($q) => $q->where('lido', $query->lido))
            ->orderBy('data_alerta', 'desc')
            ->paginate(perPage: $query->perPage, page: $query->page);
    }

    public function findById(int $id): ?Alerta
    {
        return Alerta::query()->find($id);
    }

    public function marcarComoLido(int $id): Alerta
    {
        $alerta = Alerta::query()->findOrFail($id);
        $alerta->update(['lido' => true]);

        return $alerta->refresh();
    }

    public function marcarTodosComoLidos(): int
    {
        return Alerta::query()->where('lido', false)->update(['lido' => true]);
    }

    public function create(array $data): Alerta
    {
        return Alerta::query()->create($data);
    }

    public function countNaoLidos(): int
    {
        return Alerta::query()->where('lido', false)->count();
    }

    public function existeAlertaHoje(string $tipo, string $referenciaType, int $referenciaId): bool
    {
        return Alerta::query()
            ->where('tipo', $tipo)
            ->where('referencia_type', $referenciaType)
            ->where('referencia_id', $referenciaId)
            ->whereDate('data_alerta', now()->toDateString())
            ->exists();
    }
}
