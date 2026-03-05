<?php

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetLocacaoUseCase
{
    public function __construct(
        private readonly LocacaoRepository $repository,
    ) {}

    public function execute(int $id): Locacao
    {
        $locacao = $this->repository->findById($id);

        if ($locacao === null) {
            throw new ModelNotFoundException;
        }

        return $locacao;
    }
}
