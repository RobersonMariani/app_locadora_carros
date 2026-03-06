<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\UseCases;

use App\Api\Modules\Alerta\Repositories\AlertaRepository;

class MarcarTodosAlertasComoLidosUseCase
{
    public function __construct(
        private readonly AlertaRepository $alertaRepository,
    ) {}

    public function execute(): int
    {
        return $this->alertaRepository->marcarTodosComoLidos();
    }
}
