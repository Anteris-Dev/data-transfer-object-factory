<?php

namespace Anteris\Tests\DataTransferObjectFactory\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class PersonData extends DataTransferObject
{
    public $firstName;
    public string $lastName;
    public string $email;
    public string $homeAddress;

    public string $companyName;
    public string $workAddress;

    public PersonDataDocBlock $spouse;
}
