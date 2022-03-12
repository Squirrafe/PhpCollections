<?php

namespace Squingla\Collections;

/**
 * @template T
 */
interface IterableCollection
{
    /**
     * @return iterable<T>
     */
    public function iterate(): iterable;
}