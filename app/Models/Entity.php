<?php

namespace App\Models;

use App\Core\Traits\AutoMapper;
use Illuminate\Database\Eloquent\Model;
use App\Core\ApplicationModels\IObjAutoMapper;
use App\Core\ApplicationModels\IArraySerializer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entity extends Model implements IObjAutoMapper, IArraySerializer
{
    use HasFactory, AutoMapper;

    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';
    public const DELETED_AT = 'deletado_em';

    protected static function booted()
    {
        parent::booted();
        static::creating(function (Entity $model) {
            $now = $model->freshTimestamp();
            $model->setCreatedAt($now->setTimezone('UTC'));
            $model->setUpdatedAt($now->setTimezone('UTC'));
        });
    }
}
