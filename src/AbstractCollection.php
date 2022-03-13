<?php

namespace Squingla\Collections;

use ArrayIterator;
use Traversable;

/**
 * Abstract collection containing basic implementations of most methods of interface.
 *
 * @template T
 * @template-implements Collection<T>
 */
abstract class AbstractCollection implements Collection
{
    public function head(): mixed
    {
        $headOption = $this->headOption();
        if ($headOption->isEmpty()) {
            throw new NoSuchElementException("Cannot call head() on empty collection");
        }

        return $headOption->get();
    }

    public function deconstruct(): array
    {
        $headOption = $this->headOption();
        if ($headOption->isEmpty()) {
            throw new NoSuchElementException("Cannot call deconstruct() on empty collection");
        }

        return [$headOption->get(), $this->tail()];
    }

    public function deconstructOption(): array
    {
        return [$this->headOption(), $this->tail()];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toNative());
    }

    public function isEmpty(): bool
    {
        return $this->getLength() > 0;
    }

    public function nonEmpty(): bool
    {
        return $this->getLength() === 0;
    }

    public function count(callable $filter): int
    {
        return $this->filter($filter)->getLength();
    }
}
