<?php

declare(strict_types=1);

namespace App\Api\Modules\Multa\Controllers;

use App\Api\Modules\Multa\Data\CreateMultaData;
use App\Api\Modules\Multa\Data\MultaQueryData;
use App\Api\Modules\Multa\Data\UpdateMultaData;
use App\Api\Modules\Multa\Resources\MultaResource;
use App\Api\Modules\Multa\UseCases\CreateMultaUseCase;
use App\Api\Modules\Multa\UseCases\DeleteMultaUseCase;
use App\Api\Modules\Multa\UseCases\GetMultasByClienteUseCase;
use App\Api\Modules\Multa\UseCases\GetMultasByLocacaoUseCase;
use App\Api\Modules\Multa\UseCases\GetMultasUseCase;
use App\Api\Modules\Multa\UseCases\GetMultaUseCase;
use App\Api\Modules\Multa\UseCases\UpdateMultaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MultaController extends Controller
{
    public function __construct(
        private readonly CreateMultaUseCase $createMultaUseCase,
        private readonly GetMultaUseCase $getMultaUseCase,
        private readonly GetMultasUseCase $getMultasUseCase,
        private readonly GetMultasByLocacaoUseCase $getMultasByLocacaoUseCase,
        private readonly GetMultasByClienteUseCase $getMultasByClienteUseCase,
        private readonly UpdateMultaUseCase $updateMultaUseCase,
        private readonly DeleteMultaUseCase $deleteMultaUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = MultaQueryData::from($request);
        $multas = $this->getMultasUseCase->execute($query);

        return response()->json(MultaResource::collection($multas));
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateMultaData::from($request);
        $multa = $this->createMultaUseCase->execute($data);

        return response()->json(new MultaResource($multa), 201);
    }

    public function show(int|string $multa): JsonResponse
    {
        try {
            $multaModel = $this->getMultaUseCase->execute((int) $multa);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Multa pesquisada não existe'], 404);
        }

        return response()->json(new MultaResource($multaModel));
    }

    public function update(Request $request, int|string $multa): JsonResponse
    {
        try {
            $data = UpdateMultaData::from($request);
            $multaModel = $this->updateMultaUseCase->execute((int) $multa, $data);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        return response()->json(new MultaResource($multaModel));
    }

    public function destroy(int|string $multa): JsonResponse
    {
        try {
            $this->deleteMultaUseCase->execute((int) $multa);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        return response()->json(null, 204);
    }

    public function indexByLocacao(int $locacao, Request $request): JsonResponse
    {
        $query = MultaQueryData::from($request->query());
        $multas = $this->getMultasByLocacaoUseCase->execute($locacao, $query);

        return response()->json(MultaResource::collection($multas));
    }

    public function indexByCliente(int $cliente, Request $request): JsonResponse
    {
        $query = MultaQueryData::from($request->query());
        $multas = $this->getMultasByClienteUseCase->execute($cliente, $query);

        return response()->json(MultaResource::collection($multas));
    }
}
