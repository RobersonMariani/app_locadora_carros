<?php

namespace App\Api\Modules\Modelo\Controllers;

use App\Api\Modules\Modelo\Data\CreateModeloData;
use App\Api\Modules\Modelo\Data\ModeloQueryData;
use App\Api\Modules\Modelo\Data\UpdateModeloData;
use App\Api\Modules\Modelo\Resources\ModeloResource;
use App\Api\Modules\Modelo\UseCases\CreateModeloUseCase;
use App\Api\Modules\Modelo\UseCases\DeleteModeloUseCase;
use App\Api\Modules\Modelo\UseCases\GetModelosUseCase;
use App\Api\Modules\Modelo\UseCases\GetModeloUseCase;
use App\Api\Modules\Modelo\UseCases\UpdateModeloUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function __construct(
        private readonly CreateModeloUseCase $createModeloUseCase,
        private readonly GetModeloUseCase $getModeloUseCase,
        private readonly GetModelosUseCase $getModelosUseCase,
        private readonly UpdateModeloUseCase $updateModeloUseCase,
        private readonly DeleteModeloUseCase $deleteModeloUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = ModeloQueryData::from($request);
        $modelos = $this->getModelosUseCase->execute($query);

        return response()->json(ModeloResource::collection($modelos));
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateModeloData::from($request);
        $modelo = $this->createModeloUseCase->execute($data);

        return response()->json(new ModeloResource($modelo), 201);
    }

    public function show(int|string $modelo): JsonResponse
    {
        $modeloModel = $this->getModeloUseCase->execute((int) $modelo);

        if ($modeloModel === null) {
            return response()->json(['erro' => 'Modelo pesquisado não existe'], 404);
        }

        return response()->json(new ModeloResource($modeloModel));
    }

    public function update(Request $request, int|string $modelo): JsonResponse
    {
        $modeloId = (int) $modelo;
        $modeloModel = $this->getModeloUseCase->execute($modeloId);

        if ($modeloModel === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        $data = UpdateModeloData::from($request);
        $modeloModel = $this->updateModeloUseCase->execute($modeloModel, $data);

        return response()->json(new ModeloResource($modeloModel));
    }

    public function destroy(int|string $modelo): JsonResponse
    {
        $modeloModel = $this->getModeloUseCase->execute((int) $modelo);

        if ($modeloModel === null) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        $this->deleteModeloUseCase->execute($modeloModel);

        return response()->json(['msg' => 'O modelo foi removido com sucesso'], 200);
    }
}
