<?php

namespace App\Core\Services\Usuario;

use App\Http\Requests\Usuario\UsuarioAlterarSenhaRequest;

interface IUsuarioAlterarSenhaService
{
    public function alterarSenha(UsuarioAlterarSenhaRequest $request): bool;
}
