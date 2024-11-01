<?php

namespace App\Models;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movimentos extends Entity
{
    use HasFactory, HasUuids;

    const DELETED_AT = null;
    protected $table = 'movimentos';
    public $incrementing = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'quantidade',
        'tipo',
        'data_movimentacao',
        'nota_fiscal',
        'fornecedor',
        'numero_controle_saida',
        'local_destino',
        'criado_em',
        'atualizado_em',
        'atualizado_por'
    ];
}
