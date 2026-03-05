<?php

declare(strict_types=1);

use App\Api\Modules\Auth\Controllers\AuthController;
use App\Api\Modules\Carro\Controllers\CarroController;
use App\Api\Modules\Cliente\Controllers\ClienteController;
use App\Api\Modules\Dashboard\Controllers\DashboardController;
use App\Api\Modules\Locacao\Controllers\LocacaoController;
use App\Api\Modules\Marca\Controllers\MarcaController;
use App\Api\Modules\Modelo\Controllers\ModeloController;
use App\Api\Modules\Pagamento\Controllers\PagamentoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('resumo', [DashboardController::class, 'resumo']);
        Route::get('locacoes-por-status', [DashboardController::class, 'locacoesPorStatus']);
        Route::get('faturamento', [DashboardController::class, 'faturamento']);
    });
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('carro', CarroController::class);
    Route::patch('locacao/{locacao}/iniciar', [LocacaoController::class, 'iniciar']);
    Route::patch('locacao/{locacao}/finalizar', [LocacaoController::class, 'finalizar']);
    Route::patch('locacao/{locacao}/cancelar', [LocacaoController::class, 'cancelar']);
    Route::apiResource('locacao', LocacaoController::class);
    Route::apiResource('marca', MarcaController::class);
    Route::apiResource('modelo', ModeloController::class);
    Route::get('locacao/{locacao}/pagamento', [PagamentoController::class, 'indexByLocacao']);
    Route::apiResource('pagamento', PagamentoController::class);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('refresh', [AuthController::class, 'refresh']);
