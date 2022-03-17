<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\AbstractIndexedCollection;

abstract class AbstractIndexedCollectionTest extends TestCase
{
    /**
     * @template T
     * @param T[] $elements
     * @return AbstractIndexedCollection<T>
     */
    protected abstract function getInstanceWithElements(array $elements): AbstractIndexedCollection;

    public function testGetLength(): void
    {
        self::assertSame(0, $this->getInstanceWithElements([])->getLength());
        self::assertSame(1, $this->getInstanceWithElements(['foo'])->getLength());
        self::assertSame(2, $this->getInstanceWithElements(['foo', 'bar'])->getLength());
    }
}
