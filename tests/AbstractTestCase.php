<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class AbstractTestCase extends TestCase
{
    protected function getProtectedProperty($className, $propertyName)
    {
        $reflector = new ReflectionClass($className);
        $property  = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
