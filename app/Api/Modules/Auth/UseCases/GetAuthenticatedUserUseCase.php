<?php

declare(strict_types=1);

namespace App\Api\Modules\Auth\UseCases;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetAuthenticatedUserUseCase
{
    public function execute(): ?User
    {
        return Auth::user();
    }
}
