<?php

namespace Anteris\Tests\DataTransferObjectFactory\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class SpatieDto extends DataTransferObject
{
    public string $firstName;
    public string $lastName;
    public string $email;
    public null | string $address;
}
