<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\ArrayList;
use Squingla\Collections\IndexedCollection;

class ArrayListTest extends TestCase
{
    use IndexedCollectionTestTrait;

    protected function getInstanceWithElements(array $elements): IndexedCollection
    {
        return ArrayList::with($elements);
    }

    protected function getTestInstance(): TestCase
    {
        return $this;
    }
}
