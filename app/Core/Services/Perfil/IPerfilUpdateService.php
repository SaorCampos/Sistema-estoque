<?php

namespace App\Core\Services\Perfil;

use App\Core\Dtos\PerfilDetalhesDto;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;

interface IPerfilUpdateService
{
    public function updatePermissoesPerfil(PerfilPermissaoUpdateRequest $request): PerfilDetalhesDto;
}
