<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\CollectionFactory;
use Anteris\Tests\DataTransferObjectFactory\Collections\PersonCollection;
use Anteris\Tests\DataTransferObjectFactory\DataTransferObjects\PersonData;

class CollectionTest extends AbstractTestCase
{
    /**
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory::make
     */
    public function test_it_cannot_create_collection_without_collection()
    {
        $this->expectExceptionMessage(
            'Please specify a Collection to be generated!'
        );

        (new CollectionFactory)->make();
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory::collection
     * @covers \Anteris\DataTransferObjectFactory\Validator::isDTOCollection
     */
    public function test_it_cannot_accept_non_collection()
    {
        $this->expectExceptionMessage(
            'Class must be an instance of Spatie\DataTransferObject\DataTransferObjectCollection!'
        );

        (new CollectionFactory)->collection(\DateTime::class);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory::collection
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory::make
     * @covers \Anteris\DataTransferObjectFactory\Validator::isDTOCollection
     */
    public function test_it_can_create_empty_collection()
    {
        $collection = (new CollectionFactory)
            ->collection(PersonCollection::class)
            ->make();

        $this->assertInstanceOf(PersonCollection::class, $collection);
        $this->assertEmpty($collection);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\PropertyFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_can_create_collection_of_dtos()
    {
        $collection = (new CollectionFactory)
            ->collection(PersonCollection::class)
            ->of(PersonData::class)
            ->make();

        $this->assertInstanceOf(PersonCollection::class, $collection);
        $this->assertNotEmpty($collection);
        
        foreach ($collection as $dto) {
            $this->assertInstanceOf(PersonData::class, $dto);
        }
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_cannot_make_collection_of_non_dtos()
    {
        $this->expectExceptionMessage(
            'Class must be an instance of Spatie\DataTransferObject\DataTransferObject!'
        );

        (new CollectionFactory)
            ->collection(PersonCollection::class)
            ->of(\DateTime::class)
            ->make();
    }
}
