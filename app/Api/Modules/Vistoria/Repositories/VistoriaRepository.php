<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Repositories;

use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Models\Vistoria;
use Illuminate\Support\Collection;

class VistoriaRepository
{
    public function create(array $data): Vistoria
    {
        return Vistoria::query()->create($data);
    }

    public function getByLocacao(int $locacaoId): Collection
    {
        return Vistoria::query()
            ->with(['realizadoPor:id,name', 'locacao:id,cliente_id,carro_id'])
            ->where('locacao_id', $locacaoId)
            ->orderBy('data_vistoria', 'asc')
            ->limit(100)
            ->get();
    }

    public function hasVistoriaByTipo(int $locacaoId, VistoriaTipoEnum $tipo): bool
    {
        return Vistoria::query()
            ->where('locacao_id', $locacaoId)
            ->where('tipo', $tipo->value)
            ->exists();
    }
}
