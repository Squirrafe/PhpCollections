<?php

namespace Squingla\Collections\Dictionary;

use Squingla\Collections\Collection;
use Squingla\Collections\CollectionWithKey;
use Squingla\Collections\Dictionary\Tuple\Tuple;
use Squingla\Collections\Lists\IndexedCollection;

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
    public function put($key, $value): Dictionary;

    /**
     * If given key does not exist in current dictionary, returns a new dictionary containing all elements of current
     * dictionary with new tuple containing key-value pair. Otherwise, returns a new dictionary with containing all
     * elements of current dictionary, with new value set in given key.
     *
     * @param K $key
     * @param V $value
     * @return Dictionary<K,V>
     */
    public function set($key, $value): Dictionary;

    /**
     * Returns "true" if dictionary contains entry with given key, "false" otherwise.
     *
     * @param K $key
     * @return bool
     */
    public function hasKey($key): bool;

    /**
     * Returns "true" if dictionary contains entry with given value, "false" otherwise.
     *
     * @param V $value
     * @return bool
     */
    public function hasValue($value): bool;

    /**
     * Returns a list containing all key-value tuples in this dictionary.
     *
     * @return IndexedCollection<Tuple<K,V>>
     */
    public function toList(): IndexedCollection;

    /**
     * Returns a list containing all keys in this dictionary.
     *
     * @return IndexedCollection<K>
     */
    public function keyList(): IndexedCollection;

    /**
     * Returns a list containing all values in this dictionary.
     *
     * @return IndexedCollection<V>
     */
    public function valueList(): IndexedCollection;

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
