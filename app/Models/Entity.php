<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
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
