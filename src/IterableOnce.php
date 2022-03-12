<?php

namespace Squingla\Collections;

use IteratorAggregate;

/**
 * Basic interface for all collections in library. Allows using collections in foreach loops. All collections
 * implementing that interface must be immutable.
 *
 * @template T
 * @template-extends IteratorAggregate<int,T>
 */
interface IterableOnce extends IteratorAggregate
{
    /**
     * Returns number of elements in collection.
     *
     * @return int
     */
    public function getLength(): int;

    /**
     * Returns "true" if collection does not have any elements, "false" otherwise.
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Returns "true" if collection contains at least one element, "false" otherwise.
     *
     * @return bool
     */
    public function nonEmpty(): bool;

    /**
     * Returns number of elements in collection that satisfy given filter.
     *
     * ```
     * $collection->count($filter);
     * ```
     *
     * is a shortcut for
     *
     * ```
     * $collection->filter($filter)->getLength();
     * ```
     *
     *
     * @param callable(T): boolean $filter
     * @return int
     */
    public function count(callable $filter): int;

    /**
     * Creates a new collection containing only elements from this collection that satisfy given filter.
     *
     * @param callable(T): boolean $filter
     * @return IterableOnce<T>
     */
    public function filter(callable $filter): IterableOnce;

    /**
     * Creates a new collection containing only elements from this collection that do not satisfy given filter.
     *
     * @param callable(T): boolean $filter
     * @return IterableOnce<T>
     */
    public function filterNot(callable $filter): IterableOnce;

    /**
     * Returns "true" if there is at least one element in collection that satisfy given filter, "false" otherwise.
     *
     * @param callable(T): boolean $filter
     * @return bool
     */
    public function exists(callable $filter): bool;

    /**
     * Returns "true" if all elements of collection satisfy given filter, "false" otherwise. Note that empty collection
     * will return "true" here.
     *
     * @param callable(T): boolean $filter
     * @return bool
     */
    public function forAll(callable $filter): bool;

    /**
     * Builds a new collection by applying a $mapper to all elements of current function.
     *
     * @template U
     * @param callable(T): U $mapper
     * @return IterableOnce<U>
     */
    public function map(callable $mapper): IterableOnce;

    /**
     * Builds a new collection by applying a $mapper to all elements of current function and using elements of resulting
     * collections.
     *
     * @template U
     * @param callable(T): IterableOnce<U> $mapper
     * @return IterableOnce<U>
     */
    public function flatMap(callable $mapper): IterableOnce;

    /**
     * Applies a binary operator to a start value and all elements of this iterator, going left to right.
     *
     * @template U
     * @param U $startValue
     * @param callable(U,T): U $operator
     * @return U
     */
    public function foldLeft($startValue, callable $operator);

    /**
     * Applies a binary operator to a start value and all elements of this iterator, going right to left.
     *
     * @template U
     * @param U $startValue
     * @param callable(T,U): U $operator
     * @return U
     */
    public function foldRight($startValue, callable $operator);

    /**
     * Applies a function to every element in collection.
     *
     * @param callable(T): void $consumer
     * @return void
     */
    public function forEach(callable $consumer): void;

    /**
     * Applies a binary operator to all elements of this collection, going left to right. If collection is empty, throws
     * exception.
     *
     * @param callable(T,T): T $operator
     * @return T
     * @throws UnsupportedTraversalException if collection is empty.
     */
    public function reduceLeft(callable $operator);

    /**
     * If collection is non-empty, applies a binary operator to all elements of this collection, going left to right,
     * and returns an Optional with result. If collection is non empty, returns empty Optional.
     *
     * @param callable(T,T): T $operator
     * @return Optional<T>
     */
    public function reduceLeftOption(callable $operator): Optional;

    /**
     * Applies a binary operator to all elements of this collection, going right to left. If collection is empty, throws
     * exception.
     *
     * @param callable(T,T): T $operator
     * @return T
     * @throws UnsupportedTraversalException if collection is empty.
     */
    public function reduceRight(callable $operator);

    /**
     * If collection is non-empty, applies a binary operator to all elements of this collection, going right to left,
     * and returns an Optional with result. If collection is non empty, returns empty Optional.
     *
     * @param callable(T,T): T $operator
     * @return Optional<T>
     */
    public function reduceRightOption(callable $operator): Optional;
}