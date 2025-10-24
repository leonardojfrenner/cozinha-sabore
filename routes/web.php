<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RestauranteLoginController;
use App\Http\Controllers\PedidoController;

Route::get('/', function () {
    return redirect()->route('restaurante.login');
});

// Rotas de autenticação do restaurante
Route::get('/login', [RestauranteLoginController::class, 'showLoginForm'])->name('restaurante.login');
Route::post('/login', [RestauranteLoginController::class, 'login']);
Route::post('/logout', [RestauranteLoginController::class, 'logout'])->name('restaurante.logout');

// Rotas protegidas
Route::middleware(['restaurante.auth'])->group(function () {
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::patch('/pedidos/{id}/status', [PedidoController::class, 'updateStatus'])->name('pedidos.update-status');
    Route::get('/pedidos/historico', [PedidoController::class, 'historico'])->name('pedidos.historico');
});
