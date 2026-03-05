<?php

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteLocacaoUseCase
{
    public function __construct(
        private readonly LocacaoRepository $repository,
    ) {}

    public function execute(int $id): void
    {
        $locacao = $this->repository->findById($id);

        if ($locacao === null) {
            throw new ModelNotFoundException;
        }

        $this->repository->delete($locacao);
    }
}
