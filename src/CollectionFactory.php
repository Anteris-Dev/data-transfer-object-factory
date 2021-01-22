<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\Exceptions\InvalidCollectionException;
use Anteris\DataTransferObjectFactory\Exceptions\InvalidDataTransferObjectException;

class CollectionFactory
{
    protected string $collectionClass;
    protected array $contents;
    protected string $dataTransferObjectClass;
    protected array $states = [];

    public static function new()
    {
        return new static;
    }

    /***************************************************************************
     * Factory Options
     **************************************************************************/

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

    public function fill(array $contents)
    {
        $clone           = clone $this;
        $clone->contents = $contents;

        return $clone;
    }

    public function of(string $dataTransferObject)
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

    /**
     * Create a sequence of overrides.
     */
    public function sequence(...$sequence)
    {
        return $this->state(Sequence::make(...$sequence));
    }

    /**
     * Manually override attributes by passing an array of values.
     *
     * @param callable|array $state
     */
    public function state($state)
    {
        $clone = clone $this;

        if (! is_callable($state)) {
            $state = fn () => $state;
        }

        $clone->states[] = $state;

        return $clone;
    }

    /***************************************************************************
     * Collection Creator
     **************************************************************************/

    public function make($attributes = [])
    {
        if (! isset($this->collectionClass)) {
            throw new InvalidCollectionException('Please specify a Collection to be generated!');
        }

        if (isset($this->contents)) {
            return new $this->collectionClass($this->contents);
        }

        if (isset($this->dataTransferObjectClass)) {
            $dtos = DataTransferObjectFactory::new()
                ->dto($this->dataTransferObjectClass)
                ->random()
                ->states($this->states)
                ->make($attributes);

            return new $this->collectionClass($dtos);
        }

        return new $this->collectionClass;
    }
}
