<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\UseCases;

use App\Api\Modules\Dashboard\Repositories\DashboardRepository;

class GetResumoUseCase
{
    public function __construct(
        private readonly DashboardRepository $repository,
    ) {}

    /**
     * @return array<string, int|float>
     */
    public function execute(): array
    {
        return $this->repository->getResumo();
    }
}
