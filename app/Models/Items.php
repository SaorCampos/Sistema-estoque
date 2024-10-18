<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Items extends Entity
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'items';
    public $incrementing = false;

    protected $fillable = [
        'nome',
        'descricao',
        'user_id',
        'estoque',
        'categoria',
        'sub_categoria',
        'criado_em',
        'atualizado_em',
        'deletado_em',
        'atualizado_por'
    ];
}
