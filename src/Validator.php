<?php

namespace Anteris\DataTransferObjectFactory;

use ReflectionClass;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

class Validator
{
    public static function isDTO(string $class)
    {
        if (! class_exists($class)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);

        if (! $reflectionClass->isSubclassOf(DataTransferObject::class)) {
            return false;
        }

        return true;
    }

    public static function isDTOCollection(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);

        if (! $reflectionClass->isSubclassOf(DataTransferObjectCollection::class)) {
            return false;
        }

        return true;
    }
}
