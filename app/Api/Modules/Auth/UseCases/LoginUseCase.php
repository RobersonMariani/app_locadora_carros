<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use App\Api\Modules\Auth\Data\LoginData;
use Illuminate\Support\Facades\Auth;

class LoginUseCase
{
    public function execute(LoginData $data): ?string
    {
        $token = Auth::guard('api')->attempt($data->toCredentials());

        return $token ?: null;
    }
}
