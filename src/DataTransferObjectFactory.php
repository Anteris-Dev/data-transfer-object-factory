<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\Exceptions\InvalidCollectionException;
use Anteris\DataTransferObjectFactory\Exceptions\InvalidDataTransferObjectException;
use ReflectionClass;
use ReflectionProperty;
use Spatie\DataTransferObject\DataTransferObject;

class DataTransferObjectFactory
{
    protected string $collectionClass;
    protected int $count;
    protected string $dataTransferObjectClass;

    public static function new()
    {
        return new static;
    }

    /***************************************************************************
     * Factory Options
     **************************************************************************/

    /**
     * Sets the collection we are working with.
     */
    public function collection(string $collection)
    {
        if (! Validator::isDTOCollection($collection)) {
            throw new InvalidCollectionException(
                'Class must be an instance of Spatie\DataTransferObject\DataTransferObjectCollection!'
            );
        }

        $clone                  = clone $this;
        $clone->collectionClass = $collection;

        return $clone;
    }

    /**
     * Sets the number of Data Transfer Objects we should generate.
     */
    public function count(int $count)
    {
        $clone        = clone $this;
        $clone->count = $count;

        return $clone;
    }

    /**
     * Sets a random number of Data Transfer Objects we should generate.
     */
    public function random(int $min = 3, int $max = 100)
    {
        return $this->count(random_int($min, $max));
    }

    /**
     * Sets the Data Transfer Object we are working with.
     */
    public function dto(string $dataTransferObject)
    {
        if (! Validator::isDTO($dataTransferObject)) {
            throw new InvalidDataTransferObjectException(
                'Class must be an instance of Spatie\DataTransferObject\DataTransferObject!'
            );
        }

        $clone                          = clone $this;
        $clone->dataTransferObjectClass = $dataTransferObject;

        return $clone;
    }

    /***************************************************************************
     * DTO Creator
     **************************************************************************/

    public function make()
    {
        if (! isset($this->dataTransferObjectClass)) {
            throw new InvalidDataTransferObjectException(
                'Please specify a Data Transfer Object to be generated!'
            );
        }

        if (! isset($this->count) && ! isset($this->collectionClass)) {
            return $this->makeDTO();
        }

        $multipleDTOs = $this->makeDTOs(
            $this->count ?? random_int(3, 100)
        );

        if (! isset($this->collectionClass)) {
            return $multipleDTOs;
        }

        return CollectionFactory::new()
            ->collection($this->collectionClass)
            ->fill($multipleDTOs)
            ->make();
    }

    protected function makeDTO(): DataTransferObject
    {
        $class      = new ReflectionClass($this->dataTransferObjectClass);
        $parameters = [];
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $parameters[$property->getName()] = PropertyFactory::new()->make($property);
        }

        return (new $this->dataTransferObjectClass($parameters));
    }

    protected function makeDTOs(int $count): array
    {
        $numberOfDtosCreated = 0;
        $dtos                = [];

        while ($numberOfDtosCreated < $count) {
            $dtos[] = $this->makeDTO();
            $numberOfDtosCreated++;
        }

        return $dtos;
    }
}
