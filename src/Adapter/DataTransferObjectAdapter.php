<?php

namespace Anteris\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use ReflectionClass;

class DataTransferObjectAdapter extends PublicPropertyAdapter
{
    public function handles(ReflectionClass $class): bool
    {
        return $class->isSubclassOf(
            \Spatie\DataTransferObject\DataTransferObject::class
        );
    }

    public function createClass(
        ReflectionClass $class,
        PropertyCollection $properties
    ): \Spatie\DataTransferObject\DataTransferObject {
        $propertiesAndValues = [];

        foreach ($properties as $property) {
            $propertiesAndValues[$property->name] = $property->value;
        }

        return $class->newInstanceArgs($propertiesAndValues);
    }
}
