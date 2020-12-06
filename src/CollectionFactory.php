<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\Exceptions\InvalidCollectionException;
use Anteris\DataTransferObjectFactory\Exceptions\InvalidDataTransferObjectException;

class CollectionFactory
{
    protected string $collectionClass;
    protected array $contents;
    protected string $dataTransferObjectClass;

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

    /***************************************************************************
     * Collection Creator
     **************************************************************************/

    public function make()
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
                ->make();

            return new $this->collectionClass($dtos);
        }

        return new $this->collectionClass;
    }
}
