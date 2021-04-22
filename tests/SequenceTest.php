<?php

namespace Anteris\Tests\DataTransferObjectFactory;

use Anteris\DataTransferObjectFactory\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    public function test_it_returns_values_in_sequence()
    {
        $sequence = Sequence::make(
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
        );

        $this->assertSame([0, 1, 2], $sequence());
        $this->assertSame([3, 4, 5], $sequence());
        $this->assertSame([6, 7, 8], $sequence());
        $this->assertSame([0, 1, 2], $sequence());
    }
}
