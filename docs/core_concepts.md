## Core concepts

Coming from Scala and other functional programming languages, this library shares some basic concepts on how any
language should work.

### Object immutability

As a rule of thumb, objects that exist should not change. That is of course only a recommendation - there are always
places where changing state of an object is required for readability. For the majority of cases however, we believe
that immutable objects are safer, more predictable, easier to test and to work with.

As an example from our experiences: probably every PHP developer has at least once lost a bit of time and nerves on
debugging problems with standard `DateTime` class in case where `DateTimeImmutable` had a preferred behaviour:

```php
function getNextDay(DateTime $dateTime): DateTime
{
    return $dateTime->modify('+ 1 day');
}

$originalTime = new DateTime('2022-03-21 08:15:00');
$nextDay = $this->getNextDat($originalTime);
echo $nextDay->format('Y-m-d H:i:s');      // 2022-03-22 08:15:00
echo $originalTime->format('Y-m-d H:i:s'); // 2022-03-22 08:15:00 too!
```

In case of those two classes, authors of standard library have decided that mutable behaviour should be a default one
and immutable behaviour will be marked (with additional "Immutable" word in class name). We believe that it was a huge
mistake - when we write code, we try "immutable-first" and add mutability only when it is truly needed (either because
of domain requirements, or because in that case code will be cleaner).

With that thought, all classes in this library are immutable. When you create a collection with some elements, you
can safely pass that collection to other functions without being afraid that content of your collection will change.

### Functions are first-class citizens

All collections in this library have methods allowing for simpler iterations, filtering and mapping of results with
usage of lambdas/anonymous functions. It makes your code cleaner than using for loops.

Consider those examples of function that get an array with integers and is supposed to return another array, containing
only even numbers from original array, divided by 2. The most naïve approach would be to run `foreach` loop over array:

```php
/**
 * @param int[] $input
 * @return int[]
 */
function withForEach(array $input): array
{
    $result = [];
    foreach ($input as $number) {
        if ($number % 2 === 0) {
            $result[] = $number / 2;
        }
    }
    return $result;
}
```

Another approach, with usage of lambdas, would be to use an `array_filter` and `array_map` functions:

```php
/**
 * @param int[] $input
 * @return int[]
 */
function withArrayFilterAndArrayMap(array $input): array
{
    // remember about argument order! array_filter has an array as first argument and a lambda as a second one...
    $filtered = array_filter(
        $input,
        fn (int $i) => $i % 2 === 0,
    );
    // ... while array_map has a lambda as a first argument and an array as a second one.
    return array_map(
        fn (int $i) => $i / 2,
        $input,
    );
}
```

And then, we have a usage of `ArrayList` class from Collections library:

```php
/**
 * @param int[] $input
 * @return int[]
 */
function withArrayFilterAndArrayMap(array $input): array
{
    return ArrayList::with($input)
        ->filter(fn (int $i) => $i % 2 === 0) // filter input
        ->map(fn (int $i) => $i / 2) // map filtered values
        ->toNative(); // return native PHP array
}
```

Treating functions as first-class citizens does not stop here. Most of the collections in this library behave very closely
to how functions behave:
- lists of type T behave like a function `f(int) => T`
- dictionaries of key type K and value type V behave like a function `f(K) => V`

Because of that correlation, almost all collections in this library implement `__invoke` method that accepts indices
(keys in dictionaries, integers in lists) and return values stored under given indices. You can pass those collections
to functions that require a `callable` argument:

```php
function example(callable $c, int $from, int $to): void
{
    for ($i = $from; $i < $to; $i++) {
        echo $c($i);
    }
}

/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 33, 46]);
example($list, 0, $list->getLength());
```

### Polymorphism

There is a whole structure of interfaces in this library, giving more and more precise methods. It allows you to
write code that really does not care about implementation details. If you write a function that will require some
collection that you will use to iterate over elements, you don't need to choose a concrete class - `IterableOnce`
interface, the *lowest* in library, can be used in `foreach` loop, it also contains methods for filtering, mapping,
counting, folding and reducing.

And even if you need a more precise type, there is often no need for using concrete classes as argument types. For
example, if your function really needs a list of objects where indices of that list are integers (like a typical array),
there is an `IndexedCollection` interface that matches your requirement. There is no need for deciding that argument
type for your function should be a concrete implementation (`LinkedList`? `ArrayList`?) - they don't give you any
additional usable methods. Leave that decision to a place in your code that has to create such list. Any time you decide
that you need to use a different implementation, you will only have to change it in that one place.

### Generic typing

While PHP does not have a generic types on its own, there are static analysis tools like PhpStan or Psalm that use
annotations in phpdoc blocks to define and control generic-like behaviour. All interfaces and classes in Collections
library uses these mechanisms, giving you a possibility of smoothly adding both Collections library to project using
PhpStan, and PhpStan to project using Collections library.

As an example, creating list of integers and trying to add a string to it, while unfortunately a completely valid PHP
code, will be marked as error by PhpStan during analysis:

```php
/** @var ArrayList<int> $list */
$list = ArrayList::with([1, 33, 46]);

$listWithNewElement = $list->prepended('foo'); // PhpStan should mark it as an error
```