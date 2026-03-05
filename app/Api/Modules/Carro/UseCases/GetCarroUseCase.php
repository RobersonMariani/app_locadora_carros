<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\UseCases;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Models\Carro;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetCarroUseCase
{
    public function __construct(
        private readonly CarroRepository $repository,
    ) {}

    public function execute(int $id): Carro
    {
        $carro = $this->repository->findById($id);

        if ($carro === null) {
            throw new ModelNotFoundException;
        }

        return $carro;
    }
}
