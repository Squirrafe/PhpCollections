<?php

namespace Squingla\Collections\Lists;

use Squingla\Collections\Collection;
use Squingla\Collections\CollectionWithKey;

/**
 * Interface for all collections that use integers as an index. In such collections, first element has index = 0, second
 * has index = 1, etc.
 *
 * @template T
 * @template-extends Collection<T>
 * @template-extends CollectionWithKey<int,T>
 */
interface IndexedCollection extends Collection, CollectionWithKey
{
    /**
     * Returns collection containing all elements except first $count elements from left. If $count is negative or equal
     * to zero, returns this collection. If $count is equal or larger than length of collection, returns empty collection.
     *
     * @param int $count
     * @return IndexedCollection<T>
     */
    public function drop(int $count): IndexedCollection;

    /**
     * Returns collection containing all elements excepts first $count elements from right. If $count is negative or
     * equal to zero, returns this collection. If $count is equal or larger than length of collection, returns empty
     * collection.
     *
     * @param int $count
     * @return IndexedCollection<T>
     */
    public function dropRight(int $count): IndexedCollection;

    /**
     * Searches for given element in collection and returns index of first appearance of that element. If $from argument
     * is greater than zero, it will start looking from given index.
     *
     * If element is not a member of collection, returns -1.
     *
     * @param T $element
     * @param int $from
     * @return int
     */
    public function indexOf($element, int $from = 0): int;

    /**
     * Returns index of first element in collection that matches given filter. If $from argument is greater than zero,
     * it will start looking from given index.
     *
     * If no element matches given filter, returns -1.
     *
     * @param callable(T): bool $filter
     * @param int $from
     * @return int
     */
    public function indexWhere(callable $filter, int $from = 0): int;

    /**
     * Creates a new collection of elements from chosen interval. Element "$x" of collection "$c" will be a member of
     * new collection if `$from <= $c->indexOf($x) < $to`.
     * @param int $from
     * @param int $to
     * @return IndexedCollection<T>
     */
    public function slice(int $from, int $to): IndexedCollection;

    /**
     * Creates a new collection containing all elements of current collection sorted with given ordering. Ordering
     * function must accept two arguments of type equal to type of elements of collection, and return integer:
     * - if integer is equal to zero, that means that two passed elements are treated as equal.
     * - if integer is lower than zero, that means that left element is lower than right element.
     * - if integer is higher than zero, that means that left element is greater than right element.
     *
     * Result of sorting must be stable, that is: if there are two elements (A and B) in original collection that
     * $ordering treats as equal (that is: $ordering(A,B) === 0), and element A is before element B in that original
     * collection (that is: indexOf(A) < indexOf(B)), then element A should still be before element B in sorted
     * collection.
     *
     * @param callable(T,T): int $ordering
     * @return IndexedCollection<T>
     */
    public function sort(callable $ordering): IndexedCollection;

    /**
     * Returns a new collection, containing all elements of this collection, but with reversed order.
     *
     * @return IndexedCollection<T>
     */
    public function reverse(): IndexedCollection;

    /**
     * Splits collection into two collections on given index and returns array containing those two collections.
     * ```
     * $result = $collection->splitAt($index);
     * ```
     * is equivalent of:
     * ```
     * $result = [ $collection->take($index), $collection->drop($index) ];
     * ```
     *
     * @param int $index
     * @return array{IndexedCollection<T>,IndexedCollection<T>}
     */
    public function splitAt(int $index): array;

    /**
     * Returns a new collection, containing first $count elements of current collection. If $count is lower than or
     * equal to zero, returns empty collection. If $count is equal or larger than length of current collection, returns
     * current collection.
     *
     * @param int $count
     * @return IndexedCollection<T>
     */
    public function take(int $count): IndexedCollection;

    /**
     * Returns a new collection, containing last $count elements of current collection. If $count is lower than or
     * equal to zero, returns empty collection. If $count is equal or larger than length of current collection, returns
     * current collection.
     *
     * @param int $count
     * @return IndexedCollection<T>
     */
    public function takeRight(int $count): IndexedCollection;

    /**
     * Returns a new collection, containing first elements of current collection that satisfy given filter.
     *
     * @param callable(T): bool $filter
     * @return IndexedCollection<T>
     */
    public function takeWhile(callable $filter): IndexedCollection;

    /**
     * Creates a new, empty collection.
     *
     * @return IndexedCollection<null>
     */
    public static function empty(): IndexedCollection;

    /**
     * Creates a new instance of collection with given elements.
     *
     * @template U
     * @param U[] $elements
     * @return IndexedCollection<U>
     */
    public static function with(array $elements): IndexedCollection;
}
