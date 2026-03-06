<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Manutencao\Data\ManutencaoQueryData;
use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetManutencoesByCarroUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $manutencaoRepository,
        private readonly CarroRepository $carroRepository,
    ) {}

    public function execute(int $carroId, ManutencaoQueryData $query): LengthAwarePaginator
    {
        $carro = $this->carroRepository->findById($carroId);

        if ($carro === null) {
            throw new ModelNotFoundException;
        }

        return $this->manutencaoRepository->getByCarroId($carroId, $query);
    }
}
