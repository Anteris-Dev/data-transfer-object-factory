<?php

namespace Anteris\Tests\DataTransferObjectFactory\Adapter;

use Anteris\DataTransferObjectFactory\Adapter\PublicPropertyAdapter;
use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use Anteris\Tests\DataTransferObjectFactory\Dummy\PhpDto;
use Anteris\Tests\DataTransferObjectFactory\Dummy\PhpGetterSetterClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PublicPropertyAdapterTest extends TestCase
{
    public function test_it_cannot_handle_classes_without_public_properties()
    {
        $adapter = new PublicPropertyAdapter;

        $this->assertFalse(
            $adapter->handles(
                new ReflectionClass(PhpGetterSetterClass::class)
            )
        );
    }

    public function test_it_can_handle_classes_with_public_properties()
    {
        $adapter = new PublicPropertyAdapter;

        $this->assertTrue($adapter->handles(
            new ReflectionClass(PhpDto::class)
        ));
    }

    public function test_it_can_get_properties()
    {
        $adapter = new PublicPropertyAdapter;

        $propertiesNeeded = new PropertyCollection([
            new PropertyData('firstName', ['string']),
            new PropertyData('lastName', ['string']),
            new PropertyData('active', ['bool', 'null']),
        ]);

        $this->assertEquals($propertiesNeeded, $adapter->getProperties(
            new ReflectionClass(PhpDto::class)
        ));
    }
}
