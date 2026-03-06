<?php

declare(strict_types=1);

use App\Api\Modules\Alerta\Controllers\AlertaController;
use App\Api\Modules\Auth\Controllers\AuthController;
use App\Api\Modules\Carro\Controllers\CarroController;
use App\Api\Modules\Cliente\Controllers\ClienteController;
use App\Api\Modules\Dashboard\Controllers\DashboardController;
use App\Api\Modules\Locacao\Controllers\LocacaoController;
use App\Api\Modules\Manutencao\Controllers\ManutencaoController;
use App\Api\Modules\Marca\Controllers\MarcaController;
use App\Api\Modules\Modelo\Controllers\ModeloController;
use App\Api\Modules\Multa\Controllers\MultaController;
use App\Api\Modules\Pagamento\Controllers\PagamentoController;
use App\Api\Modules\Vistoria\Controllers\VistoriaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['jwt.auth', 'throttle:api'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('resumo', [DashboardController::class, 'resumo']);
        Route::get('locacoes-por-status', [DashboardController::class, 'locacoesPorStatus']);
        Route::get('faturamento', [DashboardController::class, 'faturamento']);
    });
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('carro', CarroController::class);
    Route::get('carro/{carro}/manutencao', [ManutencaoController::class, 'indexByCarro']);
    Route::get('manutencao/proximas', [ManutencaoController::class, 'proximas'])->name('manutencao.proximas');
    Route::apiResource('manutencao', ManutencaoController::class);
    Route::patch('locacao/{locacao}/iniciar', [LocacaoController::class, 'iniciar']);
    Route::patch('locacao/{locacao}/finalizar', [LocacaoController::class, 'finalizar']);
    Route::patch('locacao/{locacao}/cancelar', [LocacaoController::class, 'cancelar']);
    Route::apiResource('locacao', LocacaoController::class);
    Route::apiResource('marca', MarcaController::class);
    Route::apiResource('modelo', ModeloController::class);
    Route::get('locacao/{locacao}/pagamento', [PagamentoController::class, 'indexByLocacao']);
    Route::get('locacao/{locacao}/vistoria', [VistoriaController::class, 'indexByLocacao']);
    Route::post('locacao/{locacao}/vistoria', [VistoriaController::class, 'store']);
    Route::get('locacao/{locacao}/multa', [MultaController::class, 'indexByLocacao']);
    Route::get('cliente/{cliente}/multa', [MultaController::class, 'indexByCliente']);
    Route::apiResource('multa', MultaController::class);
    Route::apiResource('pagamento', PagamentoController::class);
    Route::get('alerta/count', [AlertaController::class, 'count'])->name('alerta.count');
    Route::patch('alerta/lidos', [AlertaController::class, 'marcarTodosComoLidos'])->name('alerta.marcar-todos');
    Route::patch('alerta/{alerta}/lido', [AlertaController::class, 'marcarComoLido'])->name('alerta.marcar-lido');
    Route::get('alerta', [AlertaController::class, 'index'])->name('alerta.index');
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('throttle:login');
