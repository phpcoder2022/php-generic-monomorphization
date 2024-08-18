<?php

declare(strict_types=1);

namespace PhpCoder2022\PhpGenericMonomorphization\ExampleClasses;

class OtherExampleDTO
{
    public function __construct(
        public readonly string $fieldX,
        public readonly string $fieldY,
    ) {
    }
}
