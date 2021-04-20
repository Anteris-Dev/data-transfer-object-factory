<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\PropertyFactory;
use Anteris\FakerMap\FakerMap;
use DateTime;
use PHPUnit\Framework\TestCase;

class PropertyFactoryTest extends TestCase
{
    public function test_new_returns_new_instance()
    {
        $instance1 = new PropertyFactory;
        $instance2 = $instance1->new();

        $this->assertNotSame($instance1, $instance2);
    }

    public function test_property_type_generation()
    {
        // Test default types
        $this->assertIsArray(PropertyFactory::new()->type('array')->make());
        $this->assertIsBool(PropertyFactory::new()->type('bool')->make());
        $this->assertIsInt(PropertyFactory::new()->type('int')->make());
        $this->assertIsFloat(PropertyFactory::new()->type('float')->make());
        $this->assertIsString(PropertyFactory::new()->type('string')->make());
        $this->assertTrue(
            PropertyFactory::new()->type('DateTime')->make() instanceof DateTime
        );

        // Test multiple types
        $property = PropertyFactory::new()->types(['array', 'float'])->make();

        $this->assertTrue(is_array($property) || is_float($property));

        // Test provider
        PropertyFactory::registerProvider('decimal', function (FakerMap $faker) {
            return $faker->faker()->randomFloat();
        });

        $this->assertIsFloat(PropertyFactory::new()->type('decimal')->make());

        // Test random types
        $result = PropertyFactory::new()->make();

        $this->assertTrue(
            is_array($result) ||
            is_bool($result) ||
            is_int($result) ||
            is_float($result) ||
            is_string($result) ||
            $result instanceof DateTime
        );
    }
}
