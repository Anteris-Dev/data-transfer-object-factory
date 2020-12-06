<?php

namespace Anteris\Tests\DataTransferObjectFactory\Collections;

use Anteris\Tests\DataTransferObjectFactory\DataTransferObjects\PersonData;
use Spatie\DataTransferObject\DataTransferObjectCollection;

class PersonCollection extends DataTransferObjectCollection
{
    public function current(): PersonData
    {
        return parent::current();
    }

    public function offsetGet($offset): PersonData
    {
        return parent::offsetGet($offset);
    }
}
