<?php

declare(strict_types=1);

namespace PhpCoder2022\PhpGenericMonomorphization\Utils;

use PhpCoder2022\PhpGenericMonomorphization\ExampleClasses\ExampleDTO;
use PhpCoder2022\PhpGenericMonomorphization\ExampleClasses\OtherExampleDTO;

class T
{
    public string $string;
    public int $int;
    public int|float $number;
    public string|int $stringOrInt;
    public string|int|float $stringOrNumber;
    public ExampleDTO $exampleDTO;
    public ExampleDTO|OtherExampleDTO $anyExampleDTO;

    private function __construct()
    {
    }
}
