<?php

namespace Anteris\Tests\DataTransferObjectFactory\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PersonDataDocBlock extends DataTransferObject
{
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $email;

    /** @var string */
    public $homeAddress;

    /** @var string */
    public $companyName;

    /** @var string */
    public $workAddress;
}
