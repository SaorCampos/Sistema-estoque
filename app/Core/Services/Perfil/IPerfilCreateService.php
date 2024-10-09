<?php

namespace App\Core\Services\Perfil;

use App\Core\Dtos\PerfilDetalhesDto;
use App\Http\Requests\Perfil\PerfilCreateRequest;

interface IPerfilCreateService
{
    public function createPerfil(PerfilCreateRequest $request): PerfilDetalhesDto;
}
