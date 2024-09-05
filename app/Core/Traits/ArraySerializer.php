<?php

namespace App\Core\Traits;
use Illuminate\Database\Eloquent\Model;

trait ArraySerializer
{
    public function toArray()
    {
        $array = [];
        foreach($this as $key => $property) {
            $array[$key] = $property;
        }
        return $array;
    }
}