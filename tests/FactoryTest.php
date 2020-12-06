<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\CollectionFactory;
use Anteris\DataTransferObjectFactory\DataTransferObjectFactory;
use Anteris\DataTransferObjectFactory\Factory;
use Anteris\Tests\DataTransferObjectFactory\Collections\PersonCollection;
use Anteris\Tests\DataTransferObjectFactory\DataTransferObjects\PersonData;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @covers \Anteris\DataTransferObjectFactory\Factory
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_collection_returns_collection_factory()
    {
        $factory = Factory::collection(PersonCollection::class);

        $this->assertInstanceOf(CollectionFactory::class, $factory);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\Factory
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_dto_returns_dto_factory()
    {
        $factory = Factory::dto(PersonData::class);

        $this->assertInstanceOf(DataTransferObjectFactory::class, $factory);
    }
}
