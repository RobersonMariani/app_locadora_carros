<?php

namespace App\Api\Modules\Cliente\UseCases;

use App\Api\Modules\Cliente\Data\CreateClienteData;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

class CreateClienteUseCase
{
    public function __construct(
        private readonly ClienteRepository $repository,
    ) {}

    public function execute(CreateClienteData $data): Cliente
    {
        return DB::transaction(fn () => $this->repository->create($data->toArrayModel()));
    }
}
