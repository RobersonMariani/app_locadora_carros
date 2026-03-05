<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\UseCases;

use App\Api\Modules\Carro\Data\CreateCarroData;
use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Models\Carro;
use Illuminate\Support\Facades\DB;

class CreateCarroUseCase
{
    public function __construct(
        private readonly CarroRepository $repository,
    ) {}

    public function execute(CreateCarroData $data): Carro
    {
        return DB::transaction(fn () => $this->repository->create($data->toArrayModel()));
    }
}
