<?php

declare(strict_types=1);

namespace PhpCoder2022\PhpGenericMonomorphization\Utils\Lists;

use PhpCoder2022\PhpGenericMonomorphization\Utils\T;

class TemplateIterator implements \Iterator
{
    private int $currentIndex = 0;

    /**
     * @param T[] $list
     */
    public function __construct(private array &$list)
    {
    }

    public function current(): T
    {
        return $this->list[$this->currentIndex];
    }

    public function next(): void
    {
        $this->currentIndex++;
    }

    public function key(): int
    {
        return $this->currentIndex;
    }

    public function valid(): bool
    {
        return array_key_exists($this->currentIndex, $this->list);
    }

    public function rewind(): void
    {
        $this->currentIndex = 0;
    }
}
