<?php

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Locacao\Data\UpdateLocacaoData;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Models\Locacao;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdateLocacaoUseCase
{
    public function __construct(
        private readonly LocacaoRepository $repository,
    ) {}

    public function execute(int $id, UpdateLocacaoData $data): Locacao
    {
        $locacao = $this->repository->findById($id);

        if ($locacao === null) {
            throw new ModelNotFoundException;
        }

        return DB::transaction(fn () => $this->repository->update($locacao, $data->toArrayModel()));
    }
}
