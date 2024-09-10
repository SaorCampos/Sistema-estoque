<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perfil extends Entity
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'perfil';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'nome',
        'criado_em',
        'atualizado_em',
        'deletado_em',
        'criado_por',
        'atualizado_por'
    ];

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class, 'perfil_permissao', 'perfil_id', 'permissao_id');
    }
}
