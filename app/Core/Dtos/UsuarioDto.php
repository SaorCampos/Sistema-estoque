<?php

namespace App\Core\Dtos;

use App\Core\Traits\AutoMapper;
use App\Core\Traits\ArraySerializer;
use App\Core\Traits\EntityDefaultFields;
use App\Core\ApplicationModels\IArraySerializer;

class UsuarioDto implements IArraySerializer
{
    use ArraySerializer, EntityDefaultFields, AutoMapper;

    public string $nome;
    public string $email;
}
