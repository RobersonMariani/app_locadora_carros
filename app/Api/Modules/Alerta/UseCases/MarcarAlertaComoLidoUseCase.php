<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\UseCases;

use App\Api\Modules\Alerta\Repositories\AlertaRepository;
use App\Models\Alerta;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MarcarAlertaComoLidoUseCase
{
    public function __construct(
        private readonly AlertaRepository $alertaRepository,
    ) {}

    public function execute(int $id): Alerta
    {
        $alerta = $this->alertaRepository->findById($id);

        if ($alerta === null) {
            throw new ModelNotFoundException;
        }

        return $this->alertaRepository->marcarComoLido($id);
    }
}
