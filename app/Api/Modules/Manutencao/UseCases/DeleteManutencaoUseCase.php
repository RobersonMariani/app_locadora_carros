<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\UseCases;

use App\Api\Modules\Manutencao\Repositories\ManutencaoRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteManutencaoUseCase
{
    public function __construct(
        private readonly ManutencaoRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        $manutencao = $this->repository->findById($id);

        if ($manutencao === null) {
            throw new ModelNotFoundException;
        }

        $this->repository->delete($manutencao);
    }
}
