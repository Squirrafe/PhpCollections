<?php

namespace Squingla\Collections;

/**
 * Simplest form of an immutable tuple.
 *
 * @template K
 * @template V
 * @template-implements Tuple<K,V>
 */
class SimpleTuple implements Tuple
{
    /**
     * @param K $key
     * @param V $value
     */
    public function __construct(
        private readonly mixed $key,
        private readonly mixed $value,
    ) {}

    /**
     * @return K
     */
    public function getKey(): mixed
    {
        return $this->key;
    }

    /**
     * @return V
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
