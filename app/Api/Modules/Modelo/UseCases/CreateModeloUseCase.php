<?php

declare(strict_types=1);

namespace App\Api\Modules\Modelo\UseCases;

use App\Api\Modules\Modelo\Data\CreateModeloData;
use App\Api\Modules\Modelo\Repositories\ModeloRepository;
use App\Models\Modelo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateModeloUseCase
{
    public function __construct(
        private readonly ModeloRepository $repository,
    ) {}

    public function execute(CreateModeloData $data): Modelo
    {
        return DB::transaction(function () use ($data) {
            $imagemPath = Storage::disk('public')->putFile('imagens/modelos', $data->imagem);
            $dataArray = array_merge($data->toArrayModel(), ['imagem' => $imagemPath]);

            return $this->repository->create($dataArray);
        });
    }
}
