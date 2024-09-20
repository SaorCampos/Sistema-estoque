<?php

namespace App\Core\Services\Perfil;

use App\Core\Dtos\PerfilDetalhesDto;
use App\Http\Requests\Perfil\PerfilPermissaoUpdateRequest;

interface IPerfilDeleteService
{
    public function deletePerfilPermissoes(PerfilPermissaoUpdateRequest $request): PerfilDetalhesDto;
}
