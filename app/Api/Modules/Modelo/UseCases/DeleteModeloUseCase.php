<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\UseCases;

use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Models\Modelo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteModeloUseCase
{
    public function __construct(
        private readonly ModeloRepository $repository,
    ) {}

    public function execute(Modelo $modelo): bool
    {
        return DB::transaction(function () use ($modelo) {
            if ($modelo->imagem) {
                Storage::disk('public')->delete($modelo->imagem);
            }

            return $this->repository->delete($modelo);
        });
    }
}
