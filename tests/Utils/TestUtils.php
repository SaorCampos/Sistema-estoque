<?php

namespace Tests\Utils;

use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use ReflectionClass;

class TestUtils
{
    public static function formatDate(string $date): string
    {
        $utcDateTime = DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $date, new DateTimeZone('UTC'));
        return $utcDateTime->format("Y-m-d H:i:s");
    }

    public static function mockObj(string $class): mixed
    {
        $instance = new $class();
        $reflectionClass = new ReflectionClass($class);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $propType = $property->getType();
            $propName = $property->getName();

            switch ($propType->getName()) {
                case 'DateTime':
                    $propValue = new DateTime('@' . mt_rand(strtotime('-1 year'), strtotime('+1 year')));
                    break;
                case 'int':
                    $propValue = rand(1, 99999999);
                    break;
                case 'float':
                    $propValue = 1 + mt_rand() / mt_getrandmax() * (999999999 - 1);
                    break;
                case 'string':
                    $propValue = str_contains(strtolower($propName), 'uuid')
                        ? Str::uuid()
                        : Str::random(rand(1, 100));
                    break;
                case 'bool':
                    $propValue = !!rand(0, 1);
                    break;
                case 'array':
                    $propValue = [];
                    break;
                default:
                    break;
            }
            $instance->$propName = $propValue;
        }
        return $instance;
    }
}
