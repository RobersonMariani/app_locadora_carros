<?php

declare(strict_types=1);

namespace App\Api\Modules\Cliente\UseCases;

use App\Api\Modules\Cliente\Data\UpdateClienteData;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateClienteUseCase
{
    public function __construct(
        private readonly ClienteRepository $repository,
    ) {}

    public function execute(int $id, UpdateClienteData $data): Cliente
    {
        $cliente = $this->repository->findById($id);

        if ($cliente === null) {
            throw new ModelNotFoundException;
        }

        return DB::transaction(fn () => $this->repository->update($cliente, $data->toArrayModel()));
    }
}
