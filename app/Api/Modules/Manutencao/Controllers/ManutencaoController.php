<?php

declare(strict_types=1);

namespace App\Api\Modules\Manutencao\Controllers;

use App\Api\Modules\Manutencao\Data\CreateManutencaoData;
use App\Api\Modules\Manutencao\Data\ManutencaoQueryData;
use App\Api\Modules\Manutencao\Data\UpdateManutencaoData;
use App\Api\Modules\Manutencao\Resources\ManutencaoResource;
use App\Api\Modules\Manutencao\UseCases\CreateManutencaoUseCase;
use App\Api\Modules\Manutencao\UseCases\DeleteManutencaoUseCase;
use App\Api\Modules\Manutencao\UseCases\GetManutencaoUseCase;
use App\Api\Modules\Manutencao\UseCases\GetManutencoesByCarroUseCase;
use App\Api\Modules\Manutencao\UseCases\GetManutencoesProximasUseCase;
use App\Api\Modules\Manutencao\UseCases\GetManutencoesUseCase;
use App\Api\Modules\Manutencao\UseCases\UpdateManutencaoUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManutencaoController extends Controller
{
    public function __construct(
        private readonly CreateManutencaoUseCase $createManutencaoUseCase,
        private readonly GetManutencaoUseCase $getManutencaoUseCase,
        private readonly GetManutencoesUseCase $getManutencoesUseCase,
        private readonly GetManutencoesByCarroUseCase $getManutencoesByCarroUseCase,
        private readonly GetManutencoesProximasUseCase $getManutencoesProximasUseCase,
        private readonly UpdateManutencaoUseCase $updateManutencaoUseCase,
        private readonly DeleteManutencaoUseCase $deleteManutencaoUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = ManutencaoQueryData::from($request);
        $manutencoes = $this->getManutencoesUseCase->execute($query);

        return response()->json(ManutencaoResource::collection($manutencoes));
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateManutencaoData::from($request);
        $manutencao = $this->createManutencaoUseCase->execute($data);

        return response()->json(new ManutencaoResource($manutencao), 201);
    }

    public function show(int|string $manutencao): JsonResponse
    {
        try {
            $manutencaoModel = $this->getManutencaoUseCase->execute((int) $manutencao);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Manutenção pesquisada não existe'], 404);
        }

        return response()->json(new ManutencaoResource($manutencaoModel));
    }

    public function update(Request $request, int|string $manutencao): JsonResponse
    {
        try {
            $data = UpdateManutencaoData::from($request);
            $manutencaoModel = $this->updateManutencaoUseCase->execute((int) $manutencao, $data);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        return response()->json(new ManutencaoResource($manutencaoModel));
    }

    public function destroy(int|string $manutencao): JsonResponse
    {
        try {
            $this->deleteManutencaoUseCase->execute((int) $manutencao);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        return response()->json(null, 204);
    }

    public function indexByCarro(int|string $carro, Request $request): JsonResponse
    {
        try {
            $query = ManutencaoQueryData::from($request->query());
            $manutencoes = $this->getManutencoesByCarroUseCase->execute((int) $carro, $query);

            return response()->json(ManutencaoResource::collection($manutencoes));
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Carro pesquisado não existe'], 404);
        }
    }

    public function proximas(): JsonResponse
    {
        $manutencoes = $this->getManutencoesProximasUseCase->execute(7);

        return response()->json(ManutencaoResource::collection($manutencoes));
    }
}
