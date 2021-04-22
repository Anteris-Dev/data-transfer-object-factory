<?php

namespace Anteris\DataTransferObjectFactory\Data;

class PropertyData
{
    public function __construct(
        public string $name,
        public array $types,
        public $value = null
    ) {
    }
}
