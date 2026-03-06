<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\UseCases;

use App\Api\Modules\Alerta\Data\AlertaQueryData;
use App\Api\Modules\Alerta\Repositories\AlertaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAlertasUseCase
{
    public function __construct(
        private readonly AlertaRepository $alertaRepository,
    ) {}

    public function execute(AlertaQueryData $query): LengthAwarePaginator
    {
        return $this->alertaRepository->getAll($query);
    }
}
