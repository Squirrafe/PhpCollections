<?php

namespace Squingla\Collections\Dictionary\Tuple;

/**
 * Interface representing a single, immutable key-value pair.
 *
 * @template K
 * @template V
 */
interface Tuple
{
    /**
     * Returns key part of a tuple.
     *
     * @return K
     */
    public function getKey(): mixed;

    /**
     * Returns value part of a tuple.
     *
     * @return V
     */
    public function getValue(): mixed;
}
