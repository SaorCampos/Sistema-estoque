<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api.jwt')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['api.jwt'])->name('auth.login');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::prefix('perfil')->group(function () {
        Route::get('listagem', [PerfilController::class, 'getPerfis'])->name('lista.perfis');
        Route::get('listagem/{id}', [PerfilController::class, 'getPermissoesByPerfilId']);
        // Route::post('criar', [PerfilController::class, 'criarPerfil'])->name('criar.perfil');
        // Route::put('atualizar/{id}', [PerfilController::class, 'atualizarPerfil'])->name('atualizar.perfil');
        // Route::delete('deletar/{id}', [PerfilController::class, 'deletarPerfil'])->name('deletar.perfil');
    });
    });
});
