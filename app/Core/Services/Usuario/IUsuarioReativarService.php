<?php


namespace App\Core\Services\Usuario;

use App\Http\Requests\Usuario\UsuarioReativarRequest;

interface IUsuarioReativarService
{
    public function reativarUsuarios(UsuarioReativarRequest $request): bool;
}
