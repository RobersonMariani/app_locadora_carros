<?php

namespace App\Api\Modules\Marca\UseCases;

use App\Api\Modules\Marca\Data\UpdateMarcaData;
use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateMarcaUseCase
{
    public function __construct(
        private readonly MarcaRepository $repository,
    ) {}

    public function execute(Marca $marca, UpdateMarcaData $data): Marca
    {
        return DB::transaction(function () use ($marca, $data) {
            $updateData = $data->toArrayModel();

            if ($data->imagem !== null) {
                if ($marca->imagem) {
                    Storage::disk('public')->delete($marca->imagem);
                }
                $updateData['imagem'] = Storage::disk('public')->putFile('imagens/marcas', $data->imagem);
            }

            return $this->repository->update($marca, $updateData);
        });
    }
}
