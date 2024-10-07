<?php

namespace App\Core\Services\Usuario;

use App\Http\Requests\Usuario\UsuarioDeleteRequest;

interface IUsuarioDeleteService
{
    public function deletarUsuarios(UsuarioDeleteRequest $request): bool;
}
