<?php

namespace Tests\TestData;

use Spatie\DataTransferObject\DataTransferObject;

class TestParentDTO extends DataTransferObject
{
    public string $company;
    public \Tests\TestData\TestChildDTO $person;
}
