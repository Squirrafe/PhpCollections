<?php

namespace Squingla\Collections\Dictionary\Tuple;

/**
 * Simplest form of an immutable tuple.
 *
 * @template K
 * @template V
 * @template-implements Tuple<K,V>
 */
class SimpleTuple implements Tuple
{
    /** @var K $key */
    private $key;
    /** @var V $value */
    private $value;

    /**
     * @param K $key
     * @param V $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return K
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return V
     */
    public function getValue()
    {
        return $this->value;
    }
}
