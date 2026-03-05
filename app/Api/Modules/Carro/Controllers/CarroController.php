<?php

namespace App\Api\Modules\Carro\Controllers;

use App\Api\Modules\Carro\Data\CarroQueryData;
use App\Api\Modules\Carro\Data\CreateCarroData;
use App\Api\Modules\Carro\Data\UpdateCarroData;
use App\Api\Modules\Carro\Resources\CarroResource;
use App\Api\Modules\Carro\UseCases\CreateCarroUseCase;
use App\Api\Modules\Carro\UseCases\DeleteCarroUseCase;
use App\Api\Modules\Carro\UseCases\GetCarrosUseCase;
use App\Api\Modules\Carro\UseCases\GetCarroUseCase;
use App\Api\Modules\Carro\UseCases\UpdateCarroUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    public function __construct(
        private readonly CreateCarroUseCase $createCarroUseCase,
        private readonly GetCarroUseCase $getCarroUseCase,
        private readonly GetCarrosUseCase $getCarrosUseCase,
        private readonly UpdateCarroUseCase $updateCarroUseCase,
        private readonly DeleteCarroUseCase $deleteCarroUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = CarroQueryData::from($request);
        $carros = $this->getCarrosUseCase->execute($query);

        return response()->json(CarroResource::collection($carros));
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateCarroData::from($request);
        $carro = $this->createCarroUseCase->execute($data);

        return response()->json(new CarroResource($carro), 201);
    }

    public function show(int|string $carro): JsonResponse
    {
        try {
            $carroModel = $this->getCarroUseCase->execute((int) $carro);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Carro pesquisado não existe'], 404);
        }

        return response()->json(new CarroResource($carroModel));
    }

    public function update(Request $request, int|string $carro): JsonResponse
    {
        try {
            $data = UpdateCarroData::from($request);
            $carroModel = $this->updateCarroUseCase->execute((int) $carro, $data);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        return response()->json(new CarroResource($carroModel));
    }

    public function destroy(int|string $carro): JsonResponse
    {
        try {
            $this->deleteCarroUseCase->execute((int) $carro);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        return response()->json(null, 204);
    }
}
