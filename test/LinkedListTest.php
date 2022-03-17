<?php

namespace Squingla\Test\Collections;

use Squingla\Collections\AbstractIndexedCollection;
use Squingla\Collections\LinkedList;

class LinkedListTest extends AbstractIndexedCollectionTest
{
    protected function getInstanceWithElements(array $elements): AbstractIndexedCollection
    {
        return LinkedList::with($elements);
    }
}
