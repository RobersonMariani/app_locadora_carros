<?php

declare(strict_types=1);

namespace App\Api\Modules\Alerta\Controllers;

use App\Api\Modules\Alerta\Data\AlertaQueryData;
use App\Api\Modules\Alerta\Resources\AlertaResource;
use App\Api\Modules\Alerta\UseCases\GetAlertasCountUseCase;
use App\Api\Modules\Alerta\UseCases\GetAlertasUseCase;
use App\Api\Modules\Alerta\UseCases\MarcarAlertaComoLidoUseCase;
use App\Api\Modules\Alerta\UseCases\MarcarTodosAlertasComoLidosUseCase;
use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AlertaController extends Controller
{
    public function index(Request $request, GetAlertasUseCase $useCase): AnonymousResourceCollection
    {
        $query = AlertaQueryData::validateAndCreate($request->query());

        return AlertaResource::collection($useCase->execute($query));
    }

    public function marcarComoLido(Alerta $alerta, MarcarAlertaComoLidoUseCase $useCase): AlertaResource
    {
        return AlertaResource::make($useCase->execute($alerta->id));
    }

    public function marcarTodosComoLidos(MarcarTodosAlertasComoLidosUseCase $useCase): JsonResponse
    {
        $count = $useCase->execute();

        return response()->json(['marcados' => $count]);
    }

    public function count(GetAlertasCountUseCase $useCase): JsonResponse
    {
        return response()->json(['count' => $useCase->execute()]);
    }
}
