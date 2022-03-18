<?php

namespace Squingla\Collections;

/**
 * Interface for all collections that use tuples as a values. Tuple keys must be unique in a dictionary.
 *
 * @template K
 * @template V
 * @template-extends Collection<Tuple<K,V>>
 * @template-extends CollectionWithKey<K,V>
 */
interface Dictionary extends Collection, CollectionWithKey
{
    /**
     * If given key does not exist in current dictionary, returns a new dictionary containing all elements of current
     * dictionary with new tuple containing key-value pair. Otherwise, returns this dictionary without change.
     *
     * ```
     * $new = $old->put($key, $value);
     * ```
     * gives the same result as:
     * ```
     * $new = $old->hasKey($key) ? $old : $old->set($key, $value);
     * ```
     *
     * @param K $key
     * @param V $value
     * @return Dictionary<K,V>
     */
    public function put(mixed $key, mixed $value): Dictionary;

    /**
     * If given key does not exist in current dictionary, returns a new dictionary containing all elements of current
     * dictionary with new tuple containing key-value pair. Otherwise, returns a new dictionary with containing all
     * elements of current dictionary, with new value set in given key.
     *
     * @param K $key
     * @param V $value
     * @return Dictionary<K,V>
     */
    public function set(mixed $key, mixed $value): Dictionary;

    /**
     * Returns "true" if dictionary contains entry with given key, "false" otherwise.
     *
     * @param K $key
     * @return bool
     */
    public function hasKey(mixed $key): bool;

    /**
     * Returns "true" if dictionary contains entry with given value, "false" otherwise.
     *
     * @param V $value
     * @return bool
     */
    public function hasValue(mixed $value): bool;

    /**
     * Returns an empty dictionary.
     *
     * @return Dictionary<null,null>
     */
    public static function empty(): Dictionary;

    /**
     * Creates a new dictionary from array, where indices of array are used as dictionary keys. Limits key types to "int"
     * and "string".
     *
     * @template NK of string|int
     * @template NV
     * @param array<NK,NV> $elements
     * @return Dictionary<NK,NV>
     */
    public static function fromIndexedArray(array $elements): Dictionary;

    /**
     * Creates a new dictionary from list of tuples.
     *
     * @template NK
     * @template NV
     * @param Tuple<NK,NV> ...$tuples
     * @return Dictionary<NK,NV>
     */
    public static function fromTuples(Tuple ...$tuples): Dictionary;

    /**
     * Creates a new dictionary from list of arrays, where the first element of array is a key and the second element
     * is a value. For example:
     * ```
     * $dictionary = Dictionary::fromTupleArrays(
     *     ["key1", "value1"],
     *     ["key2", "value2"],
     * );
     * ```
     *
     * @template NK
     * @template NV
     * @param array{NK,NV} ...$tuples
     * @return Dictionary<NK,NV>
     */
    public static function fromTupleArrays(array ...$tuples): Dictionary;
}
