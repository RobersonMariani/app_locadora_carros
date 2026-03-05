<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\UseCases;

use App\Api\Modules\Carro\Data\UpdateCarroData;
use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Models\Carro;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateCarroUseCase
{
    public function __construct(
        private readonly CarroRepository $repository,
    ) {}

    public function execute(int $id, UpdateCarroData $data): Carro
    {
        $carro = $this->repository->findById($id);

        if ($carro === null) {
            throw new ModelNotFoundException;
        }

        $updateData = $data->toArrayModel();

        if (empty($updateData)) {
            return $carro;
        }

        return DB::transaction(fn () => $this->repository->update($carro, $updateData));
    }
}
