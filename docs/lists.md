## Lists

Lists are collections that keep some amount of elements in some order. Each element of list is assigned to some index:
an integer number from 0 to N-1, where N is a count of elements in the collection. All lists implement `IndexedCollection`
interface.

There are multiple implementations of that interface. Their difference lies in how data is stored internally, but
their outside usage is the same. In this documentation page, `ArrayList` implementation will be used as an example.

### Creating a list

You can create a list with static `with(array $elements)` method:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);
```

You can create an empty list either by calling `with([])` with empty array, or by calling `empty()`:

```php
/** @var ArrayList<int> $emptyList */
$emptyList = ArrayList::with([]);

/** @var ArrayList<int> $otherEmptyList */
$otherEmptyList = ArrayList::empty();
```

The result of both of those methods will be the same, but `empty()` *might* be slightly faster in some implementations.

### Reading values

You can call `get(int $index)` to read a value under given index. Using invalid index (lesser than zero, or greater or
equal to size of collection) will throw `NoSuchElementException`. As an alias to that method, you can also use square
brackets, or treat collection as a function:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);

// calling "get" method:
$list->get(0); // 1
$list->get(1); // 8

// using brackets:
$list[2]; // 23

// calling as a function:
$list(3); // 47

$list->get(4); // NoSuchElementException
```

To prevent `NoSuchElementException`, you can also use `getOption(int $index)` method. It will return an
[optional](./optionals.md) that contains value under given index, or empty optional if there is no element under index.

### Converting to native array

Lists have a `toNative()` method that returns an array containing elements of the list, keeping order:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);

/** @var int[] $array */
$array = $list->toNative(); // [1, 8, 23, 47]
```

### Checking size

You can call a `getLength()` method to get a count of elements. Methods `isEmpty()` and `nonEmpty()` are also defined,
which can be treated as aliases to `getLength() === 0` and `getLength() > 0` respectively:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);
$list->getLength(); // 4
$list->isEmpty(); // false
$list->nonEmpty(); // true

$empty = ArrayList::empty();
$empty->getLength(); // 0
$empty->isEmpty(); // true
$empty->nonEmpty(); // false
```

### Deconstructing

Lists have a `head()` and `headOption()` method that return first element of a list. They're an equivalent of
`get(0)` and `getOption(0)` methods. `head()` will throw a `NoSuchElementException` if collection is empty.

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);

$list->head(); // 1
$list->headOption(); // Optional::some(1)

$empty = ArrayList::empty();

$empty->head(); // throws NoSuchElementException
$empty->headOption(); // Optional::none()
```

There is also a `tail()` method that returns a list containing all elements of current list except for the first one.
If current list is empty, tail of that list is also empty:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);
$list->tail(); // ArrayList::with([8, 23, 47])

$empty = ArrayList::empty();
$empty->tail(); // ArrayList::empty()
```

You can connect calls to both `head()` and `tail()` methods by calling `deconstruct()` method, or connect calls to
`headOption()` and `tail()` by calling `deconstructOption()`. Result of `deconstruct()` and `deconstructOption()`
is an array consisting of two elements: head of list (or optional containing head of list in case of `deconstructOption`)
and a tail:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);

[$head, $tail] = $list->deconstruct(); // $head = 1
                                       // $tail = ArrayList::with([8, 23, 47])
                                       
[$head, $tail] = $list->deconstructOption(); // $head = Optional::some(1)
                                             // $tail = ArrayList::with([8, 23, 47])

$empty = ArrayList::empty();

$empty->deconstruct(); // throws NoSuchElementException
[$head, $tail] = $empty->deconstructOption(); // $head = Optional::none()
                                              // $tail = ArrayList::empty()
```

### Adding new values

You can call `prepended($value)` and `appended($value)` to create a new list, containing a new value added to
either beginning or end of current list:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 8, 23, 47]);

$prependedList = $list->prepended(5); // ArrayList::with([5, 1, 8, 23, 47]);
$appendedList = $list->appended(5); // ArrayList::with([1, 8, 23, 47, 5]);
```

There is also a possibility of merging two lists into one:

```php
/** @var ArrayList<int> $listA */
$listA = ArrayList::with([1, 8, 23, 47]);
/** @var ArrayList<int> $listB */
$listB = ArrayList::with([5, 78, 4]);

$concatedAwithB = $listA->concat($listB); // ArrayList::with([1, 8, 23, 47, 5, 78, 4])
$concatedBwithA = $listB->concat($listA); // ArrayList::with([5, 78, 4, 1, 8, 23, 47])
```

### Cutting values

You can call `drop(int $n)` and `dropRight(int $n)` to remove first or last N elements from the list:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 5, 23, 47]);

$droppedFirst = $list->drop(2); // ArrayList::with([8, 5, 23, 47])
$droppedLast = $list->dropRight(2); // ArrayList::with([1, 78, 8, 5])
```

You can call `take(int $n)` and `takeRight(int $n)` to keep first or last N elements from the list, or
`takeWhile(callable $predicate)` to take all first elements from start that satisfy given predicate:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 5, 23, 47]);

$takenFirst = $list->take(3); // ArrayList::with([1, 78, 8])
$takenLast = $list->takeRight(3); // ArrayList::with([5, 23, 47])

/** @var ArrayList<int> $otherList */
$otherList = ArrayList::with([8, 8, 78, 2, 3, 5, 20, 52]);
$takenWhere = $otherList->takeWhile(fn ($int $i) => $i % 2 === 0); // ArrayList::with([8, 8, 78, 2])
```

You can also use `splitAt(int $index)` to split collection into two lists on given index and get an array containing
both of those collections. It is an equivalent of `[take($index), drop($index)]`:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 5, 23, 47]);

[$left, $right] = $list->splitAt(2); // $left = ArrayList::with([1, 78])
                                     // $right = ArrayList::with([8, 5, 23, 47])
```

There is also a possibility of slicing part of list with `slice(int $from, int $to)`, that will return a new list
containing all elements with index lesser than `$to` and greater or equal to `$from`:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 5, 23, 47]);

$slice = $list->slice(2, 5); // ArrayList::with([8, 5, 23])
```

### Index search

You can use `indexOf($value)` to find index of first appearance of given value in collection, and
`indexWhere(callable $predicate)` to find index of first element of collection that matches predicate. In both cases,
if no element is found, methods will return -1.

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 6, 23, 78, 47]);

$list->indexOf(78); // 1
$list->indexWhere(fn (int $i) => $i > 5 && $i < 10); // 2

$list->indexOf(128); // -1
$list->indexWhere(fn (int $i) => $i > 100); // -1
```

Both `indexOf` and `indexWhere` accept optional second argument that defines starting index - elements lower than
that index will be ignored during search:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 6, 23, 78, 47]);

$list->indexOf(78, 2); // 5
$list->indexOf(78, 6); // -1

$list->indexWhere(fn (int $i) => $i > 5 && $i < 10, 3); // 3
```

### Ordering

In list of type `T`, method `sort(callable $ordering)` requires callable of type `(T,T)=>int` that must return:
- zero, if two passed values are equal
- number greater than zero, if left value is greater than right value
- number lesser than zero, if left value is lower than right value

As a result, `sort` method will return a new list that contains all elements of current list, but sorted starting
from the lowest to the highest element. Sorting must be [stable](https://en.wikipedia.org/wiki/Sorting_algorithm#Stability).

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 6, 23, 78, 47]);
$sorted = $list->sort(fn (int $a, int $b) => $a - $b); // ArrayList::with([1, 6, 8, 23, 47, 78, 78])
```

You can also call `reverse()` method to get a new list with reversed order.

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 78, 8, 6, 23, 78, 47]);
$reversed = $list->reverse(); // ArrayList::with([47, 78, 23, 6, 8, 78, 1])
```

### Iterative methods

All methods described in "Iterative methods over collections" section in [index page](./index.md) are implemented.