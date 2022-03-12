<?php

namespace Squingla\Collections;

use Traversable;

/**
 * Represents optional values. It can be treated as a collection that contains either zero or one element of given type,
 * never more.
 *
 * Constructor of that class is private; objects should be created either by calling `Optional::some($value)` to note
 * presence of element, or `Optional::none()` to note lack of element.
 *
 * @template T
 * @template-implements IterableOnce<T>
 */
class Optional implements IterableOnce
{
    private bool $isSet;
    /** @var T[] */
    private array $content;

    /**
     * @param bool $isSet
     * @param T[] $content
     */
    private function __construct(bool $isSet, $content)
    {
        $this->isSet = $isSet;
        $this->content = $content;
    }

    /**
     * Creates an instance of Optional that contains element.
     *
     * @template U
     * @param U $value
     * @return Optional<U>
     */
    public static function some($value): Optional
    {
        return new Optional(true, [$value]);
    }

    /**
     * Creates an instance of Optional that does not contain element.
     *
     * @return Optional<null>
     */
    public static function none(): Optional
    {
        return new Optional(false, []);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->content);
    }

    public function getLength(): int
    {
        return $this->isSet ? 1 : 0;
    }

    public function isEmpty(): bool
    {
        return !$this->isSet;
    }

    public function nonEmpty(): bool
    {
        return $this->isSet;
    }

    public function count(callable $filter): int
    {
        if (!$this->isSet) {
            return 0;
        }

        if ($filter($this->content[0])) {
            return 1;
        }

        return 0;
    }

    /**
     * If optional is non-empty and content of optional satisfies filter, returns this optional, otherwise returns empty
     * optional.
     *
     * @param callable(T): bool $filter
     * @return Optional<T>
     */
    public function filter(callable $filter): Optional
    {
        if (!$this->isSet) {
            return $this;
        }

        if ($filter($this->content[0])) {
            return $this;
        }

        /** @var Optional<T> $result */
        $result = new Optional(false, []);
        return $result;
    }


    /**
     * If optional is non-empty and content of optional does not satisfy filter, returns this optional, otherwise
     * returns empty optional.
     *
     * @param callable(T): bool $filter
     * @return Optional<T>
     */
    public function filterNot(callable $filter): Optional
    {
        if (!$this->isSet) {
            return $this;
        }

        if ($filter($this->content[0])) {
            /** @var Optional<T> $result */
            $result = new Optional(false, []);
            return $result;
        }

        return $this;
    }

    /**
     * If optional is non-empty and content of optional satisfies filter, returns "true", otherwise returns "false".
     *
     * @param callable(T): bool $filter
     * @return bool
     */
    public function exists(callable $filter): bool
    {
        if (!$this->isSet) {
            return false;
        }

        return $filter($this->content[0]);
    }

    /**
     * If optional is empty or if content of optional satisifes filter, returns "true", otherwise returns "false".
     *
     * @param callable(T): bool $filter
     * @return bool
     */
    public function forAll(callable $filter): bool
    {
        if (!$this->isSet) {
            return true;
        }

        return $filter($this->content[0]);
    }

    /**
     * If optional is empty, returns empty optional. Otherwise returns optional containing result of applying mapper
     * to content of this optional.
     *
     * @template U
     * @param callable(T): U $mapper
     * @return Optional<U>
     */
    public function map(callable $mapper): Optional
    {
        if (!$this->isSet) {
            /** @var Optional<U> $result */
            $result = new Optional(false, []);
            return $result;
        }

        return Optional::some($mapper($this->content[0]));
    }

    /**
     * If optional is empty, returns empty optional. Otherwise returns optional containing result of applying mapper
     * to content of this optional. If result of such mapping is another optional, then the result is flattened, that is:
     * if mapper returns optional, then result of map() would be an optional countaining that optional, while the
     * result of flatMap() would be a single optional.
     *
     * @template U
     * @param callable(T): (U|Optional<U>) $mapper
     * @return Optional<U>
     */
    public function flatMap(callable $mapper): Optional
    {
        if (!$this->isSet) {
            /** @var Optional<U> $result */
            $result = new Optional(false, []);
            return $result;
        }

        $mappedResult = $mapper($this->content[0]);
        if ($mappedResult instanceof Optional) {
            return $mappedResult;
        }

        return Optional::some($mappedResult);
    }

    public function foldLeft($startValue, callable $operator)
    {
        if ($this->isSet) {
            return $operator($startValue, $this->content[0]);
        }

        return $startValue;
    }

    public function foldRight($startValue, callable $operator)
    {
        if ($this->isSet) {
            return $operator($this->content[0], $startValue);
        }

        return $startValue;
    }

    public function forEach(callable $consumer): void
    {
        if ($this->isSet) {
            $consumer($this->content[0]);
        }
    }

    public function reduceLeft(callable $operator)
    {
        if ($this->isSet) {
            return $this->content[0];
        }

        throw new UnsupportedTraversalException("Cannot reduce an empty Optional");
    }

    public function reduceLeftOption(callable $operator): Optional
    {
        return $this;
    }

    public function reduceRight(callable $operator)
    {
        if ($this->isSet) {
            return $this->content[0];
        }

        throw new UnsupportedTraversalException("Cannot reduce an empty Optional");
    }

    public function reduceRightOption(callable $operator): Optional
    {
        return $this;
    }
}
