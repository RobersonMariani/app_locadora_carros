<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\UseCases;

use App\Api\Modules\Vistoria\Repositories\VistoriaRepository;
use Illuminate\Support\Collection;

class GetVistoriasByLocacaoUseCase
{
    public function __construct(
        private readonly VistoriaRepository $repository,
    ) {}

    public function execute(int $locacaoId): Collection
    {
        return $this->repository->getByLocacao($locacaoId);
    }
}
