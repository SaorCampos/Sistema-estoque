<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permissao extends Entity
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'permissao';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nome',
        'criado_em',
        'atualizado_em',
        'deletado_em',
        'criado_por',
        'atualizado_por'
    ];
}
