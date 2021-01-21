<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\FakerMap\FakerMap;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionProperty;

class PropertyFactory
{
    protected DocBlockFactory $phpDocumentor;
    protected static array $providers = [];

    public function __construct()
    {
        $this->phpDocumentor = DocBlockFactory::createInstance();
    }

    public static function new(): self
    {
        return new self;
    }

    public static function registerProvider(string $name, callable $callback): void
    {
        static::$providers[$name] = $callback;
    }

    public function make(ReflectionProperty $property)
    {
        $type = $this->extractType($property);

        // If a provider was registered to handle this type, pass off to that.
        if (isset(static::$providers[$type])) {
            return static::$providers[$type](new FakerMap, $property);
        }

        // We will try to generate a property that matches what the property name
        // indicates it expects. (e.g. $firstName would have "John")
        $faker = FakerMap::closest($property->getName());

        // If the property was type cast, we will ensure the returned type is
        // what the property expects. Otherwise we will fallback on a value based
        // on the type.
        if ($type != null) {
            $faker = $faker->type($type)->default(
                $this->createPropertyOfType($type)
            );
        }

        // If the property did not have a type, we will fallback on a random
        // type.
        if ($type == null) {
            $faker = $faker->default($this->createPropertyOfRandomType());
        }

        return $faker->fake();
    }

    protected function extractType(ReflectionProperty $property): ?string
    {
        if (
            $property->getDocComment() &&
            $type = $this->extractDocBlockType($property)
        ) {
            return $type;
        }

        $type = $property->getType();

        if ($type) {
            return $type->getName();
        }

        return null;
    }

    protected function extractDocBlockType(ReflectionProperty $property): ?string
    {
        $docblock = $this->phpDocumentor->create($property->getDocComment());

        /** @var Var_[] An array of any variable tags. */
        $var = $docblock->getTagsByName('var');

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

    protected function extractCollectionReturnType(string $collectionClass)
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

    protected function createPropertyOfType(string $type)
    {
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
            $dtoClass = $this->extractCollectionReturnType($type)
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

        switch ($type) {
            case 'array':
                return FakerMap::words();
            break;

            case 'bool':
                return FakerMap::bool();
            break;

            case 'DateTime':
                return FakerMap::dateTime();
            break;

            case 'int':
                return FakerMap::randomDigit();
            break;

            case 'float':
                return FakerMap::randomFloat();
            break;
        }

        return FakerMap::word();
    }

    protected function createPropertyOfRandomType()
    {
        $type = array_rand([
            'array',
            'bool',
            'DateTime',
            'int',
            'float',
            'string',
        ], 1);

        return $this->createPropertyOfType($type);
    }
}
