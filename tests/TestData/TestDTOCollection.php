<?php

namespace Tests\TestData;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class TestDTOCollection extends DataTransferObjectCollection
{
    public function current(): TestDTO
    {
        return parent::current();
    }

    public function offsetGet($offset): TestDTO
    {
        return parent::offsetGet($offset);
    }
}
