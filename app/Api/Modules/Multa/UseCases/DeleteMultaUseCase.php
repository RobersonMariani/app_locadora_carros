<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\UseCases;

use App\Api\Modules\Multa\Repositories\MultaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteMultaUseCase
{
    public function __construct(
        private readonly MultaRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        $multa = $this->repository->findById($id);

        if ($multa === null) {
            throw new ModelNotFoundException;
        }

        $this->repository->delete($multa);
    }
}
