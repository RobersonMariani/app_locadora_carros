<?php

declare(strict_types=1);

namespace App\Api\Modules\Dashboard\Controllers;

use App\Api\Modules\Dashboard\Resources\FaturamentoResource;
use App\Api\Modules\Dashboard\Resources\LocacoesPorStatusResource;
use App\Api\Modules\Dashboard\Resources\ResumoResource;
use App\Api\Modules\Dashboard\UseCases\GetFaturamentoUseCase;
use App\Api\Modules\Dashboard\UseCases\GetLocacoesPorStatusUseCase;
use App\Api\Modules\Dashboard\UseCases\GetResumoUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function resumo(GetResumoUseCase $useCase): JsonResponse
    {
        $resumo = $useCase->execute();

        return response()->json(ResumoResource::make($resumo)->resolve());
    }

    public function locacoesPorStatus(GetLocacoesPorStatusUseCase $useCase): JsonResponse
    {
        $dados = $useCase->execute();

        return response()->json(LocacoesPorStatusResource::collection($dados)->resolve());
    }

    public function faturamento(Request $request, GetFaturamentoUseCase $useCase): JsonResponse
    {
        $periodo = in_array($request->query('periodo'), ['mensal', 'semanal'], true)
            ? $request->query('periodo')
            : 'mensal';

        $dados = $useCase->execute($periodo);

        return response()->json(FaturamentoResource::collection($dados)->resolve());
    }
}
