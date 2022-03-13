<?php

namespace Squingla\Collections;

/**
 * Abstract class containing basic implementation of methods in IndexCollection.
 *
 * @template T
 * @template-extends AbstractCollection<T>
 * @template-implements IndexedCollection<T>
 */
abstract class AbstractIndexedCollection extends AbstractCollection implements IndexedCollection
{
    public function get(int $index): mixed
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

}
