<?php

namespace Tests\TestData;

use Spatie\DataTransferObject\DataTransferObject;

class TestDTOWithUnknownType extends DataTransferObject
{
    public \Carbon\Carbon $date;
}
