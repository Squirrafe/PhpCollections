<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\NoSuchElementException;
use Squingla\Collections\Optional;
use Squingla\Collections\UnsupportedTraversalException;

class OptionalNoneTest extends TestCase
{
    public function testIteration(): void
    {
        $optional = Optional::none();
        $values = [];

        foreach ($optional as $value) {
            $values[] = $value;
        }

        self::assertEquals([], $values);
    }

    public function testGetLength(): void
    {
        self::assertEquals(0, Optional::none()->getLength());
    }

    public function testIsEmpty(): void
    {
        self::assertTrue(Optional::none()->isEmpty());
    }

    public function testNonEmpty(): void
    {
        self::assertFalse(Optional::none()->nonEmpty());
    }

    public function testCount(): void
    {
        self::assertEquals(0, Optional::none()->count(fn ($i) => true));
    }

    public function testFilter(): void
    {
        self::assertTrue(Optional::none()->filter(fn ($i) => true)->isEmpty());
    }

    public function testFilterNot(): void
    {
        self::assertTrue(Optional::none()->filterNot(fn ($i) => true)->isEmpty());
    }

    public function testExists(): void
    {
        self::assertFalse(Optional::none()->exists(fn ($i) => true));
    }

    public function testForAll(): void
    {
        self::assertTrue(Optional::none()->forAll(fn ($i) => false));
    }

    public function testMap(): void
    {
        self::assertTrue(Optional::none()->map(fn ($i) => $i)->isEmpty());
    }

    public function testFlatMap(): void
    {
        self::assertTrue(Optional::none()->map(fn ($i) => Optional::some($i))->isEmpty());
    }

    public function testFoldLeft(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::none();
        $folded = $optional->foldLeft(15, fn (int $i, int $j) => $i + $j);
        self::assertEquals(15, $folded);
    }

    public function testFoldRight(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::none();
        $folded = $optional->foldRight(15, fn (int $i, int $j) => $i + $j);
        self::assertEquals(15, $folded);
    }

    public function testForEach(): void
    {
        $optional = Optional::none();
        $values = [];
        $optional->forEach(
            function ($i) use (&$values) {
                $values[] = $i;
            }
        );
        self::assertEquals([], $values);
    }

    public function testReduceLeft(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::none();
        self::expectException(UnsupportedTraversalException::class);
        $optional->reduceLeft(fn (int $a, int $b) => $a + $b);
    }

    public function testReduceLeftOption(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::none();
        $reduced = $optional->reduceLeftOption(fn (int $a, int $b) => $a + $b);
        self::assertTrue($reduced->isEmpty());
    }

    public function testReduceRight(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::none();
        self::expectException(UnsupportedTraversalException::class);
        $optional->reduceRight(fn (int $a, int $b) => $a + $b);
    }

    public function testReduceRightOption(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::none();
        $reduced = $optional->reduceRightOption(fn (int $a, int $b) => $a + $b);
        self::assertTrue($reduced->isEmpty());
    }

    public function testGet(): void
    {
        self::expectException(NoSuchElementException::class);
        Optional::none()->get();
    }
}
