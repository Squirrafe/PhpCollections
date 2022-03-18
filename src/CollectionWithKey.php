<?php

namespace Squingla\Collections;

use ArrayAccess;

/**
 * Basic interface for all collections that use some key as an identifier.
 *
 * @template K
 * @template V
 * @template-extends ArrayAccess<K,V>
 */
interface CollectionWithKey extends ArrayAccess
{
    /**
     * Returns element of collection with given key. If key does not exist, throws exception.
     *
     * @param K $key
     * @return V
     * @throws NoSuchElementException if given key does not have a value
     */
    public function get(mixed $key): mixed;

    /**
     * Returns optional containing element of collection with given key. If key does not exist, returns empty optional.
     * @param K $key
     * @return Optional<V>
     */
    public function getOption(mixed $key): Optional;

    /**
     * Returns element under chosen key. Behaviour of this method is identical to `get()`, but allows for passing
     * indexed collections as a K->V callables.
     *
     * @param K $key
     * @return V
     * @throws NoSuchElementException
     */
    public function __invoke(mixed $key): mixed;
}
