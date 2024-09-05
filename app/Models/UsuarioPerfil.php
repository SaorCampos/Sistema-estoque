<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioPerfil extends Model
{
    use HasFactory;

    protected $table = 'usuario_perfil';
    public $incrementing = false;
    public $timestamps = true;
    protected $primaryKey = null;

    protected $fillable = [
        'usuario_id',
        'perfil_id',
    ];
}
