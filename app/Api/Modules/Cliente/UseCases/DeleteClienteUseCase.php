<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\UseCases;

use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteClienteUseCase
{
    public function __construct(
        private readonly ClienteRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        $cliente = $this->repository->findById($id);

        if ($cliente === null) {
            throw new ModelNotFoundException;
        }

        $this->repository->delete($cliente);
    }
}
