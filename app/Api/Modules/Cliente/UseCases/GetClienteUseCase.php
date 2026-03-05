<?php

namespace App\Api\Modules\Cliente\UseCases;

use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetClienteUseCase
{
    public function __construct(
        private readonly ClienteRepository $repository,
    ) {}

    public function execute(int $id): Cliente
    {
        $cliente = $this->repository->findById($id);

        if ($cliente === null) {
            throw new ModelNotFoundException;
        }

        return $cliente;
    }
}
