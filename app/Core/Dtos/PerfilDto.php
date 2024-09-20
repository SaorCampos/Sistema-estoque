<?php

namespace App\Core\Dtos;

use App\Core\ApplicationModels\IArraySerializer;
use App\Core\Traits\ArraySerializer;
use App\Core\Traits\AutoMapper;
use App\Core\Traits\EntityDefaultFields;

class PerfilDto implements IArraySerializer
{
    use ArraySerializer, EntityDefaultFields, AutoMapper;

    public string $nome;
}
