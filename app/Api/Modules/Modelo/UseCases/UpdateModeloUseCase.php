<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\UseCases;

use App\Api\Modules\Modelo\Data\UpdateModeloData;
use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Models\Modelo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateModeloUseCase
{
    public function __construct(
        private readonly ModeloRepository $repository,
    ) {}

    public function execute(Modelo $modelo, UpdateModeloData $data): Modelo
    {
        return DB::transaction(function () use ($modelo, $data) {
            $updateData = $data->toArrayModel();

            if ($data->imagem !== null) {
                if ($modelo->imagem) {
                    Storage::disk('public')->delete($modelo->imagem);
                }
                $updateData['imagem'] = Storage::disk('public')->putFile('imagens/modelos', $data->imagem);
            }

            return $this->repository->update($modelo, $updateData);
        });
    }
}
