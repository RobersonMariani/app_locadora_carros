<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

return [
    AppServiceProvider::class,
    LaravelServiceProvider::class,
];
