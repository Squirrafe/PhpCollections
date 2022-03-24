<?php

namespace Squingla\Test\Collections\Dictionary\Tuple;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\Dictionary\Tuple\SimpleTuple;

class SimpleTupleTest extends TestCase
{
    /**
     * @template K
     * @template V
     * @dataProvider dataProvider
     * @param K $key
     * @param V $value
     * @param SimpleTuple<K,V> $tuple
     */
    public function testGetKey(
        mixed $key,
        mixed $value,
        SimpleTuple $tuple,
    ): void {
        self::assertSame($key, $tuple->getKey());
    }

    /**
     * @template K
     * @template V
     * @dataProvider dataProvider
     * @param K $key
     * @param V $value
     * @param SimpleTuple<K,V> $tuple
     */
    public function testGetValue(
        mixed $key,
        mixed $value,
        SimpleTuple $tuple,
    ): void {
        self::assertSame($value, $tuple->getValue());
    }

    public function dataProvider(): iterable
    {
        yield [15, 33, new SimpleTuple(15, 33)];
        yield [48, 72, new SimpleTuple(48, 72)];
        yield ["foo", "bar", new SimpleTuple("foo", "bar")];
    }
}
