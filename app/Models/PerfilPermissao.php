<?php

namespace App\Models;

class PerfilPermissao extends Entity
{
    protected $table = 'perfil_permissao';

    public $incrementing = false;

    protected $fillable = [
        'perfil_id',
        'permissao_id',
        'criado_em',
        'atualizado_em',
        'criado_por',
        'atualizado_por',
    ];
}
