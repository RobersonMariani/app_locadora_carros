<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\UseCases;

use App\Api\Modules\Pagamento\Data\UpdatePagamentoData;
use App\Api\Modules\Pagamento\Repositories\PagamentoRepository;
use App\Models\Pagamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdatePagamentoUseCase
{
    public function __construct(
        private readonly PagamentoRepository $repository,
    ) {}

    public function execute(int $id, UpdatePagamentoData $data): Pagamento
    {
        return DB::transaction(function () use ($id, $data) {
            $toUpdate = $data->toArrayModel();

            if ($toUpdate === []) {
                $pagamento = $this->repository->findById($id);

                if ($pagamento === null) {
                    throw new ModelNotFoundException;
                }

                return $pagamento;
            }

            return $this->repository->update($id, $toUpdate);
        });
    }
}
