## PHP Collections Library

Collections library for PHP, inspired by Scala.

### Features

Planned features:
- Array lists
- Queues and stacks
- Dictionaries - collections that allow indexing by values of any types
  - ArrayDictionary - simplest, most universal, but also slowest dictionary
  - SortedDictionary - for keys that are comparable, keeps dictionary sorted by key
- Lazy collections (for example for chained `filter`, `map` etc.)


#### Optional

`Optional` object represents an optional value. It can be treated as a collection that is either empty, or contains
exactly one element, but never more. Optionals are constructed via its static `some` or `none` methods:

```php
/** @var Optional<int> $someOptional */
$someOptional = Optional::some(15);
$mappedValue = $someOptional->map(fn (int $i) => $i * 2);
echo $mappedValue->get(); // 30
echo $mappedValue->getOrNull(); // 30
echo $mappedValue->getOrElse(100); // 30

/** @var Optional<int> $emptyOptional */
$emptyOptional = Optional::empty();
$mappedValue = $emptyOptional->map(fn (int $i) => $i * 2);
echo $mappedValue->get(); // throws NoSuchElementException
echo $mappedValue->getOrNull(); // null
echo $mappedValue->getOrElse(100); // 100
```

#### Lists

Lists behave like `array` with `int` indices. All lists implement `IndexedCollection` interface, with support for
Psalm/PhpStan generic types. Objects can be created with two static methods: `with(array $elements)` and `empty()`.
While result of `empty()` might be the same as a result of `with([])`, sometimes creating empty list of `empty()` might
be slightly faster.

Example usages of lists:

```php
/** LinkedList<string> $list */
$list = LinkedList::with(["John Doe", "Jane Smith"]);

echo $list->get(0); // "John Doe"
echo $list->get(1); // "Jane Smith"
echo $list->get(2); // throws NoSuchElementException`
$optional = $list->getOption(2); // returns empty Optional<string>

/** @var LinkedList<string> $newList */
$newList = $list->prepended("Larry Loe");
echo $newList->get(0); // "Larry Loe"
echo $newList->get(1); // "John Doe"
echo $newList->get(2); // "Jane Smith"
```

Currently, `LinkedList` is the only implementation of `IndexedCollection`. It stores elements in a
[singly linked list](https://en.wikipedia.org/wiki/Linked_list), which means that every `LinkedList` object is either:
- an empty list (a terminator), or
- a node, containing a single element of a list and a reference to next `LinkedList`.

That implementation allows for fast operations on the beginning of list, but slowing down linearly as operation moves
to the end of a list.

### Development

Library uses Docker Compose during development.

To install dependencies:
```shell
docker-compose run php composer install
```

To run tests:

```shell
docker-compose run php vendor/bin/phpunit
docker-compose run php vendor/bin/phpstan analyze
```