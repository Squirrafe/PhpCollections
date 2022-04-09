## Dictionaries

Dictionaries are collections that assign a value to a unique keys. Each element of dictionary is a tuple containing
two elements: a key and a value. All dictionaries implement `Dictionary<Key,Value>` interface. In this documentation
page, the `ArrayDictionary<K,V>` implementation will be used in examples.

The closest thing to a dictionary in native PHP is an array with string keys (`[ "foo" => "bar" ]`), or unordered
integer keys (`[ 5 => "value" ]`). Those arrays only accept integers and strings as keys. `Dictionary<K,V>` interface
accepts **any** type as a key.

* [Tuple class](#tuple-class)
* [Creating a dictionary](#creating-a-dictionary)
* [Reading values](#reading-values)
* [Adding new values](#adding-new-values)
* [Iterations](#iterations)

### Tuple class

There is a `Tuple` interface (with `SimpleTuple` implementation) that is used as both arguments' types and return types
in various methods of the `Dictionary` interface. Each object of `Tuple` type contains two elements: a key and a value:

```php
$tuple = new SimpleTuple("foo", "bar");
$tuple->getKey(); // "foo"
$tuple->getValue(); // "bar"
```

### Creating a dictionary

All implementations of `Dictionary` interface has static methods that allow creating dictionaries in various ways.

Simplest form of dictionary creation is using `empty()` method to create an empty dictionary:

```php
$dictionary = ArrayDictionary::empty();
```

If you have a list of `Tuple` objects, you can pass them to variadic `fromTuples` method:

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromTuples(
    new SimpleTuple("foo", "bar"),
    new SimpleTuple("baz", "qux"),
);
```

Instead of creating objects implementing `Tuple` interface, you can also use `fromTupleArrays`, which replaces `Tuple`
objects with two-element arrays:

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromTupleArrays(
    ["foo", "bar"],
    ["baz", "qux"],
);
```

If you have an associative PHP array with string or integer keys, you can convert it into dictionary with `fromIndexedArray`
method:

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
    "baz" => "qux",
]);
```

### Reading values

The most basic way of accessing values is with usage of `get` and `getOption` methods.
`get(K key): V` will return value under given key or throw `NoSuchElementException` if given key does not have a value.
`getOption(K key): Optional<V>` will return [optional](./optionals.md) containing a value under given key or empty
optional if given key does not have a value.

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
]);

$dictionary->get("foo"); // "bar"
$dictionary->getOption("foo")->nonEmpty(); // true
$dictionary->getOption("foo")->get(); // "bar"

$dictionary->get("baz"); // throws NoSuchElementException
$dictionary->getOption("baz")->isEmpty(); // true
```

Dictionaries also define magic `__invoke` method, so you can treat dictionaries like functions:

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
    "baz" => "qux",
]);

$dictionary("foo"); // "bar"

function example(callable $callable) {
    $callable("foo");
}

example($dictionary);
```

You can also use square brackets to access values just like arrays:

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
    "baz" => "qux",
]);

$dictionary["foo"]; // "bar"
```

In contrast to native associative arrays, nothing stops you from using other types than strings and integers as keys
here. For example, dictionary below uses arrays of integers as a key:

```php
/** @var Dictionary<int[],string> $dictionary */
$dictionary = ArrayDictionary::fromTupleArrays(
    [ [1, 1], "foo" ],
    [ [1, 2], "bar" ],
    [ [2, 1], "baz" ],
    [ [2, 2], "qux" ],
);

$dictionary[[2, 1]]; // "baz"
```

### Adding new values

There are two basic methods for creating a new dictionary with updated values: `put($key, $value)` and
`set($key, $value)`. They behave the same way when given a key that does not exist in dictionary: they return a new
dictionary containing all elements of current dictionary with new tuple added:

```php
/** @var Dictionary<string,string> $original */
$original = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
    "baz" => "qux",
]);

$set = $original->set("corge", "waldo");
$set->get("foo"); // "bar"
$set->get("corge"); // "waldo"

$put = $original->put("corge", "waldo");
$put->get("foo"); // "bar"
$put->get("corge"); // "waldo"
```

The difference in behaviour appears when already existing key is passed as an argument, because all keys in dictionary
are unique: `put` will ignore a new key and return unchanged dictionary, while `set` will return a new dictionary with
a replaced value under given key:

```php
/** @var Dictionary<string,string> $original */
$original = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
    "baz" => "qux",
]);

$set = $original->set("foo", "fred");
$set->get("foo"); // "fred"

$put = $original->put("foo", "fred");
$put->get("foo");// "bar"
```

### Iterations

All methods described in "Iterative methods over collections" section in [index page](./index.md) are implemented. You
must note that `Tuple` objects are used there:

```php
/** @var Dictionary<string,string> $dictionary */
$dictionary = ArrayDictionary::fromIndexedArray([
    "foo" => "bar",
    "baz" => "qux",
]);

/** @var Tuple<string,string> $tuple */
foreach ($dictionary as $tuple) {
    $tuple->getKey();
}
```