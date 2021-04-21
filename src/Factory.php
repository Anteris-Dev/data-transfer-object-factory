<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\Adapter\AdapterInterface;
use Anteris\DataTransferObjectFactory\Adapter\ConstructAdapter;
use Anteris\DataTransferObjectFactory\Adapter\DataTransferObjectAdapter;
use Anteris\DataTransferObjectFactory\Adapter\GetSetMethodAdapter;
use Anteris\DataTransferObjectFactory\Adapter\PublicPropertyAdapter;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use RuntimeException;

class Factory
{
    private static array $adapters = [];

    private static array $adapterCache = [];

    private static array $classPropertiesCache = [];

    private static bool $booted = false;

    private int $count;

    private string $dataTransferObjectClass;

    private array $states = [];

    public function __construct()
    {
        if (static::$booted == false) {
            static::registerAdapter(new GetSetMethodAdapter);
            static::registerAdapter(new DataTransferObjectAdapter);
            static::registerAdapter(new PublicPropertyAdapter);
            static::registerAdapter(new ConstructAdapter);

            static::$booted = true;
        }
    }

    public static function registerAdapter(AdapterInterface $adapter)
    {
        static::$adapters[] = $adapter;
    }

    public static function new(?string $dto = null): static
    {
        $factory = new static;

        if ($dto) {
            $factory->dto($dto);
        }

        return $factory;
    }

    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function random(int $min = 3, int $max = 100): static
    {
        return $this->count(random_int($min, $max));
    }

    public function dto(string $dataTransferObject): static
    {
        if (! class_exists($dataTransferObject)) {
            throw new InvalidArgumentException(
                "Class [{$dataTransferObject}] does not exist."
            );
        }

        $this->dataTransferObjectClass = $dataTransferObject;

        return $this;
    }

    public function sequence(...$sequence): static
    {
        return $this->state(Sequence::make(...$sequence));
    }

    public function state(callable | array $state): static
    {
        if (! is_callable($state)) {
            $state = fn () => $state;
        }

        $this->states[] = $state;

        return $this;
    }

    public function states(array $states): static
    {
        foreach ($states as $state) {
            $this->state($state);
        }

        return $this;
    }

    public function make(array $attributes = [])
    {
        if (! isset($this->dataTransferObjectClass)) {
            throw new LogicException(
                'Please specify a Data Transfer Object to be generated.'
            );
        }

        // Pass attributes along as state.
        if (! empty($attributes)) {
            return $this->state($attributes)->make();
        }

        // Start the generator!
        $reflectionClass = new ReflectionClass($this->dataTransferObjectClass);

        if (isset($this->count)) {
            return $this->makeMultipleDtosFromReflection(
                $reflectionClass,
                $this->count
            );
        }

        return $this->makeDtoFromReflection($reflectionClass);
    }

    private function makeDtoWithAdapter(
        AdapterInterface $adapter,
        ReflectionClass $class
    ) {
        if (! isset(static::$classPropertiesCache[$class->getName()])) {
            static::$classPropertiesCache[$class->getName()] = $adapter->getProperties($class);
        }

        $properties = static::$classPropertiesCache[$class->getName()];

        // Resolve all the options set through the state method.
        $setByState = [];

        foreach ($this->states as $state) {
            $result     = $state();
            $setByState = array_merge($setByState, is_array($result) ? $result : []);
        }

        // Iterate through the properties and either pull a state value or
        // generate a property value.
        foreach ($properties as &$property) {
            if (isset($setByState[$property->name])) {
                $property->value = $setByState[$property->name];

                continue;
            }

            $property->value = PropertyFactory::new()
                ->name($property->name)
                ->types($property->types)
                ->make();
        }

        return $adapter->createClass($class, $properties);
    }

    private function makeDtoFromReflection(ReflectionClass $class)
    {
        $className = $class->getName();

        if (array_key_exists($className, static::$adapterCache)) {
            return $this->makeDtoWithAdapter(
                static::$adapterCache[$class->getName()],
                $class
            );
        }

        foreach (static::$adapters as $adapter) {
            if ($adapter->handles($class)) {
                static::$adapterCache[$className] = $adapter;

                return $this->makeDtoWithAdapter($adapter, $class);
            }
        }

        throw new RuntimeException(
            "No adapter was found to handle [{$class->getName()}]."
        );
    }

    protected function makeMultipleDtosFromReflection(
        ReflectionClass $class,
        int $count
    ): array {
        $numberOfDtosCreated = 0;
        $dtos                = [];

        while ($numberOfDtosCreated < $count) {
            $dtos[] = $this->makeDtoFromReflection($class);
            $numberOfDtosCreated++;
        }

        return $dtos;
    }
}
