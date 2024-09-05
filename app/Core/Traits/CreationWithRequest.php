<?php

namespace App\Core\Traits;

use App\Core\ApplicationModels\IArraySerializer;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use ReflectionClass;

trait CreationWithRequest
{
    public static function createFromRequest(Request $request): ?static
    {
        try {
            $instance = new self();
            return $instance->mapFromArray($request->all());
        } catch (Exception $e) {
            return null;
        }
    }

    private function mapFromArray(array $entity): static
    {
        $entityArr = $entity;

        $reflectionClass = new ReflectionClass(self::class);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $propType = $property->getType();
            $propName = $property->getName();
            $modelPropName = null;
            foreach ($entityArr as $key => $_) {
                if (str_replace('_', '', strtolower($key)) === strtolower($property->getName())) {
                    $modelPropName = $key;
                    break;
                }
            }
            if (!isset($modelPropName)) continue;
            $propValue = null;
            switch ($propType->getName()) {
                case 'DateTime':
                    if ($entityArr[$modelPropName] instanceof DateTime) {
                        $propValue = $entityArr[$modelPropName] ?? null;
                    } else {
                        $propValue = new DateTime($entityArr[$modelPropName]) ?? null;
                    }
                    break;
                case 'int':
                    $propValue = intval($entityArr[$modelPropName]) ?? null;
                    break;
                case 'float':
                    $propValue = floatval($entityArr[$modelPropName]) ?? null;
                    break;
                case 'string':
                    $propValue = $entityArr[$modelPropName] ?? null;
                    break;
                case 'bool':
                    $propValue = $entityArr[$modelPropName] ?? null;
                    break;
                case 'array':
                    $propValue = $entityArr[$modelPropName] ?? null;
                    break;
                default:
                    break;
            }
            $this->$propName = $propValue ?? null;
        }
        return $this;
    }
}
