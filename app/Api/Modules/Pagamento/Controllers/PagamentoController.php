<?php

declare(strict_types=1);

namespace App\Api\Modules\Pagamento\Controllers;

use App\Api\Modules\Pagamento\Data\CreatePagamentoData;
use App\Api\Modules\Pagamento\Data\PagamentoQueryData;
use App\Api\Modules\Pagamento\Data\UpdatePagamentoData;
use App\Api\Modules\Pagamento\Resources\PagamentoResource;
use App\Api\Modules\Pagamento\UseCases\CreatePagamentoUseCase;
use App\Api\Modules\Pagamento\UseCases\DeletePagamentoUseCase;
use App\Api\Modules\Pagamento\UseCases\GetPagamentosByLocacaoUseCase;
use App\Api\Modules\Pagamento\UseCases\GetPagamentosUseCase;
use App\Api\Modules\Pagamento\UseCases\GetPagamentoUseCase;
use App\Api\Modules\Pagamento\UseCases\UpdatePagamentoUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class PagamentoController extends Controller
{
    public function __construct(
        private readonly CreatePagamentoUseCase $createPagamentoUseCase,
        private readonly GetPagamentoUseCase $getPagamentoUseCase,
        private readonly GetPagamentosUseCase $getPagamentosUseCase,
        private readonly GetPagamentosByLocacaoUseCase $getPagamentosByLocacaoUseCase,
        private readonly UpdatePagamentoUseCase $updatePagamentoUseCase,
        private readonly DeletePagamentoUseCase $deletePagamentoUseCase,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = PagamentoQueryData::from($request);

        return PagamentoResource::collection($this->getPagamentosUseCase->execute($query));
    }

    public function indexByLocacao(int $locacao): AnonymousResourceCollection
    {
        $pagamentos = $this->getPagamentosByLocacaoUseCase->execute($locacao);

        return PagamentoResource::collection($pagamentos);
    }

    public function store(Request $request): Response
    {
        $data = CreatePagamentoData::from($request);

        return PagamentoResource::make($this->createPagamentoUseCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $pagamento): PagamentoResource
    {
        return PagamentoResource::make($this->getPagamentoUseCase->execute($pagamento));
    }

    public function update(int $pagamento, Request $request): PagamentoResource
    {
        $data = UpdatePagamentoData::from($request);

        return PagamentoResource::make($this->updatePagamentoUseCase->execute($pagamento, $data));
    }

    public function destroy(int $pagamento): JsonResponse
    {
        $this->deletePagamentoUseCase->execute($pagamento);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
