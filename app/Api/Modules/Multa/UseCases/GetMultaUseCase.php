<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\UseCases;

use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Models\Multa;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetMultaUseCase
{
    public function __construct(
        private readonly MultaRepository $repository,
    ) {}

    public function execute(int $id): Multa
    {
        $multa = $this->repository->findById($id);

        if ($multa === null) {
            throw new ModelNotFoundException;
        }

        return $multa;
    }
}
