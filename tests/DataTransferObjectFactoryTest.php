<?php

namespace Tests;

use Anteris\DataTransferObjectFactory\DataTransferObjectFactory;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Tests\TestData\TestChildDTO;
use Tests\TestData\TestClass;
use Tests\TestData\TestDTO;
use Tests\TestData\TestDTOCollection;
use Tests\TestData\TestDTOWithUnknownType;
use Tests\TestData\TestParentDTO;

class DataTransferObjectFactoryTest extends TestCase
{
    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_dto()
    {
        $this->assertInstanceOf(
            TestDTO::class,
            DataTransferObjectFactory::make(TestDTO::class)
        );
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_cannot_make_non_dto()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class must be an instance of Spatie\DataTransferObject\DataTransferObject!');
        DataTransferObjectFactory::make(TestClass::class);
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_cannot_make_dto_with_unknown_type()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown data type Carbon!');
        DataTransferObjectFactory::make(TestDTOWithUnknownType::class);
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_create_dto_with_child_dto()
    {
        $dto = DataTransferObjectFactory::make(TestParentDTO::class);

        $this->assertInstanceOf(
            TestParentDTO::class,
            $dto
        );

        $this->assertInstanceOf(
            TestChildDTO::class,
            $dto->person
        );
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_dto_collection()
    {
        $collection = DataTransferObjectFactory::makeCollection(
            TestDTO::class,
            TestDTOCollection::class
        );

        $this->assertInstanceOf(
            TestDTOCollection::class,
            $collection
        );

        $this->assertIsIterable($collection);
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_cannot_make_non_dto_collection()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Class must be an instance of Spatie\DataTransferObject\DataTransferObjectCollection!');
        DataTransferObjectFactory::makeCollection(TestDTO::class, TestClass::class);
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_array()
    {
        $this->assertIsArray(DataTransferObjectFactory::makeArray());
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_bool()
    {
        $this->assertIsBool(DataTransferObjectFactory::makeBool());
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_date_time()
    {
        $this->assertInstanceOf(DateTime::class, DataTransferObjectFactory::makeDateTime());
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_int()
    {
        $this->assertIsInt(DataTransferObjectFactory::makeInt());
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_float()
    {
        $this->assertIsFloat(DataTransferObjectFactory::makeFloat());
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_random_type()
    {
        $this->assertContains(
            gettype(DataTransferObjectFactory::makeRandomType()),
            [
                'array',
                'boolean',
                'object',
                'integer',
                'double',
                'string',
            ]
        );
    }

    /**
     * @covers Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_can_make_string()
    {
        $this->assertIsString(DataTransferObjectFactory::makeString());
    }
}
