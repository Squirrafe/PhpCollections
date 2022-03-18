<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\ArrayDictionary;
use Squingla\Collections\Dictionary;
use Squingla\Collections\SimpleTuple;

class ArrayDictionaryTest extends TestCase
{
    use DictionaryTestTrait;

    protected function getInstanceWithElements(array $elements): Dictionary
    {
        return ArrayDictionary::fromTupleArrays(...$elements);
    }

    protected function getTestInstance(): TestCase
    {
        return $this;
    }

    public function testCreateEmpty(): void
    {
        self::assertTrue(ArrayDictionary::empty()->isEmpty());
    }

    public function testCreateFromIndexedArray(): void
    {
        $dict = ArrayDictionary::fromIndexedArray(['foo' => 'bar', 'biz' => 'baz']);
        self::assertSame(2, $dict->getLength());

        self::assertTrue($dict->hasKey('foo'));
        self::assertTrue($dict->hasKey('biz'));

        self::assertSame('bar', $dict('foo'));
        self::assertSame('baz', $dict['biz']);
    }

    public function testCreateFromTuples(): void
    {
        $dict = ArrayDictionary::fromTuples(
            new SimpleTuple('foo', 'bar'),
            new SimpleTuple('biz', 'baz'),
        );
        self::assertSame(2, $dict->getLength());

        self::assertTrue($dict->hasKey('foo'));
        self::assertTrue($dict->hasKey('biz'));

        self::assertSame('bar', $dict('foo'));
        self::assertSame('baz', $dict['biz']);
    }

    public function testCreateFromTupleArrays(): void
    {
        $dict = ArrayDictionary::fromTupleArrays(
            ['foo', 'bar'],
            ['biz', 'baz'],
        );
        self::assertSame(2, $dict->getLength());

        self::assertTrue($dict->hasKey('foo'));
        self::assertTrue($dict->hasKey('biz'));

        self::assertSame('bar', $dict('foo'));
        self::assertSame('baz', $dict['biz']);
    }
}
