<?php

namespace App\Api\Modules\Marca\UseCases;

use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteMarcaUseCase
{
    public function __construct(
        private readonly MarcaRepository $repository,
    ) {}

    public function execute(Marca $marca): bool
    {
        return DB::transaction(function () use ($marca) {
            if ($marca->imagem) {
                Storage::disk('public')->delete($marca->imagem);
            }

            return $this->repository->delete($marca);
        });
    }
}
