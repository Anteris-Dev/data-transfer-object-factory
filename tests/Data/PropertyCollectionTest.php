<?php

namespace Anteris\Tests\DataTransferObjectFactory\Data;

use Anteris\DataTransferObjectFactory\Data\PropertyCollection;
use Anteris\DataTransferObjectFactory\Data\PropertyData;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PropertyCollectionTest extends TestCase
{
    public function test_it_can_only_accept_property_data_objects()
    {
        $collection = new PropertyCollection;

        $this->assertEmpty($collection);

        $property      = new PropertyData('test', ['string']);
        $collection[0] = $property;

        $this->assertSame($property, $collection[0]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("[Anteris\DataTransferObjectFactory\Data\PropertyCollection] offset must be of type [Anteris\DataTransferObjectFactory\Data\PropertyData].");

        $property      = new DateTime('now');
        $collection[0] = $property;
    }
}
