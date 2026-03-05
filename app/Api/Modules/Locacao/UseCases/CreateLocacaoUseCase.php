<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Locacao\Data\CreateLocacaoData;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use App\Models\Locacao;
use Illuminate\Support\Facades\DB;

class CreateLocacaoUseCase
{
    public function __construct(
        private readonly LocacaoRepository $repository,
    ) {}

    public function execute(CreateLocacaoData $data): Locacao
    {
        return DB::transaction(fn () => $this->repository->create($data->toArrayModel()));
    }
}
