<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use Tymon\JWTAuth\JWTGuard;

class RefreshTokenUseCase
{
    public function execute(): string
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        return $guard->refresh();
    }
}
