<?php


namespace App\Support\Models;


use App\Support\Utils\DateUtils;
use DateTime;

abstract class Model
{
    public function toArray(): array
    {
        $array = [];
        foreach ($this as $key => $value) {
            if (isset($value)) {
                $array[$key] = $value;
                if (is_object($array[$key]) and get_class($array[$key]) === DateTime::class) {
                    $array[$key] = DateUtils::formatDateIso8601($array[$key]);
                }
            }
        }
        return $array;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
