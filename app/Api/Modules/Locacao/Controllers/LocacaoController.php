<?php

declare(strict_types=1);

namespace App\Api\Modules\Locacao\Controllers;

use App\Api\Modules\Locacao\Data\CreateLocacaoData;
use App\Api\Modules\Locacao\Data\LocacaoQueryData;
use App\Api\Modules\Locacao\Data\UpdateLocacaoData;
use App\Api\Modules\Locacao\Resources\LocacaoResource;
use App\Api\Modules\Locacao\UseCases\CreateLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\DeleteLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\GetLocacaoUseCase;
use App\Api\Modules\Locacao\UseCases\GetLocacoesUseCase;
use App\Api\Modules\Locacao\UseCases\UpdateLocacaoUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class LocacaoController extends Controller
{
    public function index(Request $request, GetLocacoesUseCase $useCase): AnonymousResourceCollection
    {
        $query = LocacaoQueryData::from($request);

        return LocacaoResource::collection($useCase->execute($query));
    }

    public function store(Request $request, CreateLocacaoUseCase $useCase): Response
    {
        $data = CreateLocacaoData::from($request);

        return LocacaoResource::make($useCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $locacao, GetLocacaoUseCase $useCase): LocacaoResource
    {
        return LocacaoResource::make($useCase->execute($locacao));
    }

    public function update(int $locacao, Request $request, UpdateLocacaoUseCase $useCase): LocacaoResource
    {
        $data = UpdateLocacaoData::from($request);

        return LocacaoResource::make($useCase->execute($locacao, $data));
    }

    public function destroy(int $locacao, DeleteLocacaoUseCase $useCase): JsonResponse
    {
        $useCase->execute($locacao);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
