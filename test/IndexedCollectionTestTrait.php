<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\TestListenerAdapter;
use Squingla\Collections\IndexedCollection;
use Squingla\Collections\NoSuchElementException;

trait IndexedCollectionTestTrait
{
    use CollectionTestTrait;

    /**
     * @template T
     * @param T[] $elements
     * @return IndexedCollection<T>
     */
    protected abstract function getInstanceWithElements(array $elements): IndexedCollection;

    /**
     * @dataProvider testGetDataProvider
     * @param int[] $input
     */
    public function testGet(
        array $input,
        int $index,
        int $value,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        TestCase::assertSame($value, $instance->get($index));
    }

    /**
     * @dataProvider testGetDataProvider
     * @param int[] $input
     */
    public function testGetOption(
        array $input,
        int $index,
        int $value,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        TestCase::assertTrue($instance->getOption($index)->nonEmpty());
        TestCase::assertSame($value, $instance->getOption($index)->getOrNull());
    }

    /**
     * @dataProvider testGetDataProvider
     * @param int[] $input
     */
    public function testArrayAccess(
        array $input,
        int $index,
        int $value,
    ): void {
        $instance = $this->getInstanceWithElements($input);
        TestCase::assertTrue(isset($instance[$index]));
        TestCase::assertSame($value, $instance[$index]);
    }

    private function testGetDataProvider(): iterable
    {
        yield [[3, 1, 8, 5], 0, 3];
        yield [[3, 1, 8, 5], 1, 1];
        yield [[3, 1, 8, 5], 2, 8];
        yield [[3, 1, 8, 5], 3, 5];
    }

    public function testGetInvalidIndex(): void
    {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements([3, 1, 8, 5]);
        $this->expectException(NoSuchElementException::class);
        $instance->get(-1);
    }

    public function testGetIndexOutOfBounds(): void
    {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements([3, 1, 8, 5]);
        $this->expectException(NoSuchElementException::class);
        $instance->get(15);
    }

    public function testGetOptionInvalidIndex(): void
    {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements([3, 1, 8, 5]);
        TestCase::assertTrue($instance->getOption(-1)->isEmpty());
    }

    public function testGetOptionIndexOutOfBounds(): void
    {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements([3, 1, 8, 5]);
        TestCase::assertTrue($instance->getOption(15)->isEmpty());
    }

    /**
     * @dataProvider testDropDataProvider
     * @param int[] $input
     * @param int $drop
     * @param int[] $expected
     */
    public function testDrop(
        array $input,
        int $drop,
        array $expected
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $dropped = $instance->drop($drop);
        TestCase::assertSame($expected, $dropped->toNative());
    }

    private function testDropDataProvider(): iterable
    {
        yield [[3, 1, 8, 6], -2, [3, 1, 8, 6]];
        yield [[3, 1, 8, 6], -1, [3, 1, 8, 6]];
        yield [[3, 1, 8, 6], 0, [3, 1, 8, 6]];
        yield [[3, 1, 8, 6], 1, [1, 8, 6]];
        yield [[3, 1, 8, 6], 2, [8, 6]];
        yield [[3, 1, 8, 6], 3, [6]];
        yield [[3, 1, 8, 6], 4, []];
        yield [[3, 1, 8, 6], 5, []];
    }

    /**
     * @dataProvider testDropRightDataProvider
     * @param int[] $input
     * @param int[] $expected
     */
    public function testDropRight(
        array $input,
        int $drop,
        array $expected
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $dropped = $instance->dropRight($drop);
        TestCase::assertSame($expected, $dropped->toNative());
    }

    private function testDropRightDataProvider(): iterable
    {
        yield [[3, 1, 8, 6], -2, [3, 1, 8, 6]];
        yield [[3, 1, 8, 6], -1, [3, 1, 8, 6]];
        yield [[3, 1, 8, 6], 0, [3, 1, 8, 6]];
        yield [[3, 1, 8, 6], 1, [3, 1, 8]];
        yield [[3, 1, 8, 6], 2, [3, 1]];
        yield [[3, 1, 8, 6], 3, [3]];
        yield [[3, 1, 8, 6], 4, []];
        yield [[3, 1, 8, 6], 5, []];
    }

    /**
     * @dataProvider testReverseDataProvider
     * @param int[] $input
     * @param int[] $expected
     */
    public function testReverse(
        array $input,
        array $expected
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        TestCase::assertSame($expected, $instance->reverse()->toNative());
    }

    private function testReverseDataProvider(): iterable
    {
        yield [[3, 1, 8, 6], [6, 8, 1, 3]];
        yield [[67, 42], [42, 67]];
        yield [[5], [5]];
        yield [[], []];
    }

    /**
     * @dataProvider testIndexOfDataProvider
     * @param int[] $input
     */
    public function testIndexOf(
        array $input,
        int $value,
        int $from,
        int $expected
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $index = $instance->indexOf($value, $from);
        TestCase::assertSame($expected, $index);
    }

    private function testIndexOfDataProvider(): iterable
    {
        yield [[3, 1, 8, 6, 5, 1, 7], 3, 0, 0];
        yield [[3, 1, 8, 6, 5, 1, 7], 1, 0, 1];
        yield [[3, 1, 8, 6, 5, 1, 7], 8, 0, 2];
        yield [[3, 1, 8, 6, 5, 1, 7], 6, 0, 3];
        yield [[3, 1, 8, 6, 5, 1, 7], 5, 0, 4];
        yield [[3, 1, 8, 6, 5, 1, 7], 7, 0, 6];

        yield [[3, 1, 8, 6, 5, 1, 7], 1, 1, 1];
        yield [[3, 1, 8, 6, 5, 1, 7], 1, 2, 5];
        yield [[3, 1, 8, 6, 5, 1, 7], 9, 0, -1];
        yield [[3, 1, 8, 6, 5, 1, 7], 3, 1, -1];
    }

    /**
     * @dataProvider testIndexWhereDataProvider
     * @param int[] $input
     */
    public function testIndexWhere(
        array $input,
        int $from,
        int $expected
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $index = $instance->indexWhere(fn (int $i) => $i > 5, $from);
        TestCase::assertSame($expected, $index);
    }

    private function testIndexWhereDataProvider(): iterable
    {
        yield [[3, 1, 2], 0, -1];
        yield [[3, 1, 8, 1, 2, 7], 0, 2];
        yield [[3, 1, 8, 1, 2, 7], 2, 2];
        yield [[3, 1, 8, 1, 2, 7], 3, 5];
    }

    /**
     * @dataProvider testSliceDataProvider
     * @param int[] $input
     * @param int[] $expected
     */
    public function testSlice(
        array $input,
        int $from,
        int $to,
        array $expected,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $slice = $instance->slice($from, $to);
        TestCase::assertSame($expected, $slice->toNative());
    }

    private function testSliceDataProvider(): iterable
    {
        yield [[3, 1, 2, 8], -1, 5, [3, 1, 2, 8]];
        yield [[3, 1, 2, 8], -1, 4, [3, 1, 2, 8]];
        yield [[3, 1, 2, 8], 0, 4, [3, 1, 2, 8]];
        yield [[3, 1, 2, 8], 1, 4, [1, 2, 8]];
        yield [[3, 1, 2, 8], 2, 4, [2, 8]];
        yield [[3, 1, 2, 8], 0, 3, [3, 1, 2]];
        yield [[3, 1, 2, 8], 0, 2, [3, 1]];
        yield [[3, 1, 2, 8], 1, 3, [1, 2]];
    }

    /**
     * @dataProvider testSortDataProvider
     * @param int[] $input
     * @param int[] $expected
     */
    public function testSort(
        array $input,
        array $expected,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $sorted = $instance->sort(fn (int $a, int $b) => $a - $b);
        TestCase::assertSame($expected, $sorted->toNative());
    }

    private function testSortDataProvider(): iterable
    {
        yield [[3, 1, 2, 8], [1, 2, 3, 8]];
        yield [[5, 7, 5], [5, 5, 7]];
        yield [[3], [3]];
        yield [[], []];
    }

    /**
     * @dataProvider testSplitAtDataProvider
     * @param int[] $input
     * @param int[] $leftExpected
     * @param int[] $rightExpected
     */
    public function testSplitAt(
        array $input,
        int $index,
        array $leftExpected,
        array $rightExpected,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        [$left, $right] = $instance->splitAt($index);
        TestCase::assertInstanceOf(IndexedCollection::class, $left);
        TestCase::assertInstanceOf(IndexedCollection::class, $right);
        TestCase::assertSame($leftExpected, $left->toNative());
        TestCase::assertSame($rightExpected, $right->toNative());
    }

    /**
     * @dataProvider testSplitAtDataProvider
     * @param int[] $input
     * @param int[] $leftExpected
     */
    public function testTake(
        array $input,
        int $count,
        array $leftExpected,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $taken = $instance->take($count);
        TestCase::assertSame($leftExpected, $taken->toNative());
    }

    private function testSplitAtDataProvider(): iterable
    {
        yield [[3, 1, 2, 8, 5, 9], -1, [], [3, 1, 2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 0, [], [3, 1, 2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 1, [3], [1, 2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 2, [3, 1], [2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 3, [3, 1, 2], [8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 4, [3, 1, 2, 8], [5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 5, [3, 1, 2, 8, 5], [9]];
        yield [[3, 1, 2, 8, 5, 9], 6, [3, 1, 2, 8, 5, 9], []];
        yield [[3, 1, 2, 8, 5, 9], 7, [3, 1, 2, 8, 5, 9], []];
    }

    /**
     * @dataProvider testTakeRightDataProvider
     * @param int[] $input
     * @param int[] $expected
     */
    public function testTakeRight(
        array $input,
        int $count,
        array $expected,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $taken = $instance->takeRight($count);
        TestCase::assertSame($expected, $taken->toNative());
    }

    private function testTakeRightDataProvider(): iterable
    {
        yield [[3, 1, 2, 8, 5, 9], -1, []];
        yield [[3, 1, 2, 8, 5, 9], 0, []];
        yield [[3, 1, 2, 8, 5, 9], 1, [9]];
        yield [[3, 1, 2, 8, 5, 9], 2, [5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 3, [8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 4, [2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 5, [1, 2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 6, [3, 1, 2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 9], 7, [3, 1, 2, 8, 5, 9]];
    }

    /**
     * @dataProvider testTakeWhileDataProvider
     * @param int[] $input
     * @param int[] $expected
     */
    public function testTakeWhile(
        array $input,
        array $expected,
    ): void {
        /** @var IndexedCollection<int> $instance */
        $instance = $this->getInstanceWithElements($input);
        $taken = $instance->takeWhile(fn (int $i) => $i < 10);
        TestCase::assertSame($expected, $taken->toNative());
    }

    private function testTakeWhileDataProvider(): iterable
    {
        yield [[3, 1, 2, 8, 5, 9], [3, 1, 2, 8, 5, 9]];
        yield [[3, 1, 2, 8, 5, 15], [3, 1, 2, 8, 5]];
        yield [[3, 1, 2, 15, 8, 5, 9], [3, 1, 2]];
    }
}