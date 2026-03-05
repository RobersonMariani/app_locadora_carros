<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use App\Api\Modules\Auth\Data\LoginData;
use Tymon\JWTAuth\JWTGuard;

class LoginUseCase
{
    public function execute(LoginData $data): ?string
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        $token = $guard->attempt($data->toCredentials());

        return $token ?: null;
    }
}
