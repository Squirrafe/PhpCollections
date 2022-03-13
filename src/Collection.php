<?php

namespace Squingla\Collections;

/**
 * Basic interface for collections that can contain more than one element.
 * @template T
 * @template-extends IterableOnce<T>
 */
interface Collection extends IterableOnce
{
    /**
     * Creates a new collection with a value added to end of collection.
     *
     * @param T $value
     * @return Collection<T>
     */
    public function appended(mixed $value): Collection;

    /**
     * Creates a new collection with a value added to beginning of application.
     * @param T $value
     * @return Collection<T>
     */
    public function prepended(mixed $value): Collection;

    /**
     * Merges two collections of the same type into one collection containing all elements of both collections.
     *
     * @param Collection<T> $collection
     * @return Collection<T>
     */
    public function concat(Collection $collection): Collection;

    /**
     * Returns first element of collection. Throws exception if collection is empty.
     *
     * @return T
     * @throws NoSuchElementException if collection is empty
     */
    public function head(): mixed;

    /**
     * Returns optional containing first element of collection. If collection is empty, returns empty optional.
     *
     * @return Optional<T>
     */
    public function headOption(): Optional;

    /**
     * Returns all elements of collection except for first one.
     *
     * @return Collection<T>
     */
    public function tail(): Collection;

    /**
     * Returns array with first index containing head of collection and second index containing tail of collection.
     * If collection is empty, throws exception.
     *
     * ```
     * $array = $collection->deconstruct();
     * ```
     * if equivalent of
     * ```
     * $array = [ $collection->head(), $collection->tail() ];
     * ```
     *
     * @return array{T,Collection<T>}
     * @throws UnsupportedTraversalException if collection is empty
     */
    public function deconstruct(): array;

    /**
     * Returns array with first index containing optional head of collection and second index containing tail of
     * collection.
     *
     * ```
     * $array = $collection->deconstructOption();
     * ```
     * if equivalent of
     * ```
     * $array = [$collection->headOption(), $collection->tail()];
     * ```
     *
     * @return array{Optional<T>,Collection<T>}
     */
    public function deconstructOption(): array;

    /**
     * Converts collection to native PHP array.
     *
     * @return T[]
     */
    public function toNative(): array;
}