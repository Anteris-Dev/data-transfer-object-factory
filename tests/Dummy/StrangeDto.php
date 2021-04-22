<?php

namespace Anteris\Tests\DataTransferObjectFactory\Dummy;

class StrangeDto
{
    private string $name;

    public function strangeGetter(): string
    {
        return $this->name;
    }

    public function strangeSetter(string $name)
    {
        $this->name = $name;
    }
}
