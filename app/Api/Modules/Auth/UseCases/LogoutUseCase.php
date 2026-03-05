<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use Tymon\JWTAuth\JWTGuard;

class LogoutUseCase
{
    public function execute(): void
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        $guard->logout();
    }
}
