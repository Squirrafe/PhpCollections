<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\IterableOnce;
use Squingla\Collections\UnsupportedTraversalException;

trait IterableOnceTestTrait
{
    use TestTrait;

    /**
     * @template T
     * @param T[] $elements
     * @return IterableOnce<T>
     */
    protected abstract function getInstanceWithElements(array $elements): IterableOnce;

    public function testGetLength(): void
    {
        TestCase::assertSame(0, $this->getInstanceWithElements([])->getLength());
        TestCase::assertSame(1, $this->getInstanceWithElements(['foo'])->getLength());
        TestCase::assertSame(2, $this->getInstanceWithElements(['foo', 'bar'])->getLength());
    }

    public function testIsEmpty(): void
    {
        TestCase::assertTrue($this->getInstanceWithElements([])->isEmpty());
        TestCase::assertFalse($this->getInstanceWithElements(['foo', 'bar'])->isEmpty());
    }

    public function testNonEmpty(): void
    {
        TestCase::assertFalse($this->getInstanceWithElements([])->nonEmpty());
        TestCase::assertTrue($this->getInstanceWithElements(['foo', 'bar'])->nonEmpty());
    }

    public function testCount(): void
    {
        $instance = $this->getInstanceWithElements([5, 8, 1, 3]);
        $count = $instance->count(fn (int $i) => $i < 5);
        TestCase::assertSame(2, $count);
    }

    public function testFilter(): void
    {
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $filtered = $instance->filter(fn (int $i) => $i < 5);
        $elements = [];
        $filtered->forEach(
            function (int $i) use (&$elements) {
                $elements[] = $i;
            }
        );
        TestCase::assertSame([1, 3], $elements);
    }

    public function testFilterNot(): void
    {
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $filtered = $instance->filterNot(fn (int $i) => $i < 5);
        $elements = [];
        $filtered->forEach(
            function (int $i) use (&$elements) {
                $elements[] = $i;
            }
        );
        TestCase::assertSame([5, 8], $elements);
    }

    public function testExists(): void
    {
        TestCase::assertTrue($this->getInstanceWithElements([5, 1, 8, 3])->exists(fn (int $i) => $i > 5));
        TestCase::assertFalse($this->getInstanceWithElements([5, 1, 8, 3])->exists(fn (int $i) => $i > 10));
        TestCase::assertFalse($this->getInstanceWithElements([])->exists(fn (int $i) => $i > 5));
    }

    public function testForAll(): void
    {
        TestCase::assertTrue($this->getInstanceWithElements([5, 1, 8, 3])->forAll(fn (int $i) => $i < 10));
        TestCase::assertFalse($this->getInstanceWithElements([5, 1, 8, 3])->forAll(fn (int $i) => $i < 5));
        TestCase::assertTrue($this->getInstanceWithElements([])->forAll(fn (int $i) => $i < 10));
    }

    public function testMap(): void
    {
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $mapped = $instance->map(fn (int $i) => $i * 2);
        $elements = [];
        $mapped->forEach(
            function (int $i) use (&$elements) {
                $elements[] = $i;
            }
        );

        TestCase::assertSame([10, 2, 16, 6], $elements);
    }

    public function testFlatMap(): void
    {
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $mapped = $instance->flatMap(fn (int $i) => $this->getInstanceWithElements([$i, $i * 2]));
        $elements = [];
        $mapped->forEach(
            function (int $i) use (&$elements) {
                $elements[] = $i;
            }
        );

        TestCase::assertSame([5, 10, 1, 2, 8, 16, 3, 6], $elements);
    }

    public function testFoldLeft(): void
    {
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $folded = $instance->foldLeft(33, fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(16, $folded);
    }

    public function testFoldLeftEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $folded = $instance->foldLeft(33, fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(33, $folded);
    }

    public function testFoldRight(): void
    {
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $folded = $instance->foldRight(33, fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(42, $folded);
    }

    public function testFoldRightEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $folded = $instance->foldRight(33, fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(33, $folded);
    }

    public function testForEach(): void
    {
        $elements = [5, 1, 8, 3];
        $instance = $this->getInstanceWithElements($elements);
        $collected = [];
        $instance->forEach(
            function (int $e) use (&$collected) {
                $collected[] = $e;
            }
        );
        TestCase::assertSame($elements, $collected);
    }

    public function testForEachEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $collected = [];
        $instance->forEach(
            function (int $e) use (&$collected) {
                $collected[] = $e;
            }
        );
        TestCase::assertSame([], $collected);
    }

    public function testReduceLeft(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $reduced = $instance->reduceLeft(fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(((5 - 1) - 8) - 3, $reduced);
    }

    public function testReduceRight(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $reduced = $instance->reduceRight(fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(5 - (1 - (8 - 3)), $reduced);
    }

    public function testReduceLeftOption(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $reduced = $instance->reduceLeftOption(fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(((5 - 1) - 8) - 3, $reduced->getOrNull());
    }

    public function testReduceRightOption(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([5, 1, 8, 3]);
        $reduced = $instance->reduceRightOption(fn (int $a, int $b) => $a - $b);
        TestCase::assertSame(5 - (1 - (8 - 3)), $reduced->getOrNull());
    }

    public function testReduceLeftEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $this->getTestInstance()->expectException(UnsupportedTraversalException::class);
        $instance->reduceLeft(fn (int $a, int $b) => $a - $b);
    }

    public function testReduceRightEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $this->getTestInstance()->expectException(UnsupportedTraversalException::class);
        $instance->reduceRight(fn (int $a, int $b) => $a - $b);
    }

    public function testReduceLeftOptionEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $reduced = $instance->reduceLeftOption(fn (int $a, int $b) => $a - $b);
        TestCase::assertTrue($reduced->isEmpty());
    }

    public function testReduceRightOptionEmpty(): void
    {
        /** @var IterableOnce<int> $instance */
        $instance = $this->getInstanceWithElements([]);
        $reduced = $instance->reduceRightOption(fn (int $a, int $b) => $a - $b);
        TestCase::assertTrue($reduced->isEmpty());
    }
}