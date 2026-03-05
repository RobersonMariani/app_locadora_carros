<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\UseCases;

use App\Api\Modules\Locacao\Data\LocacaoQueryData;
use App\Api\Modules\Locacao\Repositories\LocacaoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetLocacoesUseCase
{
    public function __construct(
        private readonly LocacaoRepository $repository,
    ) {}

    public function execute(LocacaoQueryData $query): LengthAwarePaginator
    {
        return $this->repository->getAllPaginated($query);
    }
}
