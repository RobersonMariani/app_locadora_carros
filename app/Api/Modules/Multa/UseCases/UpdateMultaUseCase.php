<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\UseCases;

use App\Api\Modules\Multa\Data\UpdateMultaData;
use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Models\Multa;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateMultaUseCase
{
    public function __construct(
        private readonly MultaRepository $repository,
    ) {}

    public function execute(int $id, UpdateMultaData $data): Multa
    {
        $multa = $this->repository->findById($id);

        if ($multa === null) {
            throw new ModelNotFoundException;
        }

        $updateData = $data->toArrayModel();

        if (empty($updateData)) {
            return $multa;
        }

        return DB::transaction(fn () => $this->repository->update($multa, $updateData));
    }
}
