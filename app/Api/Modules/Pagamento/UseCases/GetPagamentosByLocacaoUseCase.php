<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\UseCases;

use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use Illuminate\Support\Collection;

class GetPagamentosByLocacaoUseCase
{
    public function __construct(
        private readonly PagamentoRepository $repository,
    ) {}

    public function execute(int $locacaoId): Collection
    {
        return $this->repository->getByLocacao($locacaoId);
    }
}
