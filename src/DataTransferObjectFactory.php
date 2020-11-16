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

    /** @var Generator An instance of phpDocumentor for use throughout this class. */
    protected static DocBlockFactory $phpDocumentor;

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

        foreach ($classProperties as $property) {
            // Skip static properties
            if ($property->isStatic()) {
                continue;
            }

            // This is kinda messy, but we wanna support docblocks!
            $docCommentType = static::getDocCommentType($property->getDocComment());

            if (
                $docCommentType &&
                (is_array($docCommentType) ||
                    $docCommentType instanceof DataTransferObject)
            ) {
                $dtoParameters[$property->getName()] = $docCommentType;

                continue;
            }

            // If the property does not have a type, randomize it
            if (! $docCommentType && ! $property->hasType()) {
                $dtoParameters[$property->getName()] = static::makeRandomType();

                continue;
            }

            // Generate a value for properties with types
            // This section gets rid of namespaces when creating the make function
            // BUT if a type was defined back in the docblock, default to that because
            // that is the behavior of DataTransferObject.
            if (! $docCommentType) {
                $propertyType = ucwords($property->getType()->getName());
                $propertyType = trim(substr($propertyType, strrpos($propertyType, '\\')), '\\');
            } else {
                $propertyType = $docCommentType;
            }

            if (! method_exists(static::class, "make$propertyType")) {
                throw new Exception("Unknown data type $propertyType!");
            }

            $dtoParameters[$property->getName()] = static::{"make$propertyType"}();

            unset($docCommentType);
            unset($propertyType);
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
    ): DataTransferObjectCollection {
        // This step just ensures we are dealing with a DTO collection
        if (! static::isDTOCollection($dtoCollectionClass)) {
            throw new Exception(
                'Class must be an instance of Spatie\DataTransferObject\DataTransferObjectCollection!'
            );
        }

        // Now start creating some DTOs!
        return new $dtoCollectionClass(
            static::makeRandomNumberOfDtos($dtoClass)
        );
    }

    /**
     * Creates a random number (between 3 and 100) of Data Transfer Objects.
     */
    public static function makeRandomNumberOfDtos($class): array
    {
        $numberOfDtos        = random_int(3, 100);
        $numberOfDtosCreated = 0;
        $dtos                = [];

        while ($numberOfDtosCreated < $numberOfDtos) {
            $dtos[] = static::make($class);
            $numberOfDtosCreated++;
        }

        return $dtos;
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
        if (! isset(static::$faker)) {
            static::$faker = Factory::create();
        }

        return static::$faker;
    }

    /**
     * Returns the instance of phpDocumentor.
     */
    protected static function phpDocumentor()
    {
        if (! isset(static::$phpDocumentor)) {
            static::$phpDocumentor = DocBlockFactory::createInstance();
        }

        return static::$phpDocumentor;
    }

    /**
     * This reads the doc comment and returns the type.
     */
    protected static function getDocCommentType($docComment)
    {
        if (! $docComment) {
            return false;
        }

        // Find the var tag, if it doesn't exist, early return
        $docblock   = static::phpDocumentor()->create($docComment);
        $var        = $docblock->getTagsByName('var');

        if (! $var) {
            return false;
        }

        // This looks bad, but basically we're always gonna generate
        // the first type, cause why bother with the second?
        $type = explode('|', $var[0]->getType())[0];

        $isArrayOfEntities = false;

        // First remove the whole iterable thing
        if (strpos($type, '[]')) {
            $type              = str_replace('[]', '', $type);
            $isArrayOfEntities = true;
        }

        // Early return if not a DTO
        if (! static::isDto($type)) {
            return $type;
        }

        // If we are an iterable of entities make that now
        if ($isArrayOfEntities) {
            return static::makeRandomNumberOfDtos($type);
        }

        return static::make($type);

        // Leaving this here to show that we could handle collections
        // if they are ever supported

        // if (static::isDTOCollection($type)) {
        //     $dtoType = static::getDTOCollectionReturnType($type);

        //     if (! $dtoType) {
        //         throw new Exception("Unable to determine return dto type of $type collection!");
        //     }

        //     $dtoParameters[$property->getName()] = static::makeCollection($dtoType, $type);
        // }
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
        if (! class_exists($class)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);

        // This step just ensures we are dealing with a DTO
        if (! $reflectionClass->newInstanceWithoutConstructor() instanceof DataTransferObject) {
            return false;
        }

        return true;
    }

    /**
     * Determines whether or not the class name passed is a Data Transfer Object collection.
     */
    protected static function isDTOCollection(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);

        // This step just ensures we are dealing with a DTO
        if (! $reflectionClass->newInstanceWithoutConstructor() instanceof DataTransferObjectCollection) {
            return false;
        }

        return true;
    }
}
