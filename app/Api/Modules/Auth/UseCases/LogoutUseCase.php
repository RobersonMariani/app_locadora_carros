<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use Illuminate\Support\Facades\Auth;

class LogoutUseCase
{
    public function execute(): void
    {
        Auth::guard('api')->logout();
    }
}
