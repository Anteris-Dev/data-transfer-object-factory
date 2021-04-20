<?php

namespace Anteris\DataTransferObjectFactory\Data;

use ArrayObject;
use InvalidArgumentException;

class PropertyCollection extends ArrayObject
{
    public function offsetGet($index): PropertyData
    {
        return parent::offsetGet($index);
    }

    public function offsetSet($index, $newval): void
    {
        if (! $newval instanceof PropertyData) {
            throw new InvalidArgumentException(
                "[Anteris\DataTransferObjectFactory\Data\PropertyCollection] offset must be of type [Anteris\DataTransferObjectFactory\Data\PropertyData]."
            );
        }

        parent::offsetSet($index, $newval);
    }
}
