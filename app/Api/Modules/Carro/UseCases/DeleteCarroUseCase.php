<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\UseCases;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteCarroUseCase
{
    public function __construct(
        private readonly CarroRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        $carro = $this->repository->findById($id);

        if ($carro === null) {
            throw new ModelNotFoundException;
        }

        $this->repository->delete($carro);
    }
}
