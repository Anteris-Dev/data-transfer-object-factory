<?php

namespace Anteris\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;

class PublicPropertyAdapter implements AdapterInterface
{
    public function handles(ReflectionClass $class): bool
    {
        return count($class->getProperties(ReflectionProperty::IS_PUBLIC)) > 0;
    }

    public function getProperties(ReflectionClass $class): PropertyCollection
    {
        $propertyCollection   = new PropertyCollection;
        $reflectionProperties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectionProperties as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }

            $types = $this->getPropertyTypes($reflectionProperty);

            // Make sure properties that allow null values have "null" in their
            // types list.
            if (
                ! in_array('null', $types) &&
                $reflectionProperty->getType()?->allowsNull()
            ) {
                array_push($types, 'null');
            }

            $propertyCollection[] = new PropertyData(
                $reflectionProperty->getName(),
                $types
            );
        }

        return $propertyCollection;
    }

    public function createClass(
        ReflectionClass $class,
        PropertyCollection $properties
    ): object {
        $instance = $class->newInstance();

        foreach ($properties as $property) {
            $instance->{$property->name} = $property->value;
        }

        return $instance;
    }

    private function getPropertyTypes(ReflectionProperty $property): array
    {
        $type = $property->getType();

        if ($type == null) {
            return [];
        }

        if (! $type instanceof ReflectionUnionType) {
            return [ $type->getName() ];
        }

        $types = [];

        foreach ($type->getTypes() as $unionType) {
            $types[] = $unionType->getName();
        }

        return $types;
    }
}
