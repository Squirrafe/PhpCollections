<?php

namespace Squingla\Collections;

use ArrayIterator;
use Traversable;

/**
 * Abstract collection containing basic implementations of most methods of interface.
 *
 * @template T
 * @template-implements Collection<T>
 */
abstract class AbstractCollection implements Collection
{
    public function head(): mixed
    {
        $headOption = $this->headOption();
        if ($headOption->isEmpty()) {
            throw new NoSuchElementException("Cannot call head() on empty collection");
        }

        return $headOption->get();
    }

    public function deconstruct(): array
    {
        $headOption = $this->headOption();
        if ($headOption->isEmpty()) {
            throw new UnsupportedTraversalException("Cannot call deconstruct() on empty collection");
        }

        return [$headOption->get(), $this->tail()];
    }

    public function deconstructOption(): array
    {
        return [$this->headOption(), $this->tail()];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toNative());
    }

    public function isEmpty(): bool
    {
        return $this->getLength() === 0;
    }

    public function nonEmpty(): bool
    {
        return $this->getLength() > 0;
    }

    public function count(callable $filter): int
    {
        return $this->filter($filter)->getLength();
    }

    public function reduceLeft(callable $operator): mixed
    {
        $optional = $this->reduceLeftOption($operator);
        if ($optional->isEmpty()) {
            throw new UnsupportedTraversalException("Cannot call reduceLeft() on empty collection");
        }

        return $optional->get();
    }

    public function reduceRight(callable $operator): mixed
    {
        $optional = $this->reduceRightOption($operator);
        if ($optional->isEmpty()) {
            throw new UnsupportedTraversalException("Cannot call reduceRight() on empty collection");
        }

        return $optional->get();
    }

    /**
     * @param callable(T,T): T $operator
     * @return Optional<T>
     */
    public function reduceLeftOption(callable $operator): Optional
    {
        /**
         * @param Optional<T> $left
         * @param T $value
         * @return Optional<T>
         */
        $functor = function (Optional $left, mixed $value) use ($operator) {
            return $left->isEmpty()
                ? Optional::some($value)
                : $left->map(fn ($v) => $operator($v, $value));
        };
        /** @var Optional<T> $startValue */
        $startValue = Optional::none();

        return $this->foldLeft($startValue, $functor);
    }

    /**
     * @param callable(T,T): T $operator
     * @return Optional<T>
     */
    public function reduceRightOption(callable $operator): Optional
    {
        /**
         * @param T $value
         * @param Optional<T> $right
         * @return Optional<T>
         */
        $functor = function (mixed $value, Optional $right) use ($operator) {
            return $right->isEmpty()
                ? Optional::some($value)
                : $right->map(fn ($v) => $operator($value, $v));
        };
        /** @var Optional<T> $startValue */
        $startValue = Optional::none();

        return $this->foldRight($startValue, $functor);
    }
}
