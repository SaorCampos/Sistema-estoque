<?php

namespace App\Core\Services\Usuario;

use App\Core\Dtos\UsuarioDto;
use App\Http\Requests\Usuario\UsuarioCreateRequest;

interface IUsuarioCreateService
{
    public function createUsuario(UsuarioCreateRequest $request): UsuarioDto;
}
