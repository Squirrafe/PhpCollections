<?php

namespace Squingla\Test\Collections\Lists;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\Lists\AbstractIndexedCollection;
use Squingla\Collections\Lists\LinkedList;

class LinkedListTest extends TestCase
{
    use IndexedCollectionTestTrait;

    /**
     * @template T
     * @param T[] $elements
     * @return AbstractIndexedCollection<T>
     */
    protected function getInstanceWithElements(array $elements): AbstractIndexedCollection
    {
        return LinkedList::with($elements);
    }

    protected function getTestInstance(): TestCase
    {
        return $this;
    }

    public function testLinkedListWith(): void
    {
        $elements = [15, 33];
        $list = LinkedList::with($elements);
        self::assertSame($elements, $list->toNative());
    }

    public function testLinkedListEmpty(): void
    {
        self::assertSame([], LinkedList::empty()->toNative());
    }
}
