<?php

namespace Squingla\Test\Collections;

use Squingla\Collections\Dictionary;

trait DictionaryTestTrait
{
    use TestTrait;

    /**
     * @template K
     * @template V
     * @param array{K,V}[] $elements
     * @return Dictionary<K,V>
     */
    protected abstract function getInstanceWithElements(array $elements): Dictionary;
}