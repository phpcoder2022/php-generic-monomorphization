<?php

namespace PhpCoder2022\PhpGenericMonomorphization\Utils\Lists;

use PhpCoder2022\PhpGenericMonomorphization\Utils\T;

class TemplateList implements \IteratorAggregate
{
    /** @var T[] */
    private array $list;

    public function __construct(T ...$items)
    {
        $this->list = $items;
    }

    public function getIterator(): TemplateIterator
    {
        return new TemplateIterator($list);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(int $index): T
    {
        $this->checkIndex($index, IndexMode::OnlyExists);
        return $this->list[$index];
    }

    public function safeGet(int $index): T|null
    {
        return $this->list[$index] ?? null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(int $index, T $value): void
    {
        $this->checkIndex($index, IndexMode::Adding);
        $this->list[$index] = $value;
    }

    public function add(T $value): void
    {
        $this->list[] = $value;
    }

    public function delete(int ...$indexes): void
    {
        /** @var T[] */
        $newList = [];
        foreach ($this->list as $index => $item) {
            if (in_array($index, $indexes)) {
                $newList[] = $item;
            }
        }
        $this->list = $newList;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function checkIndex(int $index, IndexMode $mode): void
    {
        $correction = $mode === IndexMode::OnlyExists ? -1 : 0;
        if ($index < 0 || $index > ($len = count($this->list)) + $correction) {
            throw new \InvalidArgumentException("index $index is incorrect. list len: $len");
        }
    }

    public function length(): int
    {
        return count($this->list);
    }

    /**
     * @param T|(\Closure(T): bool)|(\Closure(int): bool)|(\Closure(TemplatePair): bool) $needle
     */
    public function find(
        T|\Closure $needle,
        FindMode $resultMode = FindMode::Value,
        FindMode $checkingMode = FindMode::Value,
    ): T|int|TemplatePair|null {
        foreach ($this->list as $index => $value) {
            $entityForChecking = $this->getFindModeEntity($index, $value, $checkingMode);
            $found = $needle instanceof \Closure ? $needle($entityForChecking) : $value === $needle;
            if ($found) {
                return $this->getFindModeEntity($index, $value, $resultMode);
            }
        }
        return null;
    }

    /**
     * @param T|(\Closure(T): bool)|(\Closure(int): bool)|(\Closure(TemplatePair): bool) $needle
     * @disregard P1006 Т.к. я передаю нужный $resultMode, union-тип return'a можно сузить
     */
    public function findValue(
        T|\Closure $needle,
        FindMode $checkingMode = FindMode::Value,
    ): T|null {
        return $this->find($needle, FindMode::Value, $checkingMode);
    }

    /**
     * @param T|(\Closure(T): bool)|(\Closure(int): bool)|(\Closure(TemplatePair): bool) $needle
     * @disregard P1006 Т.к. я передаю нужный $resultMode, union-тип return'a можно сузить
     */
    public function findIndex(
        T|\Closure $needle,
        FindMode $checkingMode = FindMode::Value,
    ): ?int {
        return $this->find($needle, FindMode::Index, $checkingMode);
    }

    /**
     * @param T|(\Closure(T): bool)|(\Closure(int): bool)|(\Closure(TemplatePair): bool) $needle
     * @disregard P1006 Т.к. я передаю нужный $resultMode, union-тип return'a можно сузить
     */
    public function findPair(
        T|\Closure $needle,
        FindMode $checkingMode = FindMode::Value,
    ): ?TemplatePair {
        return $this->find($needle, FindMode::Both, $checkingMode);
    }

    private function getFindModeEntity(int $index, T $value, FindMode $mode): int|T|TemplatePair
    {
        switch ($mode) {
            case FindMode::Value:
                return $value;
            case FindMode::Index:
                return $index;
            case FindMode::Both:
                return new TemplatePair($index, $value);
        }
        throw new \InvalidArgumentException("Mode $mode is incorrect");
    }

    /**
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->list;
    }
}
