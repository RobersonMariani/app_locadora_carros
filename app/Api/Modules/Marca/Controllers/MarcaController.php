<?php

declare(strict_types=1);

namespace App\Api\Modules\Marca\Controllers;

use App\Api\Modules\Marca\Data\CreateMarcaData;
use App\Api\Modules\Marca\Data\MarcaQueryData;
use App\Api\Modules\Marca\Data\UpdateMarcaData;
use App\Api\Modules\Marca\Resources\MarcaResource;
use App\Api\Modules\Marca\UseCases\CreateMarcaUseCase;
use App\Api\Modules\Marca\UseCases\DeleteMarcaUseCase;
use App\Api\Modules\Marca\UseCases\GetMarcasUseCase;
use App\Api\Modules\Marca\UseCases\GetMarcaUseCase;
use App\Api\Modules\Marca\UseCases\UpdateMarcaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct(
        private readonly CreateMarcaUseCase $createMarcaUseCase,
        private readonly GetMarcaUseCase $getMarcaUseCase,
        private readonly GetMarcasUseCase $getMarcasUseCase,
        private readonly UpdateMarcaUseCase $updateMarcaUseCase,
        private readonly DeleteMarcaUseCase $deleteMarcaUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = MarcaQueryData::from($request);
        $marcas = $this->getMarcasUseCase->execute($query);

        return response()->json(MarcaResource::collection($marcas));
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateMarcaData::from($request);
        $marca = $this->createMarcaUseCase->execute($data);

        return response()->json(new MarcaResource($marca), 201);
    }

    public function show(int|string $marca): JsonResponse
    {
        $marcaModel = $this->getMarcaUseCase->execute((int) $marca);

        if ($marcaModel === null) {
            return response()->json(['erro' => 'Marca pesquisada não existe'], 404);
        }

        return response()->json(new MarcaResource($marcaModel));
    }

    public function update(Request $request, int|string $marca): JsonResponse
    {
        $marcaId = (int) $marca;
        $marcaModel = $this->getMarcaUseCase->execute($marcaId);

        if ($marcaModel === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        $data = UpdateMarcaData::from($request);
        $marcaModel = $this->updateMarcaUseCase->execute($marcaModel, $data);

        return response()->json(new MarcaResource($marcaModel));
    }

    public function destroy(int|string $marca): JsonResponse
    {
        $marcaModel = $this->getMarcaUseCase->execute((int) $marca);

        if ($marcaModel === null) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        $this->deleteMarcaUseCase->execute($marcaModel);

        return response()->json(null, 204);
    }
}
