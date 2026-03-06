<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Carro\Repositories\CarroRepository;
use App\Api\Modules\Cliente\Repositories\ClienteRepository;
use App\Api\Modules\Locacao\Data\CreateLocacaoData;
use App\Api\Modules\Locacao\Enums\LocacaoStatusEnum;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Models\Locacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateLocacaoUseCase
{
    public function __construct(
        private readonly LocacaoRepository $repository,
        private readonly LocacaoService $locacaoService,
        private readonly CarroRepository $carroRepository,
        private readonly ClienteRepository $clienteRepository,
    ) {}

    public function execute(CreateLocacaoData $data): Locacao
    {
        return DB::transaction(function () use ($data) {
            $cliente = $this->clienteRepository->findById($data->clienteId);

            if ($cliente !== null && $cliente->isBloqueado()) {
                throw ValidationException::withMessages([
                    'cliente_id' => ['Cliente bloqueado não pode realizar locações.'],
                ]);
            }

            $this->locacaoService->validarDisponibilidade(
                $data->carroId,
                $data->dataInicioPeriodo,
                $data->dataFinalPrevistoPeriodo,
            );

            $locacao = $this->repository->create($data->toArrayModel());

            if ($locacao->status === LocacaoStatusEnum::ATIVA) {
                $this->carroRepository->marcarIndisponivel($locacao->carro_id);
            }

            return $locacao;
        });
    }
}
