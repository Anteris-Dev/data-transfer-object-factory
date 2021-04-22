<?php

namespace Anteris\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use ReflectionClass;
use ReflectionParameter;
use ReflectionUnionType;

class ConstructAdapter implements AdapterInterface
{
    public function handles(ReflectionClass $class): bool
    {
        $construct = $class->getConstructor();

        if (
            is_null($construct) ||
            ! $construct->isPublic() ||
            $construct->getNumberOfParameters() <= 0
        ) {
            return false;
        }

        return true;
    }

    public function getProperties(ReflectionClass $class): PropertyCollection
    {
        $properties = new PropertyCollection;
        $construct  = $class->getConstructor();

        foreach ($construct->getParameters() as $parameter) {
            $types = $this->getParameterTypes($parameter);

            // Make sure properties that allow null values have "null" in their
            // types list.
            if (
                ! in_array('null', $types) &&
                $parameter->getType()?->allowsNull()
            ) {
                array_push($types, 'null');
            }

            $properties[] = new PropertyData(
                $parameter->getName(),
                $types
            );
        }

        return $properties;
    }

    public function createClass(ReflectionClass $class, PropertyCollection $properties): object
    {
        $propertiesAndValues = [];

        foreach ($properties as $property) {
            $propertiesAndValues[$property->name] = $property->value;
        }

        return $class->newInstanceArgs($propertiesAndValues);
    }

    private function getParameterTypes(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

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
