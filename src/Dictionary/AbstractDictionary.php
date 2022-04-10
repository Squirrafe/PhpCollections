<?php

namespace Squingla\Collections\Dictionary;

use Squingla\Collections\AbstractCollection;
use Squingla\Collections\Dictionary\Tuple\Tuple;
use Squingla\Collections\ImmutableException;
use Squingla\Collections\Lists\IndexedCollection;
use Squingla\Collections\NoSuchElementException;

/**
 * Implementations of basic methods from Dictionary interface.
 *
 * @template K
 * @template V
 * @template-extends AbstractCollection<Tuple<K,V>>
 * @template-implements Dictionary<K,V>
 */
abstract class AbstractDictionary extends AbstractCollection implements Dictionary
{
    /**
     * @param K $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasKey($offset);
    }

    /**
     * @param K $offset
     * @return V
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param K $offset
     * @param V $value
     * @return void
     * @throws ImmutableException
     */
    public function offsetSet($offset, $value): void
    {
        throw new ImmutableException("Cannot call offsetSet method on immutable collection.");
    }

    /**
     * @param K $offset
     * @return void
     * @throws ImmutableException
     */
    public function offsetUnset($offset): void
    {
        throw new ImmutableException("Cannot call offsetUnset method on immutable collection.");
    }

    /**
     * @param K $index
     * @return V
     */
    public function __invoke($index)
    {
        return $this->get($index);
    }

    /**
     * @param Tuple<K,V> $value
     * @return Dictionary<K,V>
     */
    public function appended($value): Dictionary
    {
        return $this->put($value->getKey(), $value->getValue());
    }

    /**
     * @param Tuple<K,V> $value
     * @return Dictionary<K,V>
     */
    public function prepended($value): Dictionary
    {
        return $this->put($value->getKey(), $value->getValue());
    }

    /**
     * @param K $key
     * @return V
     * @throws NoSuchElementException
     */
    public function get($key)
    {
        $optional = $this->getOption($key);
        if ($optional->isEmpty()) {
            throw new NoSuchElementException("There is no value under given key");
        }
        return $optional->get();
    }

    /**
     * @param K $key
     * @param V $value
     * @return Dictionary<K,V>
     */
    public function put($key, $value): Dictionary
    {
        if ($this->hasKey($key)) {
            return $this;
        }
        return $this->set($key, $value);
    }

    public function isEmpty(): bool
    {
        return $this->getLength() === 0;
    }

    public function nonEmpty(): bool
    {
        return $this->getLength() > 0;
    }

    /**
     * @param callable(Tuple<K,V>): bool $filter
     * @return int
     */
    public function count(callable $filter): int
    {
        return $this->filter($filter)->getLength();
    }

    /**
     * @param callable(Tuple<K,V>): bool $filter
     * @return bool
     */
    public function exists(callable $filter): bool
    {
        return $this->filter($filter)->nonEmpty();
    }

    /**
     * @param callable(Tuple<K,V>): bool $filter
     * @return bool
     */
    public function forAll(callable $filter): bool
    {
        return $this->filter($filter)->getLength() === $this->getLength();
    }

    /**
     * @return IndexedCollection<K>
     */
    public function keyList(): IndexedCollection
    {
        /** @var IndexedCollection<Tuple<K,V>> $list */
        $list = $this->toList();
        /** @var IndexedCollection<K> $keys */
        $keys = $list->map(fn (Tuple $tuple) => $tuple->getKey());

        return $keys;
    }

    /**
     * @return IndexedCollection<V>
     */
    public function valueList(): IndexedCollection
    {
        /** @var IndexedCollection<Tuple<K,V>> $list */
        $list = $this->toList();
        /** @var IndexedCollection<V> $values */
        $values = $list->map(fn (Tuple $tuple) => $tuple->getValue());

        return $values;
    }
}
