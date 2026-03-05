<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use Illuminate\Support\Facades\Auth;

class RefreshTokenUseCase
{
    public function execute(): string
    {
        return Auth::guard('api')->refresh();
    }
}
