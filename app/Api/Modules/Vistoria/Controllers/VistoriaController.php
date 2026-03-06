<?php

declare(strict_types=1);

namespace App\Api\Modules\Vistoria\Controllers;

use App\Api\Modules\Vistoria\Data\CreateVistoriaData;
use App\Api\Modules\Vistoria\Resources\VistoriaResource;
use App\Api\Modules\Vistoria\UseCases\CreateVistoriaUseCase;
use App\Api\Modules\Vistoria\UseCases\GetVistoriasByLocacaoUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class VistoriaController extends Controller
{
    public function __construct(
        private readonly CreateVistoriaUseCase $createVistoriaUseCase,
        private readonly GetVistoriasByLocacaoUseCase $getVistoriasByLocacaoUseCase,
    ) {}

    public function store(int $locacao, Request $request): Response
    {
        $data = CreateVistoriaData::validateAndCreate(
            array_merge($request->all(), ['locacao_id' => $locacao]),
        );

        return VistoriaResource::make($this->createVistoriaUseCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    public function indexByLocacao(int $locacao): AnonymousResourceCollection
    {
        $vistorias = $this->getVistoriasByLocacaoUseCase->execute($locacao);

        return VistoriaResource::collection($vistorias);
    }
}
