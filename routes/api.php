<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MovimentoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.jwt')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['api.jwt'])->name('auth.login');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
    Route::prefix('perfil')->group(function () {
        Route::get('listagem', [PerfilController::class, 'getPerfis'])->name('lista.perfis');
        Route::get('listagem/{id}', [PerfilController::class, 'getPermissoesByPerfilId']);
        Route::put('atualizar/permissoes', [PerfilController::class, 'updatePerfil'])->name('atualizar.perfil.permissoes');
        Route::delete('deletar/permissoes', [PerfilController::class, 'deletePerfil'])->name('deletar.perfil.permissoes');
        Route::post('criar', [PerfilController::class, 'createPerfil'])->name('criar.perfil');
    });
    Route::prefix('permissao')->group(function () {
        Route::get('listagem', [PermissaoController::class, 'getPermissoes'])->name('lista.permissoes');
        Route::put('ativar', [PermissaoController::class, 'ativarPermissoes'])->name('ativar.permissoes');
        Route::delete('deletar', [PermissaoController::class, 'desativarPermissoes'])->name('deletar.permissoes');
    });
    Route::prefix('usuario')->group(function () {
        Route::get('listagem', [UsuarioController::class, 'getUsuarios'])->name('lista.usuarios');
        Route::post('criar', [UsuarioController::class, 'createUsuario'])->name('criar.usuario');
        Route::put('alterar/senha', [UsuarioController::class, 'alterarSenha'])->name('alterar.senha.usuario')->withoutMiddleware('api.jwt');
        Route::delete('deletar', [UsuarioController::class, 'deletarUsuarios'])->name('deletar.usuario');
        Route::put('reativar', [UsuarioController::class, 'reativarUsuarios'])->name('reativar.usuario');
    });
    Route::prefix('itens')->group(function () {
        Route::get('listagem', [ItemController::class, 'getItems'])->name('lista.itens');
        Route::post('criar', [ItemController::class, 'createItem'])->name('criar.item');
        Route::put('alterar', [ItemController::class, 'updateItem'])->name('alterar.item');
    });
    Route::prefix('movimentacoes')->group(function () {
        Route::get('listagem', [MovimentoController::class, 'getAllMovimetacoes'])->name('lista.movimentacoes');
        Route::post('criar/entrada', [MovimentoController::class, 'createMovimentacaoEntrada'])->name('criar.movimentacao.entrada');
    });
});
