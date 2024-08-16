<?php

namespace PhpCoder2022\PhpGenericMonomorphization\Utils\Lists;

use PhpCoder2022\PhpGenericMonomorphization\Utils\T;

class TemplatePair
{
    public function __construct(
        public readonly int $index,
        public readonly T $value,
    ) {
    }
}
