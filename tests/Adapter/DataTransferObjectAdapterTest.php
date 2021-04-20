<?php

namespace Anteris\Tests\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Adapter\DataTransferObjectAdapter;
use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use Anteris\Tests\DataTransferObjectFactory\Dummy\PhpDto;
use Anteris\Tests\DataTransferObjectFactory\Dummy\SpatieDto;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DataTransferObjectAdapterTest extends TestCase
{
    public function test_it_can_only_handle_spatie_dtos()
    {
        $adapter = new DataTransferObjectAdapter;

        $this->assertFalse(
            $adapter->handles(new ReflectionClass(PhpDto::class))
        );

        $this->assertTrue(
            $adapter->handles(new ReflectionClass(SpatieDto::class))
        );
    }

    public function test_it_can_create_class()
    {
        $adapter = new DataTransferObjectAdapter;

        $propertiesNeeded = new PropertyCollection([
            new PropertyData('firstName', ['string'], 'Aidan'),
            new PropertyData('lastName', ['string'], 'Casey'),
            new PropertyData('email', ['string'], 'aidan.casey@example.com'),
            new PropertyData('address', ['string', 'null'], '123 Test Ave.'),
        ]);

        $dto = $adapter->createClass(new ReflectionClass(SpatieDto::class), $propertiesNeeded);

        $this->assertInstanceOf(SpatieDto::class, $dto);
        $this->assertSame('Aidan', $dto->firstName);
        $this->assertSame('Casey', $dto->lastName);
        $this->assertSame('aidan.casey@example.com', $dto->email);
        $this->assertSame('123 Test Ave.', $dto->address);
    }

    public function test_it_can_get_properties()
    {
        $adapter = new DataTransferObjectAdapter;

        $propertiesNeeded = new PropertyCollection([
            new PropertyData('firstName', ['string']),
            new PropertyData('lastName', ['string']),
            new PropertyData('email', ['string']),
            new PropertyData('address', ['string', 'null']),
        ]);

        $this->assertEquals($propertiesNeeded, $adapter->getProperties(
            new ReflectionClass(SpatieDto::class)
        ));
    }
}
