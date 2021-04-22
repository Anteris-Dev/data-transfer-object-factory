<?php

namespace Anteris\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionUnionType;

class GetSetMethodAdapter implements AdapterInterface
{
    private static $methodCache = [];

    public function handles(ReflectionClass $class): bool
    {
        $methods = $this->getSetterMethods($class);

        if (count($methods) <= 0) {
            return false;
        }

        return true;
    }

    public function getProperties(ReflectionClass $class): PropertyCollection
    {
        $properties = new PropertyCollection;
        $methods    = $this->getSetterMethods($class);

        foreach ($methods as $method) {
            $name   = lcfirst(preg_replace('/set/i', '', $method->getName(), 1));
            $params = $method->getParameters();

            if (count($params) <= 0) {
                continue;
            }

            $theParam = $params[0];
            $types    = $this->getParameterTypes($params[0]);

            if (! in_array('null', $types) && $theParam->allowsNull()) {
                $types[] = 'null';
            }

            $properties[] = new PropertyData($name, $types);
        }

        return $properties;
    }

    public function createClass(ReflectionClass $class, PropertyCollection $properties): object
    {
        $instance = $class->newInstance();

        foreach ($properties as $property) {
            $method = 'set' . ucfirst($property->name);
            $instance->{$method}($property->value);
        }

        return $instance;
    }

    /**
     * @return ReflectionMethod[]
     */
    private function getSetterMethods(ReflectionClass $class): array
    {
        if (! isset(static::$methodCache[$class->getName()])) {
            $methods         = $class->getMethods(ReflectionMethod::IS_PUBLIC);
            $settableMethods = [];
    
            foreach ($methods as $method) {
                if (! str_starts_with($method->getName(), 'set')) {
                    continue;
                }
    
                $settableMethods[] = $method;
            }

            static::$methodCache[$class->getName()] = $settableMethods;
        }


        return static::$methodCache[$class->getName()];
    }

    private function getParameterTypes(ReflectionParameter $parameter): array
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
