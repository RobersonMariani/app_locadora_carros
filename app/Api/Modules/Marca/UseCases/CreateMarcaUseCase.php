<?php

namespace App\Api\Modules\Marca\UseCases;

use App\Api\Modules\Marca\Data\CreateMarcaData;
use App\Api\Modules\Marca\Repositories\MarcaRepository;
use App\Models\Marca;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateMarcaUseCase
{
    public function __construct(
        private readonly MarcaRepository $repository,
    ) {}

    public function execute(CreateMarcaData $data): Marca
    {
        return DB::transaction(function () use ($data) {
            $imagemPath = Storage::disk('public')->putFile('imagens/marcas', $data->imagem);
            $dataArray = array_merge($data->toArrayModel(), ['imagem' => $imagemPath]);

            return $this->repository->create($dataArray);
        });
    }
}
