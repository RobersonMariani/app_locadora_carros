<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\UseCases;

use App\Api\Modules\Dashboard\Repositories\DashboardRepository;

class GetFaturamentoUseCase
{
    public function __construct(
        private readonly DashboardRepository $repository,
    ) {}

    /**
     * @return array<int, array{periodo: string, faturamento: float, quantidade_locacoes: int}>
     */
    public function execute(string $periodo = 'mensal'): array
    {
        return $this->repository->getFaturamento($periodo);
    }
}
