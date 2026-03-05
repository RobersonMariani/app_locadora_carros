<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Api\Modules\Locacao\Services\LocacaoService;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class IniciarLocacaoUseCase
{
    public function __construct(
        private readonly LocacaoRepository $locacaoRepository,
        private readonly LocacaoService $locacaoService,
    ) {}

    public function execute(int $id): Locacao
    {
        $locacao = $this->locacaoRepository->findById($id);

        if ($locacao === null) {
            throw new ModelNotFoundException;
        }

        return DB::transaction(fn () => $this->locacaoService->iniciarLocacao($locacao));
    }
}
