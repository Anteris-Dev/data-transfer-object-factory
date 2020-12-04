<?php

namespace Tests\TestData;

use Spatie\DataTransferObject\DataTransferObject;

class TestChildDTO extends DataTransferObject
{
    public string $firstName;
    public string $middleName;
    public string $lastName;
}
