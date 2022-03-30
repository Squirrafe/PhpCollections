<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use Squingla\Collections\Optional;

class OptionalSomeTest extends TestCase
{
    public function testIteration(): void
    {
        $optional = Optional::some(15);
        $values = [];

        foreach ($optional as $value) {
            $values[] = $value;
        }

        self::assertEquals([15], $values);
    }

    public function testGetLength(): void
    {
        self::assertEquals(1, Optional::some(15)->getLength());
    }

    public function testIsEmpty(): void
    {
        self::assertFalse(Optional::some(15)->isEmpty());
    }

    public function testNonEmpty(): void
    {
        self::assertTrue(Optional::some(15)->nonEmpty());
    }

    public function testCount(): void
    {
        self::assertEquals(1, Optional::some(15)->count(fn (int $i) => $i < 30));
        self::assertEquals(0, Optional::some(15)->count(fn (int $i) => $i > 30));
    }

    public function testFilter(): void
    {
        $optional = Optional::some(15);

        self::assertTrue($optional->filter(fn (int $i) => $i < 30)->nonEmpty());
        self::assertTrue($optional->filter(fn (int $i) => $i > 30)->isEmpty());
    }

    public function testFilterNot(): void
    {
        $optional = Optional::some(15);

        self::assertFalse($optional->filterNot(fn (int $i) => $i < 30)->nonEmpty());
        self::assertFalse($optional->filterNot(fn (int $i) => $i > 30)->isEmpty());
    }

    public function testExists(): void
    {
        $optional = Optional::some(15);

        self::assertTrue($optional->exists(fn (int $i) => $i < 30));
        self::assertFalse($optional->exists(fn (int $i) => $i > 30));
    }

    public function testForAll(): void
    {
        $optional = Optional::some(15);

        self::assertTrue($optional->forAll(fn (int $i) => $i < 30));
        self::assertFalse($optional->forAll(fn (int $i) => $i > 30));
    }

    public function testMap(): void
    {
        $optional = Optional::some(15);
        $mapped = $optional->map(fn (int $i) => $i * 2);

        self::assertTrue($mapped->nonEmpty());
        self::assertTrue($mapped->exists(fn (int $i) => $i === 30));
    }

    public function testFlatMap(): void
    {
        $optional = Optional::some(15);
        $mappedEmpty = $optional->flatMap(fn (int $i) => Optional::none());
        $mappedNonEmpty = $optional->flatMap(fn (int $i) => Optional::some($i * 2));

        self::assertTrue($mappedEmpty->isEmpty());
        self::assertTrue($mappedNonEmpty->nonEmpty());
        self::assertTrue($mappedNonEmpty->exists(fn (int $i) => $i === 30));
    }

    public function testFlatMapWithoutOptionalResult(): void
    {
        $optional = Optional::some(15);
        $mappedNonEmpty = $optional->flatMap(fn (int $i) => $i * 2);

        self::assertTrue($mappedNonEmpty->nonEmpty());
        self::assertTrue($mappedNonEmpty->exists(fn (int $i) => $i === 30));
    }

    public function testFoldLeft(): void
    {
        $optional = Optional::some(15);
        $fold = $optional->foldLeft(10, fn (int $a, int $b) => $a - $b);
        self::assertEquals(-5, $fold);
    }

    public function testFoldRight(): void
    {
        $optional = Optional::some(15);
        $fold = $optional->foldRight(10, fn (int $a, int $b) => $a - $b);
        self::assertEquals(5, $fold);
    }

    public function testForEach(): void
    {
        $optional = Optional::some(15);
        $values = [];
        $optional->forEach(
            function (int $i) use (&$values) {
                $values[] = $i;
            }
        );
        self::assertEquals([15], $values);
    }

    public function testReduceLeft(): void
    {
        $optional = Optional::some(15);
        $reduced = $optional->reduceLeft(fn (int $a, int $b) => $a + $b);
        self::assertEquals(15, $reduced);
    }

    public function testReduceLeftOption(): void
    {
        $optional = Optional::some(15);
        $reduced = $optional->reduceLeftOption(fn (int $a, int $b) => $a + $b);
        self::assertTrue($reduced->nonEmpty());
        self::assertTrue($reduced->exists(fn (int $i) => $i === 15));
    }

    public function testReduceRight(): void
    {
        $optional = Optional::some(15);
        $reduced = $optional->reduceRight(fn (int $a, int $b) => $a + $b);
        self::assertEquals(15, $reduced);
    }

    public function testReduceRightOption(): void
    {
        $optional = Optional::some(15);
        $reduced = $optional->reduceRightOption(fn (int $a, int $b) => $a + $b);
        self::assertTrue($reduced->nonEmpty());
        self::assertTrue($reduced->exists(fn (int $i) => $i === 15));
    }

    public function testGet(): void
    {
        $optional = Optional::some(15);
        self::assertEquals(15, $optional->get());
    }

    public function testGetOrNull(): void
    {
        $optional = Optional::some(15);
        self::assertEquals(15, $optional->getOrNull());
    }

    public function testGetOrElse(): void
    {
        $optional = Optional::some(15);
        self::assertEquals(15, $optional->getOrElse(30));
    }

    public function testOrElse(): void
    {
        $optional = Optional::some(15);
        $orElse = $optional->orElse(30);
        self::assertTrue($orElse->nonEmpty());
        self::assertSame(15, $orElse->getOrNull());
    }

    public function testIfSet(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::some(13);
        $called = false;
        $passedValue = null;

        $optional->ifSet(
            function(int $value) use (&$called, &$passedValue) {
                $called = true;
                $passedValue = $value;
            }
        );

        self::assertTrue($called);
        self::assertSame(13, $passedValue);
    }

    public function testIfEmpty(): void
    {
        /** @var Optional<int> $optional */
        $optional = Optional::some(13);
        $called = false;

        $optional->ifEmpty(
            function() use (&$called) {
                $called = true;
            }
        );

        self::assertFalse($called);
    }
}
