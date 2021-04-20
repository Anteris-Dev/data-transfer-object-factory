<?php

namespace Anteris\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use ReflectionClass;

interface AdapterInterface
{
    /**
     * Determines whether or not the adapter can handle interacting with the
     * reflection class passed to it.
     */
    public function handles(ReflectionClass $class): bool;

    /**
     * Returns the properties that need to be generated based on the reflection
     * class passed to it.
     */
    public function getProperties(ReflectionClass $class): PropertyCollection;

    /**
     * Creates and returns an instance of the class being reflected with the
     * generated properties attached to it.
     */
    public function createClass(ReflectionClass $class, PropertyCollection $properties);
}
