<?php

use App\Api\Modules\Auth\Controllers\AuthController;
use App\Api\Modules\Carro\Controllers\CarroController;
use App\Api\Modules\Cliente\Controllers\ClienteController;
use App\Api\Modules\Locacao\Controllers\LocacaoController;
use App\Api\Modules\Marca\Controllers\MarcaController;
use App\Api\Modules\Modelo\Controllers\ModeloController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('carro', CarroController::class);
    Route::apiResource('locacao', LocacaoController::class);
    Route::apiResource('marca', MarcaController::class);
    Route::apiResource('modelo', ModeloController::class);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('refresh', [AuthController::class, 'refresh']);
