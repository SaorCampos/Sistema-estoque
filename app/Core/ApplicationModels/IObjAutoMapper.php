<?php

namespace App\Core\ApplicationModels;
use App\Core\ApplicationModels\IArraySerializer;


interface IObjAutoMapper
{
    public function mapFromObj(IArraySerializer $obj): static;
}