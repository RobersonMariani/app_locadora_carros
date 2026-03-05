<?php

declare(strict_types=1);

namespace App\Api\Modules\Carro\UseCases;

use App\Api\Modules\Carro\Data\CarroQueryData;
use App\Api\Modules\Carro\Repositories\CarroRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCarrosUseCase
{
    public function __construct(
        private readonly CarroRepository $repository,
    ) {}

    public function execute(CarroQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
