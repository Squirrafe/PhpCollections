<?php

namespace Squingla\Collections\Lists;

use Squingla\Collections\AbstractCollection;
use Squingla\Collections\ImmutableException;
use Squingla\Collections\NoSuchElementException;

/**
 * Abstract class containing basic implementation of methods in IndexCollection.
 *
 * @template T
 * @template-extends AbstractCollection<T>
 * @template-implements IndexedCollection<T>
 */
abstract class AbstractIndexedCollection extends AbstractCollection implements IndexedCollection
{
    /**
     * @param int $index
     * @return T
     * @throws NoSuchElementException
     */
    public function get(mixed $index): mixed
    {
        $optional = $this->getOption($index);
        if ($optional->nonEmpty()) {
            return $optional->get();
        }

        throw new NoSuchElementException("There is no element of collection under index $index");
    }

    public function splitAt(int $index): array
    {
        return [
            $this->take($index),
            $this->drop($index),
        ];
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_int($offset) && $offset >= 0 && $offset < $this->getLength();
    }

    /**
     * @param int $offset
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param int $offset
     * @param T $value
     * @return void
     * @throws ImmutableException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new ImmutableException("Cannot call offsetSet method on immutable collection.");
    }

    /**
     * @param int $offset
     * @return void
     * @throws ImmutableException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new ImmutableException("Cannot call offsetUnset method on immutable collection.");
    }

    /**
     * @param int $index
     * @return T
     */
    public function __invoke(mixed $index): mixed
    {
        return $this->get($index);
    }
}
