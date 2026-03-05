<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Controllers;

use App\Api\Modules\Locacao\Data\CreateLocacaoData;
use App\Api\Modules\Locacao\Data\FinalizarLocacaoData;
use App\Api\Modules\Locacao\Data\LocacaoQueryData;
use App\Api\Modules\Locacao\Data\UpdateLocacaoData;
use App\Api\Modules\Locacao\Resources\LocacaoResource;
use App\Api\Modules\Locacao\UseCases\CancelarLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\CreateLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\DeleteLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\FinalizarLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\GetLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\GetLocacoesUseCase;
use App\Api\Modules\Locacao\UseCases\IniciarLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\UpdateLocacaoUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class LocacaoController extends Controller
{
    public function __construct(
        private readonly CreateLocacaoUseCase $createLocacaoUseCase,
        private readonly GetLocacaoUseCase $getLocacaoUseCase,
        private readonly GetLocacoesUseCase $getLocacoesUseCase,
        private readonly UpdateLocacaoUseCase $updateLocacaoUseCase,
        private readonly DeleteLocacaoUseCase $deleteLocacaoUseCase,
        private readonly IniciarLocacaoUseCase $iniciarLocacaoUseCase,
        private readonly FinalizarLocacaoUseCase $finalizarLocacaoUseCase,
        private readonly CancelarLocacaoUseCase $cancelarLocacaoUseCase,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = LocacaoQueryData::from($request);

        return LocacaoResource::collection($this->getLocacoesUseCase->execute($query));
    }

    public function store(Request $request): Response
    {
        $data = CreateLocacaoData::from($request);

        return LocacaoResource::make($this->createLocacaoUseCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $locacao): LocacaoResource
    {
        return LocacaoResource::make($this->getLocacaoUseCase->execute($locacao));
    }

    public function update(int $locacao, Request $request): LocacaoResource
    {
        $data = UpdateLocacaoData::from($request);

        return LocacaoResource::make($this->updateLocacaoUseCase->execute($locacao, $data));
    }

    public function destroy(int $locacao): JsonResponse
    {
        $this->deleteLocacaoUseCase->execute($locacao);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function iniciar(int $locacao): LocacaoResource
    {
        return LocacaoResource::make($this->iniciarLocacaoUseCase->execute($locacao));
    }

    public function finalizar(int $locacao, Request $request): LocacaoResource
    {
        $data = FinalizarLocacaoData::from($request);

        return LocacaoResource::make($this->finalizarLocacaoUseCase->execute($locacao, $data));
    }

    public function cancelar(int $locacao): LocacaoResource
    {
        return LocacaoResource::make($this->cancelarLocacaoUseCase->execute($locacao));
    }
}
