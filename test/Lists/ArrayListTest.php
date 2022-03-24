<?php

namespace Squingla\Test\Collections\Lists;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\Lists\ArrayList;
use Squingla\Collections\Lists\IndexedCollection;

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
