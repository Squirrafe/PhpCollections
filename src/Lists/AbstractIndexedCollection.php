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
    public function get($index)
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
    public function offsetExists($offset): bool
    {
        return is_int($offset) && $offset >= 0 && $offset < $this->getLength();
    }

    /**
     * @param int $offset
     * @return T
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param int $offset
     * @param T $value
     * @return void
     * @throws ImmutableException
     */
    public function offsetSet($offset, $value): void
    {
        throw new ImmutableException("Cannot call offsetSet method on immutable collection.");
    }

    /**
     * @param int $offset
     * @return void
     * @throws ImmutableException
     */
    public function offsetUnset($offset): void
    {
        throw new ImmutableException("Cannot call offsetUnset method on immutable collection.");
    }

    /**
     * @param int $index
     * @return T
     */
    public function __invoke($index)
    {
        return $this->get($index);
    }
}
