<?php

namespace Squingla\Collections\Lists;

use Squingla\Collections\IterableOnce;
use Squingla\Collections\Collection;
use Squingla\Collections\Optional;

/**
 * Class representing an array-based list. Internally, all elements of that list are stored in native PHP `array` type.
 *
 * @template T
 * @template-extends AbstractIndexedCollection<T>
 */
class ArrayList extends AbstractIndexedCollection
{
    /** @var T[] */
    private array $content;

    /**
     * @param T[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->content = $elements;
    }

    /**
     * @param T $value
     * @return ArrayList<T>
     */
    public function appended($value): ArrayList
    {
        return new ArrayList([...$this->content, $value]);
    }

    /**
     * @param T $value
     * @return ArrayList<T>
     */
    public function prepended($value): ArrayList
    {
        return new ArrayList([$value, ...$this->content]);
    }

    /**
     * @param Collection<T> $collection
     * @return ArrayList<T>
     */
    public function concat(Collection $collection): ArrayList
    {
        return new ArrayList([...$this->content, ...$collection->toNative()]);
    }

    /**
     * @return Optional<T>
     */
    public function headOption(): Optional
    {
        if (empty($this->content)) {
            /** @var Optional<T> $empty */
            $empty = Optional::none();
            return $empty;
        }

        return Optional::some($this->content[0]);
    }

    /**
     * @return ArrayList<T>
     */
    public function tail(): ArrayList
    {
        if (empty($this->content)) {
            return $this;
        }

        return $this->drop(1);
    }

    /**
     * @return T[]
     */
    public function toNative(): array
    {
        return $this->content;
    }

    /**
     * @param int $index
     * @return Optional<T>
     */
    public function getOption($index): Optional
    {
        if ($index < 0 || $index >= count($this->content)) {
            /** @var Optional<T> $empty */
            $empty = Optional::none();
            return $empty;
        }

        return Optional::some($this->content[$index]);
    }

    /**
     * @param int $count
     * @return ArrayList<T>
     */
    public function drop(int $count): ArrayList
    {
        if ($count < 0) {
            return $this;
        }

        if ($count >= count($this->content)) {
            /** @var ArrayList<T> $empty */
            $empty = self::empty();
            return $empty;
        }

        return new ArrayList(array_slice($this->content, $count));
    }

    /**
     * @param int $count
     * @return ArrayList<T>
     */
    public function dropRight(int $count): ArrayList
    {
        if ($count < 0) {
            return $this;
        }

        if ($count >= count($this->content)) {
            /** @var ArrayList<T> $empty */
            $empty = self::empty();
            return $empty;
        }

        return new ArrayList(array_slice($this->content, 0, count($this->content) - $count));
    }

    /**
     * @param T $element
     * @param int $from
     * @return int
     */
    public function indexOf($element, int $from = 0): int
    {
        $count = count($this->content);
        for ($i = max(0, $from); $i < $count; $i++) {
            if ($this->content[$i] === $element) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * @param callable(T): bool $filter
     * @param int $from
     * @return int
     */
    public function indexWhere(callable $filter, int $from = 0): int
    {
        $count = count($this->content);
        for ($i = max(0, $from); $i < $count; $i++) {
            if ($filter($this->content[$i])) {
                return $i;
            }
        }
        return -1;
    }

    /**
     * @param int $from
     * @param int $to
     * @return ArrayList<T>
     */
    public function slice(int $from, int $to): ArrayList
    {
        $realFrom = max(0, $from);
        $realTo = min($to, count($this->content));
        $length = $realTo - $realFrom;

        return new ArrayList(array_slice($this->content, $realFrom, $length));
    }

    /**
     * @param callable(T,T): int $ordering
     * @return ArrayList<T>
     */
    public function sort(callable $ordering): ArrayList
    {
        $elements = $this->content;
        usort($elements, $ordering);
        return new ArrayList($elements);
    }

    /**
     * @return ArrayList<T>
     */
    public function reverse(): ArrayList
    {
        return new ArrayList(array_reverse($this->content));
    }

    /**
     * @param int $count
     * @return ArrayList<T>
     */
    public function take(int $count): ArrayList
    {
        if ($count < 0) {
            /** @var ArrayList<T> $empty */
            $empty = self::empty();
            return $empty;
        }
        if ($count >= count($this->content)) {
            return $this;
        }

        return new ArrayList(array_slice($this->content, 0, $count));
    }

    /**
     * @param int $count
     * @return ArrayList<T>
     */
    public function takeRight(int $count): ArrayList
    {
        if ($count < 0) {
            /** @var ArrayList<T> $empty */
            $empty = self::empty();
            return $empty;
        }
        if ($count >= count($this->content)) {
            return $this;
        }

        $offset = count($this->content) - $count;
        return new ArrayList(array_slice($this->content, $offset));
    }

    /**
     * @param callable(T): bool $filter
     * @return ArrayList<T>
     */
    public function takeWhile(callable $filter): ArrayList
    {
        /** @var T[] $elements */
        $elements = [];
        foreach ($this->content as $element) {
            if (!$filter($element)) {
                break;
            }
            $elements[] = $element;
        }

        return new ArrayList($elements);
    }

    /**
     * @return ArrayList<null>
     */
    public static function empty(): ArrayList
    {
        return new ArrayList([]);
    }

    /**
     * @template U
     * @param U[] $elements
     * @return ArrayList<U>
     */
    public static function with(array $elements): ArrayList
    {
        return new ArrayList($elements);
    }

    public function getLength(): int
    {
        return count($this->content);
    }

    /**
     * @param callable(T): bool $filter
     * @return ArrayList<T>
     */
    public function filter(callable $filter): ArrayList
    {
        $elements = [];
        foreach ($this->content as $element) {
            if ($filter($element)) {
                $elements[] = $element;
            }
        }

        return new ArrayList($elements);
    }

    /**
     * @param callable(T): bool $filter
     * @return ArrayList<T>
     */
    public function filterNot(callable $filter): ArrayList
    {
        $elements = [];
        foreach ($this->content as $element) {
            if (!$filter($element)) {
                $elements[] = $element;
            }
        }

        return new ArrayList($elements);
    }

    /**
     * @param callable(T): bool $filter
     * @return bool
     */
    public function exists(callable $filter): bool
    {
        foreach ($this->content as $element) {
            if ($filter($element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable(T): bool $filter
     * @return bool
     */
    public function forAll(callable $filter): bool
    {
        foreach ($this->content as $element) {
            if (!$filter($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @template U
     * @param callable(T): U $mapper
     * @return ArrayList<U>
     */
    public function map(callable $mapper): ArrayList
    {
        $mapped = array_map($mapper, $this->content);
        return new ArrayList($mapped);
    }

    /**
     * @template U
     * @param callable(T): IterableOnce<U> $mapper
     * @return ArrayList<U>
     */
    public function flatMap(callable $mapper): ArrayList
    {
        $mapped = [];
        foreach ($this->content as $element) {
            foreach ($mapper($element) as $internalElement) {
                $mapped[] = $internalElement;
            }
        }

        return new ArrayList($mapped);
    }

    /**
     * @template U
     * @param U $startValue
     * @param callable(U,T): U $operator
     * @return U
     */
    public function foldLeft($startValue, callable $operator)
    {
        $current = $startValue;
        foreach ($this->content as $element) {
            $current = $operator($current, $element);
        }
        return $current;
    }

    /**
     * @template U
     * @param U $startValue
     * @param callable(T,U): U $operator
     * @return U
     */
    public function foldRight($startValue, callable $operator)
    {
        $current = $startValue;
        for ($i = count($this->content) - 1; $i >= 0; $i--) {
            $element = $this->content[$i];
            $current = $operator($element, $current);
        }

        return $current;
    }

    /**
     * @param callable(T): void $consumer
     * @return void
     */
    public function forEach(callable $consumer): void
    {
        foreach ($this->content as $element) {
            $consumer($element);
        }
    }
}
