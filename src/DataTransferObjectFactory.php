<?php

namespace Anteris\DataTransferObjectFactory;

use DateTime;
use Exception;
use Faker\Factory;
use Faker\Generator;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

/**
 * This class makes it easy to mock your Data Transfer Objects.
 * @author Aidan Casey <aidan.casey@anteris.com>
 */
class DataTransferObjectFactory
{
    /** @var Generator An instance of faker for use throughout this class. */
    protected static Generator $faker;

    /**
     * Creates a new DataTransferObject from its definition.
     */
    public static function make(string $class): DataTransferObject
    {
        $reflectionClass = new ReflectionClass($class);

        // This step just ensures we are dealing with a DTO
        if (! static::isDTO($class)) {
            throw new Exception(
                'Class must be an instance of Spatie\DataTransferObject\DataTransferObject!'
            );
        }

        $dtoParameters   = [];
        $classProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        $factory         = DocBlockFactory::createInstance();

        foreach ($classProperties as $property) {
            // Skip static properties
            if ($property->isStatic()) {
                continue;
            }

            // Forgive the nested IF statements, but we wanna support docblocks!
            if ($property->getDocComment()) {
                $docblock   = $factory->create($property->getDocComment());
                $var        = $docblock->getTagsByName('var');

                if ($var) {
                    $type = $var[0]->getType();

                    if (static::isDTO($type)) {
                        $dtoParameters[$property->getName()] = static::make( $type );
                        continue;
                    }

                    if (static::isDTOCollection($type)) {
                        $dtoType = static::getDTOCollectionReturnType($type);

                        if (! $dtoType) {
                            throw new Exception("Unable to determine return dto type of $type collection!");
                        }

                        $dtoParameters[$property->getName()] = static::makeCollection($dtoType, $type);
                        continue;
                    }
                }
            }

            // If the property does not have a type, randomize it
            if (! $property->hasType()) {
                $dtoParameters[$property->getName()] = static::makeRandomType();
                continue;
            }

            // Generate a value for properties with types
            // This section gets rid of namespaces when creating the make function
            $propertyType = ucwords($property->getType()->getName());
            $propertyType = trim(substr($propertyType, strrpos($propertyType, '\\')), '\\');

            if (! method_exists(static::class, "make$propertyType")) {
                throw new Exception("Unknown data type $propertyType!");
            }

            $dtoParameters[$property->getName()] = static::{"make$propertyType"}();
        }

        // Return a new instance of our DataTransferObject
        return new $class($dtoParameters);
    }

    /**
     * Creates a collection of DataTransferObjects.
     * 
     * @param  string  $dtoClass  The DataTransferObject we should be creating a collection for.
     * @param  string  $dtoCollection  The collection we should be returning.
     * 
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public static function makeCollection(
        string $dtoClass,
        string $dtoCollectionClass
    ): DataTransferObjectCollection
    {
        // This step just ensures we are dealing with a DTO collection
        if (! static::isDTOCollection($dtoCollectionClass)) {
            throw new Exception(
                'Class must be an instance of Spatie\DataTransferObject\DataTransferObjectCollection!'
            );
        }

        // Now start creating some DTOs!
        $numberOfDtos = random_int(3, 100);
        $numberOfDtosCreated = 0;
        $dtos = [];

        while ($numberOfDtosCreated < $numberOfDtos) {
            $dtos[] = static::make($dtoClass);
            $numberOfDtosCreated++;
        }

        return new $dtoCollectionClass( $dtos );
    }

    /**
     * Creates an array of words.
     */
    public static function makeArray(): array
    {
        return static::faker()->words();
    }

    /**
     * Creates a bool.
     */
    public static function makeBool(): bool
    {
        return static::faker()->boolean;
    }

    /**
     * Creates a date time object.
     */
    public static function makeDateTime(): DateTime
    {
        return static::faker()->dateTime();
    }

    /**
     * Creates an integer.
     */
    public static function makeInt(): int
    {
        return static::faker()->randomDigit;
    }

    /**
     * Creates a float.
     */
    public static function makeFloat(): float
    {
        return static::faker()->randomFloat();
    }

    /**
     * This chooses a type at random and returns the value generated.
     * The only time this is useful is if a type has not been set.
     */
    public static function makeRandomType()
    {
        $options = [
            'array',
            'bool',
            'DateTime',
            'int',
            'float',
            'string',
        ];

        $key = array_rand($options, 1);

        return static::{"make$options[$key]"}();
    }

    /**
     * Creates a string.
     */
    public static function makeString(): string
    {
        return static::faker()->word;
    }

    /**
     * Returns the instance of faker.
     */
    protected static function faker()
    {
        if (!isset(static::$faker)) {
            static::$faker = Factory::create();
        }

        return static::$faker;
    }

    /**
     * Attempts to retrieve the return type of a Data Transfer Object collection.
     */
    protected static function getDTOCollectionReturnType(string $collectionClass): ?string
    {
        $reflectionClass = new ReflectionClass($collectionClass);

        // Attempts to determine the type based on return type of current()
        $currentReturnType = $reflectionClass->getMethod('current')->getReturnType();

        if ($currentReturnType) {
            return $currentReturnType->getName();
        }

        // Attempts to determine the type based on return type of offsetGet()
        $offsetGetReturnType = $reflectionClass->getMethod('offsetGet')->getReturnType();

        if ($offsetGetReturnType) {
            return $offsetGetReturnType->getName();
        }

        return null;
    }

    /**
     * Determines whether or not the class name passed is a Data Transfer Object.
     */
    protected static function isDTO(string $class): bool
    {
        $reflectionClass = new ReflectionClass($class);

        // This step just ensures we are dealing with a DTO
        if (!$reflectionClass->newInstanceWithoutConstructor() instanceof DataTransferObject) {
            return false;
        }

        return true;
    }

    /**
     * Determines whether or not the class name passed is a Data Transfer Object collection.
     */
    protected static function isDTOCollection(string $class): bool
    {
        $reflectionClass = new ReflectionClass($class);

        // This step just ensures we are dealing with a DTO
        if (!$reflectionClass->newInstanceWithoutConstructor() instanceof DataTransferObjectCollection) {
            return false;
        }

        return true;
    }
}
