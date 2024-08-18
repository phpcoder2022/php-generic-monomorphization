<?php

declare(strict_types=1);

namespace PhpCoder2022\PhpGenericMonomorphization\ExampleClasses;

class ExampleDTO
{
    public function __construct(
        public readonly string $fieldA,
        public readonly string $fieldB,
    ) {
    }
}
