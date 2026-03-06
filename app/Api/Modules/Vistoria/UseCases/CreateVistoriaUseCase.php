<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\UseCases;

use App\Api\Modules\Vistoria\Data\CreateVistoriaData;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Api\Modules\Vistoria\Repositories\VistoriaRepository;
use App\Api\Modules\Vistoria\Services\VistoriaService;
use App\Models\Vistoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateVistoriaUseCase
{
    public function __construct(
        private readonly VistoriaRepository $repository,
        private readonly VistoriaService $vistoriaService,
    ) {}

    public function execute(CreateVistoriaData $data): Vistoria
    {
        return DB::transaction(function () use ($data) {
            $this->vistoriaService->validarCriacao(
                $data->locacaoId,
                VistoriaTipoEnum::from($data->tipo),
            );

            $realizadoPor = (int) Auth::guard('api')->id();

            return $this->repository->create($data->toArrayModel($realizadoPor));
        });
    }
}
