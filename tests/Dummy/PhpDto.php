<?php

namespace Anteris\Tests\DataTransferObjectFactory\Dummy;

class PhpDto
{
    public static bool $booted = false;
    
    public string $firstName;
    public string $lastName;
    public null | bool $active;
}
