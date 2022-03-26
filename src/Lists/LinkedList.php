<?php

namespace Squingla\Collections\Lists;

use Squingla\Collections\Collection;
use Squingla\Collections\IterableOnce;
use Squingla\Collections\Optional;

/**
 * Class representing a singly linked list. Each object of linked list is either:
 * - a terminator - an empty element, representing end of list, or
 * - an element with single value and a tail - another instance of LinkedList, representing next element.
 *
 * Because of such representation, operations on linked list that concern beginning of lists are very fast, but complexity
 * of other operations grow with list size.
 *
 * Because of way linked list work, it can be very optimally used as a stack, with `prepended()` to add new element
 * to stack, `head()/headOption()` to get current element of stack and `tail()` to remove current element from stack.
 *
 * Constructor of that class is private; objects can be created with static methods:
 * - LinkedList::empty() returns empty list
 * - LinkedList::with(array $elements) returns list containing all elements from argument, keeping order.
 *
 * @template T
 * @template-extends AbstractIndexedCollection<T>
 */
class LinkedList extends AbstractIndexedCollection
{
    /** @var T[] */
    private array $element;
    /** @var LinkedList<T>|null */
    private ?LinkedList $tail;

    /**
     * @param T[] $element
     * @param LinkedList<T>|null $tail
     */
    private function __construct(array $element, ?LinkedList $tail)
    {
        $this->element = $element;
        $this->tail = $tail;
    }


    /**
     * Creates a list containing all elements from parameter. Has complexity of O(n).
     *
     * @template U
     * @param U[] $elements
     * @return LinkedList<U>
     */
    public static function with(array $elements): LinkedList
    {
        /** @var LinkedList<U> $tail */
        $tail = self::empty();
        if (count($elements) === 0) {
            return $tail;
        }

        for ($i = count($elements) - 1; $i >= 0; $i--) {
            $tail = new LinkedList([$elements[$i]], $tail);
        }

        return $tail;
    }

    /**
     * Returns empty list.
     *
     * @return LinkedList<null>
     */
    public static function empty(): LinkedList
    {
        return new self([], null);
    }

    /**
     * Adds an element to end of current list. Method has a complexity of O(n).
     *
     * @param T $value
     * @return LinkedList<T>
     */
    public function appended($value): LinkedList
    {
        if ($this->tail === null) {
            // current element is a terminator - should be replaced with a new element
            return new LinkedList([$value], $this);
        }

        return new LinkedList(
            $this->element,
            $this->tail->appended($value),
        );
    }

    /**
     * Adds an element to beginning of current list. Method has a complexity of O(1).
     *
     * @param T $value
     * @return LinkedList<T>
     */
    public function prepended($value): LinkedList
    {
        return new LinkedList([$value], $this);
    }

    /**
     * @param Collection<T> $collection
     * @return LinkedList<T>
     */
    public function concat(Collection $collection): LinkedList
    {
        return self::with([...$this->toNative(), ...$collection->toNative()]);
    }

    /**
     * @return Optional<T>
     */
    public function headOption(): Optional
    {
        if ($this->element === []) {
            /** @var Optional<T> $none */
            $none = Optional::none();
            return $none;
        }

        return Optional::some($this->element[0]);
    }

    /**
     * @return LinkedList<T>
     */
    public function tail(): LinkedList
    {
        if (null === $this->tail) {
            return $this;
        }

        return $this->tail;
    }

    /**
     * @return T[]
     */
    public function toNative(): array
    {
        if ($this->tail === null) {
            return [];
        }

        return [
            $this->element[0],
            ...$this->tail->toNative(),
        ];
    }

    /**
     * @param int $index
     * @return Optional<T>
     */
    public function getOption($index): Optional
    {
        if ($index < 0) {
            /** @var Optional<T> $empty */
            $empty = Optional::none();
            return $empty;
        }

        if ($this->tail === null) {
            /** @var Optional<T> $empty */
            $empty = Optional::none();
            return $empty;
        }

        if ($index === 0) {
            return Optional::some($this->element[0]);
        }

        return $this->tail->getOption($index - 1);
    }

    /**
     * @param int $count
     * @return LinkedList<T>
     */
    public function drop(int $count): LinkedList
    {
        if ($count <= 0 || $this->tail === null) {
            return $this;
        }

        return $this->tail->drop($count - 1);
    }

    /**
     * @param int $count
     * @return LinkedList<T>
     */
    public function dropRight(int $count): LinkedList
    {
        return $this->reverse()->drop($count)->reverse();
    }

    /**
     * @param T $element
     * @param int $from
     * @return int
     */
    public function indexOf($element, int $from = 0): int
    {
        if ($this->tail === null) {
            return -1;
        }

        if ($from <= 0 && $element === $this->element[0]) {
            return 0;
        }

        $indexFromTail = $this->tail->indexOf($element, $from - 1);
        if ($indexFromTail === -1) {
            return -1;
        }
        return $indexFromTail + 1;
    }

    /**
     * @param callable(T): bool $filter
     * @param int $from
     * @return int
     */
    public function indexWhere(callable $filter, int $from = 0): int
    {
        if ($this->tail === null) {
            return -1;
        }

        if ($from <= 0 && $filter($this->element[0])) {
            return 0;
        }

        $indexFromTail = $this->tail->indexWhere($filter, $from - 1);
        if ($indexFromTail === -1) {
            return -1;
        }

        return $indexFromTail + 1;
    }

    /**
     * @param int $from
     * @param int $to
     * @return LinkedList<T>
     */
    public function slice(int $from, int $to): LinkedList
    {
        if ($to - $from <= 0 || $this->tail === null) {
            /** @var LinkedList<T> $empty */
            $empty = self::empty();
            return $empty;
        }

        if ($from > 0) { // skipping left side
            return $this->tail->slice($from - 1, $to - 1);
        }

        return new LinkedList(
            $this->element,
            $this->tail->slice(0, $to - 1),
        );
    }

    /**
     * @param callable(T,T): int $ordering
     * @return LinkedList<T>
     */
    public function sort(callable $ordering): LinkedList
    {
        $elements = $this->toNative();
        usort($elements, $ordering);
        return self::with($elements);
    }

    /**
     * @return LinkedList<T>
     */
    public function reverse(): LinkedList
    {
        /** @var LinkedList<T> $tail */
        $tail = self::empty();
        $this->forEach(
            function ($element) use (&$tail) {
                $tail = new LinkedList([$element], $tail);
            }
        );

        return $tail;
    }

    /**
     * @param int $count
     * @return LinkedList<T>
     */
    public function take(int $count): LinkedList
    {
        if ($count <= 0 || $this->tail === null) {
            /** @var LinkedList<T> $empty */
            $empty = self::empty();
            return $empty;
        }

        return new LinkedList($this->element, $this->tail->take($count - 1));
    }

    /**
     * @param int $count
     * @return LinkedList<T>
     */
    public function takeRight(int $count): LinkedList
    {
        return $this->reverse()->take($count)->reverse();
    }

    /**
     * @param callable(T): bool $filter
     * @return LinkedList<T>
     */
    public function takeWhile(callable $filter): LinkedList
    {
        if ($this->tail === null) {
            return $this;
        }

        if (!$filter($this->element[0])) {
            /** @var LinkedList<T> $empty */
            $empty = self::empty();
            return $empty;
        }

        return new LinkedList($this->element, $this->tail->takeWhile($filter));
    }

    public function getLength(): int
    {
        if ($this->tail === null) {
            return 0;
        }

        return $this->tail->getLength() + 1;
    }

    /**
     * @param callable(T): bool $filter
     * @return LinkedList<T>
     */
    public function filter(callable $filter): LinkedList
    {
        if ($this->tail === null) {
            return $this;
        }

        $tailFiltered = $this->tail->filter($filter);

        if ($filter($this->element[0])) {
            return new LinkedList($this->element, $tailFiltered);
        }

        return $tailFiltered;
    }

    /**
     * @param callable(T): bool $filter
     * @return LinkedList<T>
     */
    public function filterNot(callable $filter): LinkedList
    {
        if ($this->tail === null) {
            return $this;
        }

        $tailFiltered = $this->tail->filterNot($filter);

        if (!$filter($this->element[0])) {
            return new LinkedList($this->element, $tailFiltered);
        }

        return $tailFiltered;
    }

    public function exists(callable $filter): bool
    {
        if ($this->tail === null) {
            return false;
        }

        if ($filter($this->element[0])) {
            return true;
        }

        return $this->tail->exists($filter);
    }

    public function forAll(callable $filter): bool
    {
        if ($this->tail === null) {
            return true;
        }

        if (!$filter($this->element[0])) {
            return false;
        }

        return $this->tail->forAll($filter);
    }

    /**
     * @template U
     * @param callable(T): U $mapper
     * @return LinkedList<U>
     */
    public function map(callable $mapper): LinkedList
    {
        if ($this->tail === null) {
            return $this;
        }

        $tailMapped = $this->tail->map($mapper);
        return new LinkedList([$mapper($this->element[0])], $tailMapped);
    }

    /**
     * @template U
     * @param callable(T): IterableOnce<U> $mapper
     * @return LinkedList<U>
     */
    public function flatMap(callable $mapper): LinkedList
    {
        if ($this->tail === null) {
            return $this;
        }

        /** @var LinkedList<U> $tailMapped */
        $tailMapped = $this->tail->flatMap($mapper);

        /** @var IterableOnce<U> $elementMapped */
        $elementMapped = $mapper($this->element[0]);
        $elementParts = [];
        foreach ($elementMapped as $elementPart) {
            $elementParts[] = $elementPart;
        }

        for ($i = count($elementParts) - 1; $i >= 0; $i--) {
            $part = $elementParts[$i];
            /** @var LinkedList<U> $tailMapped */
            $tailMapped = new LinkedList([$part], $tailMapped);
        }

        return $tailMapped;
    }

    public function foldLeft($startValue, callable $operator)
    {
        if ($this->tail === null) {
            return $startValue;
        }

        return $this->tail->foldLeft($operator($startValue, $this->element[0]), $operator);
    }

    public function foldRight($startValue, callable $operator)
    {
        if ($this->tail === null) {
            return $startValue;
        }

        return $operator($this->element[0], $this->tail->foldRight($startValue, $operator));
    }

    public function forEach(callable $consumer): void
    {
        if ($this->tail === null) {
            return;
        }

        $consumer($this->element[0]);
        $this->tail->forEach($consumer);
    }

    public function isEmpty(): bool
    {
        return $this->tail === null;
    }

    public function nonEmpty(): bool
    {
        return $this->tail !== null;
    }
}
