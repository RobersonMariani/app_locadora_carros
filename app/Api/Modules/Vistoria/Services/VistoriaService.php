<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Services;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Vistoria\Enums\VistoriaTipoEnum;
use App\Api\Modules\Vistoria\Repositories\VistoriaRepository;
use Illuminate\Validation\ValidationException;

class VistoriaService
{
    public function __construct(
        private readonly VistoriaRepository $vistoriaRepository,
        private readonly LocacaoRepository $locacaoRepository,
    ) {}

    public function validarCriacao(int $locacaoId, VistoriaTipoEnum $tipo): void
    {
        if ($this->locacaoRepository->findById($locacaoId) === null) {
            throw ValidationException::withMessages([
                'locacao_id' => ['Locação não encontrada.'],
            ]);
        }

        if ($tipo === VistoriaTipoEnum::RETIRADA) {
            if ($this->vistoriaRepository->hasVistoriaByTipo($locacaoId, VistoriaTipoEnum::RETIRADA)) {
                throw ValidationException::withMessages([
                    'tipo' => ['Esta locação já possui vistoria de retirada.'],
                ]);
            }
        }

        if ($tipo === VistoriaTipoEnum::DEVOLUCAO) {
            if (! $this->vistoriaRepository->hasVistoriaByTipo($locacaoId, VistoriaTipoEnum::RETIRADA)) {
                throw ValidationException::withMessages([
                    'tipo' => ['É necessário registrar a vistoria de retirada antes da vistoria de devolução.'],
                ]);
            }

            if ($this->vistoriaRepository->hasVistoriaByTipo($locacaoId, VistoriaTipoEnum::DEVOLUCAO)) {
                throw ValidationException::withMessages([
                    'tipo' => ['Esta locação já possui vistoria de devolução.'],
                ]);
            }
        }
    }
}
