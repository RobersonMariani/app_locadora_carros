<?php

namespace App\Api\Modules\Cliente\Controllers;

use App\Api\Modules\Cliente\Data\CreateClienteData;
use App\Api\Modules\Cliente\Data\ClienteQueryData;
use App\Api\Modules\Cliente\Data\UpdateClienteData;
use App\Api\Modules\Cliente\Resources\ClienteResource;
use App\Api\Modules\Cliente\UseCases\CreateClienteUseCase;
use App\Api\Modules\Cliente\UseCases\DeleteClienteUseCase;
use App\Api\Modules\Cliente\UseCases\GetClientesUseCase;
use App\Api\Modules\Cliente\UseCases\GetClienteUseCase;
use App\Api\Modules\Cliente\UseCases\UpdateClienteUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(
        private readonly CreateClienteUseCase $createClienteUseCase,
        private readonly GetClienteUseCase $getClienteUseCase,
        private readonly GetClientesUseCase $getClientesUseCase,
        private readonly UpdateClienteUseCase $updateClienteUseCase,
        private readonly DeleteClienteUseCase $deleteClienteUseCase,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = ClienteQueryData::from($request);
        $clientes = $this->getClientesUseCase->execute($query);

        return response()->json(ClienteResource::collection($clientes));
    }

    public function store(Request $request): JsonResponse
    {
        $data = CreateClienteData::from($request);
        $cliente = $this->createClienteUseCase->execute($data);

        return response()->json(new ClienteResource($cliente), 201);
    }

    public function show(int|string $cliente): JsonResponse
    {
        try {
            $clienteModel = $this->getClienteUseCase->execute((int) $cliente);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Cliente pesquisado não existe'], 404);
        }

        return response()->json(new ClienteResource($clienteModel));
    }

    public function update(Request $request, int|string $cliente): JsonResponse
    {
        try {
            $data = UpdateClienteData::from($request);
            $clienteModel = $this->updateClienteUseCase->execute((int) $cliente, $data);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        return response()->json(new ClienteResource($clienteModel));
    }

    public function destroy(int|string $cliente): JsonResponse
    {
        try {
            $this->deleteClienteUseCase->execute((int) $cliente);
        } catch (ModelNotFoundException) {
            return response()->json(['erro' => 'Impossível realizar a remoção. O recurso solicitado não existe'], 404);
        }

        return response()->json(['msg' => 'O cliente foi removido com sucesso']);
    }
}
