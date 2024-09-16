<?php

namespace App\Core\Traits;

trait EntityDefaultFields
{
    public string $id;
    public ?string $criadoEm;
    public ?string $criadoPor;
    public ?string $atualizadoEm;
    public ?string $atualizadoPor;
    public ?string $deletadoEm;
}
