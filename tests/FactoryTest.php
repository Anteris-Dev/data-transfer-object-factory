<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\Factory;
use Anteris\Tests\DataTransferObjectFactory\Dummy\PhpDto;
use Anteris\Tests\DataTransferObjectFactory\Dummy\StrangeDto;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FactoryTest extends TestCase
{
    public function test_new_returns_new_instance()
    {
        $instance1 = new Factory;
        $instance2 = $instance1->new();

        $this->assertNotSame($instance1, $instance2);
    }

    public function test_it_will_not_accept_class_that_does_not_exist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class [Foo\Bar\Willy\Woo] does not exist.');

        Factory::new('Foo\Bar\Willy\Woo');
    }

    public function test_it_will_not_make_without_a_valid_dto()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Please specify a Data Transfer Object to be generated.');

        Factory::new()->make();
    }

    public function test_it_cannot_make_a_dto_without_an_adapter()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("No adapter was found to handle [Anteris\Tests\DataTransferObjectFactory\Dummy\StrangeDto].");

        Factory::new(StrangeDto::class)->make();
    }

    public function test_it_can_make_dto()
    {
        $dto = Factory::new()->dto(PhpDto::class)->make();

        $this->assertInstanceOf(PhpDto::class, $dto);
    }

    public function test_it_can_make_dto_with_state()
    {
        $dto = Factory::new()->dto(PhpDto::class)->make([
            'firstName' => 'Aidan',
            'lastName'  => 'Casey',
        ]);

        $this->assertInstanceOf(PhpDto::class, $dto);
        $this->assertSame('Aidan', $dto->firstName);
        $this->assertSame('Casey', $dto->lastName);
    }

    public function test_it_can_make_multiple_dtos()
    {
        $dtos = Factory::new()->dto(PhpDto::class)->count(5)->make();

        $this->assertCount(5, $dtos);
        $this->assertContainsOnlyInstancesOf(PhpDto::class, $dtos);

        $randomDtos = Factory::new()->dto(PhpDto::class)->random()->make();
        $this->assertTrue(count($randomDtos) >= 3 && count($randomDtos) <= 100);
    }

    public function test_it_can_make_multiple_dtos_with_states()
    {
        $dtos = Factory::new()->dto(PhpDto::class)->count(3)->states([
            ['firstName' => 'Aidan'],
            ['lastName' => 'Casey'],
        ])->make();

        $this->assertCount(3, $dtos);
        $this->assertContainsOnlyInstancesOf(PhpDto::class, $dtos);

        foreach ($dtos as $dto) {
            $this->assertSame('Aidan', $dto->firstName);
            $this->assertSame('Casey', $dto->lastName);
        }
    }

    public function test_it_can_make_dtos_with_sequential_values()
    {
        $dtos = Factory::new()->dto(PhpDto::class)->count(3)->sequence(
            ['firstName' => 'Aidan'],
            ['lastName' => 'Casey']
        )->make();

        $this->assertCount(3, $dtos);

        $this->assertSame('Aidan', $dtos[0]->firstName);
        $this->assertNotSame('Casey', $dtos[0]->lastName);

        $this->assertSame('Casey', $dtos[1]->lastName);
        $this->assertNotSame('Aidan', $dtos[1]->firstName);

        $this->assertSame('Aidan', $dtos[2]->firstName);
        $this->assertNotSame('Casey', $dtos[2]->lastName);
    }
}
