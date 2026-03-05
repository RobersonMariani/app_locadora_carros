<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\UseCases;

use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Models\Marca;

class GetMarcaUseCase
{
    public function __construct(
        private readonly MarcaRepository $repository,
    ) {}

    public function execute(int $id): ?Marca
    {
        return $this->repository->findById($id);
    }
}
