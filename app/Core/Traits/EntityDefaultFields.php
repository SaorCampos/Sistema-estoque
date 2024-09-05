<?php

namespace App\Core\Traits;
use DateTime;

trait EntityDefaultFields
{
    public int $id;
    public string $uuid;
    public bool $ativo;
    public DateTime $criadoEm;
    public string $criadoPor;
    public DateTime $modificadoEm;
    public string $modificadoPor;
}