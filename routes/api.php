<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api.jwt')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['api.jwt'])->name('auth.login');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
    Route::prefix('perfil')->group(function () {
        Route::get('listagem', [PerfilController::class, 'getPerfis'])->name('lista.perfis');
        Route::get('listagem/{id}', [PerfilController::class, 'getPermissoesByPerfilId']);
        Route::put('atualizar', [PerfilController::class, 'updatePerfil'])->name('atualizar.perfil.permissoes');
        Route::delete('deletar', [PerfilController::class, 'deletePerfil'])->name('deletar.perfil.permissoes');
    });
    Route::prefix('permissao')->group(function () {
        Route::get('listagem', [PermissaoController::class, 'getPermissoes'])->name('lista.permissoes');
        Route::put('ativar', [PermissaoController::class, 'ativarPermissoes'])->name('ativar.permissoes');
        Route::delete('deletar', [PermissaoController::class, 'desativarPermissoes'])->name('deletar.permissoes');
    });
    Route::prefix('usuario')->group(function () {
        Route::get('listagem', [UsuarioController::class, 'getUsuarios'])->name('lista.usuarios');
    });
});
