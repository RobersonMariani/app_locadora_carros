<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\Controllers;

use App\Api\Modules\Auth\Data\LoginData;
use App\Api\Modules\Auth\Resources\AuthResource;
use App\Api\Modules\Auth\UseCases\GetAuthenticatedUserUseCase;
use App\Api\Modules\Auth\UseCases\LoginUseCase;
use App\Api\Modules\Auth\UseCases\LogoutUseCase;
use App\Api\Modules\Auth\UseCases\RefreshTokenUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginUseCase $loginUseCase,
        private readonly LogoutUseCase $logoutUseCase,
        private readonly RefreshTokenUseCase $refreshTokenUseCase,
        private readonly GetAuthenticatedUserUseCase $getAuthenticatedUserUseCase,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $data = LoginData::from($request->all());
        $token = $this->loginUseCase->execute($data);

        if ($token === null) {
            return response()->json(['erro' => 'Usuário ou senha inválido'], Response::HTTP_FORBIDDEN);
        }

        return response()->json(['token' => $token], Response::HTTP_OK);
    }

    public function logout(): JsonResponse
    {
        $this->logoutUseCase->execute();

        return response()->json(['msg' => 'O logout foi feito com sucesso']);
    }

    public function refresh(): JsonResponse
    {
        $token = $this->refreshTokenUseCase->execute();

        return response()->json(['token' => $token]);
    }

    public function me(): JsonResponse
    {
        $user = $this->getAuthenticatedUserUseCase->execute();

        return response()->json(AuthResource::make($user));
    }
}
