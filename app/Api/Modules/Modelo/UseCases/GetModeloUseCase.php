<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\UseCases;

use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Models\Modelo;

class GetModeloUseCase
{
    public function __construct(
        private readonly ModeloRepository $repository,
    ) {}

    public function execute(int $id): ?Modelo
    {
        return $this->repository->findById($id);
    }
}
