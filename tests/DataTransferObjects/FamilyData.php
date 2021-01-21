<?php

namespace Anteris\Tests\DataTransferObjectFactory\DataTransferObjects;

use Anteris\Tests\DataTransferObjectFactory\Collections\PersonCollection;
use Spatie\DataTransferObject\DataTransferObject;

class FamilyData extends DataTransferObject
{
    public PersonDataDocBlock $person1;
    public PersonDataDocBlock $person2;

    public PersonCollection $children;
}
