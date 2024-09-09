<?php

namespace App\Core\Traits;
use App\Core\ApplicationModels\IArraySerializer;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

trait AutoMapper
{
    public function mapTo(string $class)
    {
        return (new $class)->mapFromObj($this);
    }

    public function mapFromObj(IArraySerializer $obj): static {
        return $this->mapFromArray($obj->toArray());
    }

    public function mapFromArray(array $entity): static
    {
        $entityArr = $entity;

        $reflectionClass = new ReflectionClass(self::class);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $propType = $property->getType();
            $propName = $property->getName();
            $modelPropName = null;
            foreach($entityArr as $key => $_) {
                if (str_replace('_', '', strtolower($key)) === strtolower($property->getName())) {
                    $modelPropName = $key;
                    break;
                }
            }
            if (!$modelPropName) {
                $className = self::class;
                throw new Exception("The property $propName could not be mapped in $className from object " . json_encode($entityArr) . '.');
            }
            $propValue = null;
            switch($propType->getName()) {
                case 'DateTime':
                    if ($entityArr[$modelPropName] instanceof DateTime) {
                        $propValue = $entityArr[$modelPropName];
                    } else {
                        $propValue = new DateTime($entityArr[$modelPropName]);
                    }
                    break;
                case 'int':
                    $propValue = intval($entityArr[$modelPropName]);
                    break;
                case 'float':
                    $propValue = floatval($entityArr[$modelPropName]);
                    break;
                case 'string':
                    $propValue = $entityArr[$modelPropName];
                    break;
                case 'bool':
                    $propValue = !!$entityArr[$modelPropName];
                    break;
                case 'array':
                    $propValue = $entityArr[$modelPropName];
                    break;
                default:
                    break;
            }
            $this->$propName = $propValue;
        }
        return $this;
    }
}
