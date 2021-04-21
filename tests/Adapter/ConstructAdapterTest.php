<?php

namespace Anteris\Tests\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Adapter\ConstructAdapter;
use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use Anteris\Tests\DataTransferObjectFactory\Dummy\ImmutableDto;
use Anteris\Tests\DataTransferObjectFactory\Dummy\PhpGetterSetterClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ConstructAdapterTest extends TestCase
{
    public function test_it_cannot_handle_objects_without_construct()
    {
        $adapter = new ConstructAdapter;

        $this->assertFalse($adapter->handles(
            new ReflectionClass(PhpGetterSetterClass::class)
        ));
    }

    public function test_it_can_handle_objects_with_construct()
    {
        $adapter = new ConstructAdapter;

        $this->assertTrue($adapter->handles(
            new ReflectionClass(ImmutableDto::class)
        ));

        $adapter->getProperties(
            new ReflectionClass(ImmutableDto::class)
        );
    }

    public function test_it_can_get_properties()
    {
        $adapter = new ConstructAdapter;

        $propertiesNeeded = new PropertyCollection([
            new PropertyData('firstName', ['string']),
            new PropertyData('lastName', ['string']),
            new PropertyData('email', ['string']),
            new PropertyData('address', ['string', 'null']),
        ]);

        $this->assertEquals($propertiesNeeded, $adapter->getProperties(
            new ReflectionClass(ImmutableDto::class)
        ), 'Failed asserting that it can get properties.');
    }

    public function test_it_can_create_class()
    {
        $adapter = new ConstructAdapter;

        $propertiesNeeded = new PropertyCollection([
            new PropertyData('firstName', ['string'], 'Aidan'),
            new PropertyData('lastName', ['string'], 'Casey'),
            new PropertyData('email', ['string'], 'aidan.casey@example.com'),
            new PropertyData('address', ['string', 'null'], '123 Test Ave.'),
        ]);

        $dto = $adapter->createClass(
            new ReflectionClass(ImmutableDto::class),
            $propertiesNeeded
        );

        $this->assertInstanceOf(ImmutableDto::class, $dto);
        $this->assertSame('Aidan', $dto->getFirstName());
        $this->assertSame('Casey', $dto->getLastName());
        $this->assertSame('aidan.casey@example.com', $dto->getEmail());
        $this->assertSame('123 Test Ave.', $dto->getAddress());
    }
}
