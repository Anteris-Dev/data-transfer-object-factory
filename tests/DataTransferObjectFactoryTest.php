<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\DataTransferObjectFactory;
use Anteris\Tests\DataTransferObjectFactory\Collections\PersonCollection;
use Anteris\Tests\DataTransferObjectFactory\DataTransferObjects\PersonData;
use DateTime;

class DataTransferObjectFactoryTest extends AbstractTestCase
{
    /**
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_new_factory_is_empty()
    {
        $countProp = $this->getProtectedProperty(
            DataTransferObjectFactory::class,
            'count'
        );

        $collectionProp = $this->getProtectedProperty(
            DataTransferObjectFactory::class,
            'collectionClass'
        );

        $dtoProp = $this->getProtectedProperty(
            DataTransferObjectFactory::class,
            'dataTransferObjectClass'
        );

        $factory1 = DataTransferObjectFactory::new()
                        ->dto(PersonData::class)
                        ->collection(PersonCollection::class)
                        ->count(5);

        $this->assertEquals($countProp->getValue($factory1), 5);
        $this->assertEquals($dtoProp->getValue($factory1), PersonData::class);
        $this->assertEquals($collectionProp->getValue($factory1), PersonCollection::class);

        $factory2 = $factory1::new();
        
        $this->expectErrorMessage('Typed property Anteris\DataTransferObjectFactory\DataTransferObjectFactory::$count must not be accessed before initialization');
        $countProp->getValue($factory2);

        $this->expectErrorMessage('Typed property Anteris\DataTransferObjectFactory\DataTransferObjectFactory::$collectionClass must not be accessed before initialization');
        $dtoProp->getValue($factory2);

        $this->expectErrorMessage('Typed property Anteris\DataTransferObjectFactory\DataTransferObjectFactory::$dataTransferObjectClass must not be accessed before initialization');
        $collectionProp->getValue($factory2);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_will_not_accept_non_dto()
    {
        $this->expectExceptionMessage('Class must be an instance of Spatie\DataTransferObject\DataTransferObject!');
        DataTransferObjectFactory::new()->dto(DateTime::class);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_will_not_accept_non_collection()
    {
        $this->expectExceptionMessage('Class must be an instance of Spatie\DataTransferObject\DataTransferObjectCollection!');
        DataTransferObjectFactory::new()->collection(DateTime::class);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     */
    public function test_it_cannot_make_dto_if_dto_is_not_set()
    {
        $this->expectExceptionMessage('Please specify a Data Transfer Object to be generated!');
        DataTransferObjectFactory::new()->make();
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\PropertyFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_can_make_single_dto()
    {
        $dto = DataTransferObjectFactory::new()->dto(PersonData::class)->make();

        $this->assertInstanceOf(PersonData::class, $dto);
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\PropertyFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_can_make_array_of_dtos()
    {
        $dtos = DataTransferObjectFactory::new()->dto(PersonData::class)->count(3)->make();

        $this->assertIsArray($dtos);

        foreach ($dtos as $dto) {
            $this->assertInstanceOf(PersonData::class, $dto);
        }
    }

    /**
     * @covers \Anteris\DataTransferObjectFactory\CollectionFactory
     * @covers \Anteris\DataTransferObjectFactory\DataTransferObjectFactory
     * @covers \Anteris\DataTransferObjectFactory\PropertyFactory
     * @covers \Anteris\DataTransferObjectFactory\Validator
     */
    public function test_it_can_make_collection_of_dtos()
    {
        $dtos = DataTransferObjectFactory::new()
            ->dto(PersonData::class)
            ->random()
            ->collection(PersonCollection::class)
            ->make();

        $this->assertInstanceOf(PersonCollection::class, $dtos);

        foreach ($dtos as $dto) {
            $this->assertInstanceOf(PersonData::class, $dto);
        }
    }
}
