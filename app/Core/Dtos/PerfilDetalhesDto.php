<?php

namespace App\Core\Dtos;

use Illuminate\Support\Collection;

class PerfilDetalhesDto
{
    public function __construct(
        public PerfilDto $perfil,
        public Collection $permissoes
    )
    {
    }
}
