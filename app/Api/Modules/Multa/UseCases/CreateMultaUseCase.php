<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\UseCases;

use App\Api\Modules\Multa\Data\CreateMultaData;
use App\Api\Modules\Multa\Repositories\MultaRepository;
use App\Models\Multa;
use Illuminate\Support\Facades\DB;

class CreateMultaUseCase
{
    public function __construct(
        private readonly MultaRepository $repository,
    ) {}

    public function execute(CreateMultaData $data): Multa
    {
        return DB::transaction(fn () => $this->repository->create($data->toArrayModel()));
    }
}
