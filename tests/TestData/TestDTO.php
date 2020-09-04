<?php

namespace Tests\TestData;

use DateTime;
use Spatie\DataTransferObject\DataTransferObject;

class TestDTO extends DataTransferObject
{
    public int $id;
    public string $text;
    public DateTime $date;
    public float $float;
    public array $anArray;
    public bool $boolean;
    public $random;

    public static int $testing;
}
