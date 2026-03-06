<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use App\Models\Manutencao;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetManutencaoUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $repository,
    ) {}

    public function execute(int $id): Manutencao
    {
        $manutencao = $this->repository->findById($id);

        if ($manutencao === null) {
            throw new ModelNotFoundException;
        }

        return $manutencao;
    }
}
