<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\Collection;
use Squingla\Collections\NoSuchElementException;
use Squingla\Collections\Optional;
use Squingla\Collections\UnsupportedTraversalException;

trait CollectionTestTrait
{
    use IterableOnceTestTrait;

    /**
     * @template T
     * @param T[] $elements
     * @return Collection<T>
     */
    protected abstract function getInstanceWithElements(array $elements): Collection;

    public function testAppended(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        $appended = $instance->appended(13);

        TestCase::assertSame([8, 1, 9, 3, 13], $appended->toNative());
    }

    public function testAppendedEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $appended = $instance->appended(13);

        TestCase::assertSame([13], $appended->toNative());
    }

    public function testPrepended(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        $appended = $instance->prepended(13);

        TestCase::assertSame([13, 8, 1, 9, 3], $appended->toNative());
    }

    public function testPrependedEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $appended = $instance->prepended(13);

        TestCase::assertSame([13], $appended->toNative());
    }

    public function testConcat(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        /** @var Collection<int> $other */
        $other = $this->getInstanceWithElements([15, 7, 18, 2]);
        $concat = $instance->concat($other);

        TestCase::assertSame([8, 1, 9, 3, 15, 7, 18, 2], $concat->toNative());
    }

    public function testHead(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        TestCase::assertSame(8, $instance->head());
    }

    public function testHeadOption(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        TestCase::assertSame(8, $instance->headOption()->getOrNull());
    }

    public function testHeadEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $this->getTestInstance()->expectException(NoSuchElementException::class);
        $instance->head();
    }

    public function testHeadOptionEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        TestCase::assertTrue($instance->headOption()->isEmpty());
    }

    public function testTail(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        $tail = $instance->tail();
        TestCase::assertSame([1, 9, 3], $tail->toNative());
    }

    public function testTailEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $tail = $instance->tail();
        TestCase::assertSame([], $tail->toNative());
    }

    public function testDeconstruct(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        [$head, $tail] = $instance->deconstruct();
        TestCase::assertSame(8, $head);
        TestCase::assertInstanceOf(Collection::class, $tail);
        TestCase::assertSame([1, 9, 3], $tail->toNative());
    }

    public function testDeconstructOption(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([8, 1, 9, 3]);
        [$head, $tail] = $instance->deconstructOption();

        TestCase::assertInstanceOf(Optional::class, $head);
        TestCase::assertSame(8, $head->getOrNull());

        TestCase::assertInstanceOf(Collection::class, $tail);
        TestCase::assertSame([1, 9, 3], $tail->toNative());
    }

    public function testDeconstructEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $this->getTestInstance()->expectException(UnsupportedTraversalException::class);
        $instance->deconstruct();
    }

    public function testDeconstructOptionEmpty(): void
    {
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        [$head, $tail] = $instance->deconstructOption();

        TestCase::assertInstanceOf(Optional::class, $head);
        TestCase::assertTrue($head->isEmpty());

        TestCase::assertInstanceOf(Collection::class, $tail);
        TestCase::assertSame([], $tail->toNative());
    }

    public function testToNative(): void
    {
        $elements = [8, 1, 9, 3];
        /** @var Collection<int> $instance */
        $instance = $this->getInstanceWithElements($elements);
        TestCase::assertSame($elements, $instance->toNative());
    }

    public function testToNativeEmpty(): void
    {
        $instance = $this->getInstanceWithElements([]);
        TestCase::assertSame([], $instance->toNative());
    }
}