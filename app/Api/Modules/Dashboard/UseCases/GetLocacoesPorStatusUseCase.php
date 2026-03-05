<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\UseCases;

use App\Api\Modules\Dashboard\Repositories\DashboardRepository;

class GetLocacoesPorStatusUseCase
{
    public function __construct(
        private readonly DashboardRepository $repository,
    ) {}

    /**
     * @return array<int, array{status: string, label: string, quantidade: int}>
     */
    public function execute(): array
    {
        return $this->repository->getLocacoesPorStatus();
    }
}
