<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perfil extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'perfil';
    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'nome',
        'created_at',
        'updated_at',
        'deleted_at',
        'criado_por',
        'atualizado_por'
    ];
}
