<?php

namespace Anteris\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
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
            $propertyName = $this->getPropertyName($class, $property);

            $propertiesAndValues[$propertyName] = $property->value;
        }

        return $class->newInstanceArgs($propertiesAndValues);
    }

    private function getPropertyName(ReflectionClass $class, PropertyData $property): string
    {
        $attributes = $class->getProperty($property->name)->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === \Spatie\DataTransferObject\Attributes\MapFrom::class) {
                return $attribute->getArguments()[0];
            }
        }

        return $property->name;
    }
}
