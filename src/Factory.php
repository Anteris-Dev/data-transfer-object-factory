<?php

namespace Anteris\DataTransferObjectFactory;

class Factory
{
    public static function dto(string $dto): DataTransferObjectFactory
    {
        return DataTransferObjectFactory::new()->dto($dto);
    }

    public static function collection(string $collection): CollectionFactory
    {
        return CollectionFactory::new()->collection($collection);
    }
}
