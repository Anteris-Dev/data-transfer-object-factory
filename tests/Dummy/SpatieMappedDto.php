<?php

namespace Anteris\Tests\DataTransferObjectFactory\Dummy;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class SpatieMappedDto extends DataTransferObject
{
    #[MapFrom('first_name')]
    public string $firstName;
}
