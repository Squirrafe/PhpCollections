<?php

namespace Squingla\Collections\Dictionary;

use Squingla\Collections\Collection;
use Squingla\Collections\Dictionary\Tuple\SimpleTuple;
use Squingla\Collections\Dictionary\Tuple\Tuple;
use Squingla\Collections\IterableOnce;
use Squingla\Collections\Lists\ArrayList;
use Squingla\Collections\Optional;

/**
 * Simplest form of dictionary, behaves similarly to ArrayList with tuples instead of values.
 *
 * @template K
 * @template V
 * @template-extends AbstractDictionary<K,V>
 */
class ArrayDictionary extends AbstractDictionary
{
    /** @var ArrayList<Tuple<K,V>> */
    private ArrayList $tuplesList;

    /**
     * @param ArrayList<Tuple<K,V>> $tuplesList
     */
    private function __construct(ArrayList $tuplesList)
    {
        $this->tuplesList = $tuplesList;
    }

    /**
     * @param Collection<Tuple<K,V>> $collection
     * @return ArrayDictionary<K,V>
     */
    public function concat(Collection $collection): ArrayDictionary
    {
        /** @var Collection<Tuple<K,V>> $filtered */
        $filtered = $collection->filter(
            fn (Tuple $t) => !$this->hasKey($t->getKey()),
        );
        return new ArrayDictionary($this->tuplesList->concat($filtered));
    }

    /**
     * @return Optional<Tuple<K,V>>
     */
    public function headOption(): Optional
    {
        return $this->tuplesList->headOption();
    }

    /**
     * @return ArrayDictionary<K,V>
     */
    public function tail(): ArrayDictionary
    {
        return new ArrayDictionary($this->tuplesList->tail());
    }

    /**
     * @return Tuple<K,V>[]
     */
    public function toNative(): array
    {
        return $this->tuplesList->toNative();
    }

    /**
     * @param K $key
     * @return Optional<V>
     */
    public function getOption($key): Optional
    {
        /** @var Optional<Tuple<K,V>> $option */
        $option = $this->tuplesList
            ->filter(fn (Tuple $tuple) => $tuple->getKey() === $key)
            ->headOption();
        /** @var Optional<V> $mappedOption */
        $mappedOption = $option->map(fn (Tuple $tuple) => $tuple->getValue());
        return $mappedOption;
    }

    /**
     * @param K $key
     * @param V $value
     * @return ArrayDictionary<K,V>
     */
    public function set($key, $value): ArrayDictionary
    {
        if ($this->hasKey($key)) {
            /** @var ArrayList<Tuple<K,V>> $mapped */
            $mapped = $this->tuplesList->map(
                function (Tuple $entry) use ($key, $value) {
                    if ($entry->getKey() !== $key) return $entry;
                    return new SimpleTuple($key, $value);
                }
            );
            return new ArrayDictionary($mapped);
        }

        return new ArrayDictionary($this->tuplesList->appended(new SimpleTuple($key, $value)));
    }

    /**
     * @param K $key
     * @param V $value
     * @return ArrayDictionary<K,V>
     */
    public function put($key, $value): ArrayDictionary
    {
        if ($this->hasKey($key)) {
            return $this;
        }

        return new ArrayDictionary($this->tuplesList->appended(new SimpleTuple($key, $value)));
    }

    /**
     * @param K $key
     * @return bool
     */
    public function hasKey($key): bool
    {
        return $this->tuplesList->exists(fn (Tuple $t) => $t->getKey() === $key);
    }

    /**
     * @param V $value
     * @return bool
     */
    public function hasValue($value): bool
    {
        return $this->tuplesList->exists(fn (Tuple $t) => $t->getValue() === $value);
    }

    /**
     * @return ArrayDictionary<null,null>
     */
    public static function empty(): ArrayDictionary
    {
        /** @var ArrayList<Tuple<null,null>> $empty */
        $empty = ArrayList::empty();
        return new ArrayDictionary($empty);
    }

    /**
     * @template NK of int|string
     * @template NV
     * @param array<NK,NV> $elements
     * @return ArrayDictionary<NK,NV>
     */
    public static function fromIndexedArray(array $elements): ArrayDictionary
    {
        /** @var Tuple<NK,NV>[] $tuples */
        $tuples = [];
        /**
         * @var NK $key
         * @var NV $value
         */
        foreach ($elements as $key => $value) {
            $tuples[] = new SimpleTuple($key, $value);
        }
        /** @var ArrayList<Tuple<NK,NV>> $list */
        $list = ArrayList::with($tuples);
        return new ArrayDictionary($list);
    }

    /**
     * @template NK
     * @template NV
     * @param Tuple<NK,NV> ...$tuples
     * @return ArrayDictionary<NK,NV>
     */
    public static function fromTuples(Tuple ...$tuples): ArrayDictionary
    {
        /** @var ArrayList<Tuple<NK,NV>> $list */
        $list = ArrayList::with($tuples);
        return new ArrayDictionary($list);
    }

    /**
     * @template NK
     * @template NV
     * @param array{NK,NV} ...$elements
     * @return ArrayDictionary<NK,NV>
     */
    public static function fromTupleArrays(array ...$elements): ArrayDictionary
    {
        /** @var Tuple<NK,NV>[] $tuples */
        $tuples = [];
        foreach ($elements as $element) {
            $tuples[] = new SimpleTuple($element[0], $element[1]);
        }
        /** @var ArrayList<Tuple<NK,NV>> $list */
        $list = ArrayList::with($tuples);
        return new ArrayDictionary($list);
    }

    public function getLength(): int
    {
        return $this->tuplesList->getLength();
    }

    /**
     * @param callable(Tuple<K,V>): bool $filter
     * @return ArrayDictionary<K,V>
     */
    public function filter(callable $filter): ArrayDictionary
    {
        return new ArrayDictionary(
            $this->tuplesList->filter($filter),
        );
    }

    /**
     * @param callable(Tuple<K,V>): bool $filter
     * @return ArrayDictionary<K,V>
     */
    public function filterNot(callable $filter): ArrayDictionary
    {
        return new ArrayDictionary(
            $this->tuplesList->filterNot($filter),
        );
    }

    /**
     * @template U
     * @param callable(Tuple<K,V>): U $mapper
     * @return IterableOnce<U>
     */
    public function map(callable $mapper): IterableOnce
    {
        return $this->tuplesList->map($mapper);
    }

    /**
     * @template U
     * @param callable(Tuple<K,V>): IterableOnce<U> $mapper
     * @return IterableOnce<U>
     */
    public function flatMap(callable $mapper): IterableOnce
    {
        /** @var IterableOnce<U> $result */
        $result = $this->tuplesList->flatMap($mapper);
        return $result;
    }

    /**
     * @template U
     * @param U $startValue
     * @param callable(U,Tuple<K,V>): U $operator
     * @return U
     */
    public function foldLeft($startValue, callable $operator)
    {
        return $this->tuplesList->foldLeft($startValue, $operator);
    }

    /**
     * @template U
     * @param U $startValue
     * @param callable(Tuple<K,V>,U): U $operator
     * @return U
     */
    public function foldRight($startValue, callable $operator)
    {
        return $this->tuplesList->foldRight($startValue, $operator);
    }

    /**
     * @param callable(Tuple<K,V>): void $consumer
     * @return void
     */
    public function forEach(callable $consumer): void
    {
        $this->tuplesList->forEach($consumer);
    }
}
