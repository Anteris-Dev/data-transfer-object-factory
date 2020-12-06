<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\FakerMap\FakerMap;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionProperty;

class PropertyFactory
{
    protected FakerMap $fakerMap;
    protected DocBlockFactory $phpDocumentor;
    protected static array $providers = [];

    public function __construct()
    {
        $this->fakerMap      = new FakerMap;
        $this->phpDocumentor = DocBlockFactory::createInstance();
    }

    public static function new()
    {
        return new static;
    }

    public static function registerProvider(string $name, callable $callback): void
    {
        static::$providers[$name] = $callback;
    }

    public function make(ReflectionProperty $property)
    {
        $type = $this->getPropertyType($property);

        // First give the providers an opportunity to handle this type
        if (isset(static::$providers[$type])) {
            return static::$providers[$type]($this->fakerMap);
        }

        // Handles an array of DTOs
        if (strpos($type, '[]') !== false) {
            $type = str_replace('[]', '', $type);

            return DataTransferObjectFactory::new()
                ->dto($type)
                ->random()
                ->make();
        }

        // Handles a DTO Collection
        if (
            Validator::isDTOCollection($type) &&
            $dtoClass = $this->getCollectionReturnType($type)
        ) {
            return CollectionFactory::new()
                ->collection($type)
                ->of($dtoClass)
                ->make();
        }

        // Handles a DTO
        if (Validator::isDTO($type)) {
            return DataTransferObjectFactory::new()
                ->dto($type)
                ->make();
        }

        // Gives Faker Map a chance to guess
        $guess = $this->fakerMap->closest($property->getName());

        if (
            $guess != null &&
            ($fakedValue = $guess->fake()) &&
            gettype($fakedValue) == $type
        ) {
            return $fakedValue;
        }

        // Defaults to creating a type
        return $this->createPropertyFromType($type);
    }

    protected function createPropertyFromType($type)
    {
        switch ($type) {
            case 'array':
                return $this->fakerMap->closest('words')->fake();
            break;

            case 'bool':
                return $this->fakerMap->closest('boolean')->fake();
            break;

            case 'DateTime':
                return $this->fakerMap->closest('dateTime')->fake();
            break;

            case 'int':
                return $this->fakerMap->closest('randomDigit')->fake();
            break;

            case 'float':
                return $this->fakerMap->closest('randomFloat')->fake();
            break;
        }

        // Default to a string
        return $this->fakerMap->closest('word')->fake();
    }

    protected function getCollectionReturnType(string $collectionClass)
    {
        $reflectionClass = new ReflectionClass($collectionClass);

        $offsetGetReturnType = $reflectionClass
                                    ->getMethod('offsetGet')
                                    ->getReturnType();

        if ($offsetGetReturnType) {
            return $offsetGetReturnType->getName();
        }

        $currentReturnType = $reflectionClass
                                ->getMethod('current')
                                ->getReturnType();

        if ($currentReturnType) {
            return $currentReturnType->getName();
        }

        return null;
    }

    protected function getDocBlockPropertyType(ReflectionProperty $property)
    {
        $docblock = $this->phpDocumentor->create($property->getDocComment());
        $var      = $docblock->getTagsByName('var');

        if ($var && isset($var[0])) {
            $types = explode('|', $var[0]->getType());

            // Picks a random type out of the options and removes the first
            // namespace character to match Reflection Type form.
            return ltrim(
                $types[array_rand($types, 1)],
                '\\'
            );
        }

        return null;
    }

    protected function getPropertyType(ReflectionProperty $property)
    {
        // If the property has a doc block, check that
        if (
            $property->getDocComment() &&
            $type = $this->getDocBlockPropertyType($property)
        ) {
            return $type;
        }

        // If the property has a type cast, check that
        $type = $property->getType();

        if ($type) {
            return $type->getName();
        }

        // Default to a random type
        return $this->getRandomPropertyType();
    }

    protected function getRandomPropertyType()
    {
        $options = array_merge(array_keys(static::$providers), [
            'array',
            'bool',
            'DateTime',
            'int',
            'float',
            'string',
        ]);

        return array_rand($options, 1);
    }
}
