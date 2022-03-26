## Optionals

Optionals are objects that represent a presence of something or a lack of something. It is a close behaviour to nullable
types. You can also treat an optional as an collection (for example, an array) that can either be empty or contain
exactly one value.

* [Creating an optional](#creating-an-optional)
* [Presence checking](#presence-checking)
* [Getting content of optional](#getting-content-of-optional)
* [Iterative methods](#iterative-methods)
  * [Value mapping](#value-mapping)
  * [Filtering](#filtering)
  * [Value testing](#value-testing)
  * [Folding and reducing](#folding-and-reducing)
* [Optional of nullable value](#optional-of-nullable-value)

### Creating an optional

You can create an optional with two static methods of `Optional` class: `some($value)` creates an optional with a value,
and `none()` creates an empty optional:

```php
/** @var Optional<int> $someInt */
$someInt = Optional::some(15);

/** @var Optional<int> $emptyInt */
$emptyInt = Optional::none();
```

Generic-wise, `Optional::none()` returns type that is understood by PhpStan/Psalm as `Optional<null>`, but because
all empty optionals, no matter the type, are the same, there is no problem with defining another type with `@var`
annotation.

### Presence checking

Optionals have `isEmpty()` and `nonEmpty()` functions that both return `bool` and are negations of each other. It also
implements two methods from `IterableOnce` interface:
- `getLength(): int`, which will return 0 for empty optionals and 1 for non-empty optionals
- `count(callable $filter): int`, which return 1 for non-empty optional that contains a value matching filter, and 0 otherwise

```php
/** @var Optional<int> $someInt */
$someInt = Optional::some(15);
$someInt->isEmpty(); // false
$someInt->nonEmpty(): // true
$someInt->getLength(); // 1
$someInt->count(fn (int $i) => $i > 10); // 1
$someInt->count(fn (int $i) => $i > 100); // 0

/** @var Optional<int> $emptyInt */
$emptyInt = Optional::none();
$emptyInt->isEmpty(); // true
$emptyInt->nonEmpty(); // false
$someInt->getLength(); // 0
$someInt->count(fn (int $i) => $i > 10); // 0
$someInt->count(fn (int $i) => $i > 100); // 0
```

You can also use the `ifSet(callable $consumer)` and `ifEmpty(callable $action)` methods. Callable passed in `ifSet`
will be called only if optional has a value, while callable passed in `ifEmpty` will be called only if optional does
not have a value. In case of `ifSet`, content of the optional will be passed as an argument to the callable.

```php
/** @var Optional<int> $someInt */
$someInt = Optional::some(15);
$someInt->ifSet(fn (int $i) => doSomething($i)); // "doSomething" will be called with "15" as an argument
$someInt->ifEmpty(fn () => doSomething()); // "doSomething" will not be called

/** @var Optional<int> $emptyInt */
$emptyInt = Optional::none();
$emptyInt->ifSet(fn (int $i) => doSomething($i)); // "doSomething" will not be called
$emptyInt->ifEmpty(fn () => doSomething()); // "doSomething" will be called
```

### Getting content of optional

The most basic and most dangerous way of getting value from optional is to call a `get()` method, but that method
**will throw** `NoSuchElementException` if optional is empty:

```php
Optional::some(15)->get(); // 15
Optional::none()->get(); // throws NoSuchElementException
```

Safer way is to call a `getOrElse($default)` method that, for empty optionals, will return a passed default value:

```php
Optional::some(15)->getOrElse(30); // 15
Optional::none()->getOrElse(30); // 30
```

There is also an additional `getOrNull()` method that can be treated as an alias for `getOrElse(null)`:

```php
Optional::some(15)->getOrNull(); // 15
Optional::none()->getOrNull(); // null
```

If you still want to use optional, but you want it to contain a default value, you can use `orElse($value)` method:
```php
Optional::some(15)->orElse(30)->get(); // 15
Optional::none()->orElse(30)->get(); // 30
```

### Iterative methods

Because `Optional` class implements `IterableOnce` interface, it comes with some methods that, while having a huge sense
for collections, might not be obvious here. While those methods are described in details below, it is worth noting that
`IterableOnce` interface extends a native `IteratorAggregate` interface from PHP standard library, which allows you
to run a `foreach` over an optional:

```php
foreach (Optional::some(15) as $value) {
    echo $value; // will print "15" once
}

foreach (Optional::none() as $value) {
    echo $value; // won't do anything 
}
```

You will achieve the same result with `forEach` method:

```php
Optional::some(15)->forEach(
    function (int $i) {
        echo $i; // will print "15" once
    }
);
Optional::none()->forEach(
    function (int $i) {
        echo $i; // won't do anything
    }
);
```

#### Value mapping

`map(callable $mapper)` applies content of optional to a `$mapper` and creates a new optional with result. If current
optional is empty, then resulting optional will also be empty.

```php
Optional::some(15)->map(fn (int $i) => $i + 3)->get(); // 18
Optional::none()->map(fn (int $i) => $i + 3)->isEmpty(); // true
```

If callable returns another optional instead of a value, instead of having an "optional inside optional" you can use
a `flatMap` to flatten results to a single optional:

```php
// with map():
/** @var Optional<Optional<int>> $twoLayers */
$twoLayers = Optional::some(15)->map(fn (int $i) => Optional::some($i + 3));
$twoLayers->get()->get(); // 18

// with flatMap():
/** @var Optional<int> $singleLayer */
$singleLayer = Optional::some(15)->flatMap(fn (int $i) => Optional::some($i + 3));
$singleLayer->get(); // 18
```

And another example, with callable returning empty optional:

```php
// with map():
/** @var Optional<Optional<int>> $twoLayers */
$twoLayers = Optional::some(15)->map(fn (int $i) => Optional::none());
$twoLayers->isEmpty(); // false
$twoLayers()->get()->isEmpty(); // true

// with flatMap():
/** @var Optional<int> $singleLayer */
$singleLayer = Optional::some(15)->flatMap(fn (int $i) => Optional::none());
$twoLayers->isEmpty(); // true
```

#### Filtering

`filter(callable $predicate)` and `filterNot(callable $predicate)` will return optionals that are:
- empty, if current optional is also empty
- current optional, if value stored in optional matches (or, in case of `filterNot`, does not match) a passed predicate
- empty otherwise.

```php
Optional::some(15)->filter(fn (int $i) => $i > 10)->get(); // 15
Optional::some(15)->filter(fn (int $i) => $i > 100)->isEmpty(); // true

Optional::some(15)->filterNot(fn (int $i) => $i > 10)->isEmpty(); // true
Optional::some(15)->filterNot(fn (int $i) => $i > 100)->get(); // 15

Optional::none()->filter(fn (int $i) => $i > 10)->isEmpty(); // true
Optional::none()->filterNot(fn (int $i) => $i > 10)->isEmpty(); // true
```

#### Value testing

`IterableOnce` interface defines two methods for value testing:
- `exists(callable $predicate): bool`, that will return `true` if there is at least one element of collection that
  matches a predicate 
- `forAll(callable $predicate): bool`, that will return `true` if all elements of collection match a predicate.

From perspective of optionals, the most important difference between those two functions is the fact that if any
collection is empty, then `forAll` will always return `true` (because if there are no elements, that means that all
elements match a predicate).

That means that:
- for non-empty optionals, both `exists` and `forAll` will return `true` if value in optional matches given predicate
- for empty optionals, `exists` will return `false` and `forAll` will return `true`:

```php
Optional::some(15)->exists(fn (int $i) => $i > 10); // true
Optional::some(15)->forAll(fn (int $i) => $i > 10); // true

Optional::some(15)->exists(fn (int $i) => $i > 100); // false
Optional::some(15)->forAll(fn (int $i) => $i > 100); // false

Optional::none()->exists(fn (int $i) => $i > 10); // false
Optional::none()->forAll(fn (int $i) => $i > 10); // true
```

#### Folding and reducing

Six methods of folding and reducing defined in `IterableOnce` interface are described in more detail in separate
documentation page. In case of optionals, these methods do not have much sense and can be described as aliases of
other methods:
* `foldLeft($startValue, callable $operator)` is equal to
  `map(fn ($value) => $operator($startValue, $value))->getOrElse($startValue)`
* `foldRight($startValue, callable $operator)` is equal to
  `map(fn ($value) => $operator($value, $startValue))->getOrElse($startValue)` (only difference between those is order
  of arguments when invoking `$operator` callable inside `map()`)
* `reduceLeft(callable $operator)` and `reduceLeft(callable $operator)` ignore `$operator` completely and instead return
  result content of optional, if optional is non-empty, or throw `UnsupportedTraversalException` if optional is empty.
* `reduceLeftOption(callable $operator)` and `reduceRightOption(callable $operator)` both return `$this`. 

### Optional of nullable value

Note that `null` can also be stored inside an Optional, just like it can be stored inside array:

```php
/** @var Optional<int|null> $someNullableWithInt */
$someNullableWithInt = Optional::some(15);

/** @var Optional<int|null> $someNullableWithNull */
$someNullableWithNull = Optional::some(null);

/** @var Optional<int|null> $emptyNullable */
$emptyNullable = Optional::none();
```

In this example, `$someNullableWithNull` is **not** an empty optional, but an optional with value - it just so happen
that this value is equal to null.

Real life usage of optionals of nullable values is parsing request body for `PATCH` request. In typical REST API,
in `PATCH` request only changed properties should be sent. For example, let's consider a resource representing a single
person, with three fields:
- `firstName` of type `string`
- `middleName` of type `string|null`
- `lastName` of type `string`

Example resource:
```json
{
    "firstName": "John",
    "middleName": "Adam",
    "lastName": "Doe"
}
```

If we want to change `lastName` to "Smith", we should send `PATCH` request to that resource with content that contains
only properties that we want to change:

```json
{
    "lastName": "Smith"
}
```

Now the API, written in PHP, converts this request to object of class that represents a `PATCH` request. How to store
information that remaining two fields were not sent and thus should not change? While the first, most obvious answer
to that would be "use nullable types", it creates a new problem with `middleName` field: `null` is a proper value there,
because we decided that `middleName` is a nullable property.

Another solution would be to add a boolean flag, for example `$isMiddleNamePassed`, to the request class:
- if `$isMiddleNamePassed === false`, that means that middle name was not passed in request and thus it should not change
- if `$isMiddleNamePassed === true` and `$middleName === null`, that means that `null` was passed as a value in request
  and thus middle name should be changed to `null`.

This "value + flag" pairing is what optionals do. While entity representing a single person still has fields with
simple `string` or `string|null` types, a class representing a `PATCH` request will use optionals:
- `firstName` of type `Optional<string>`
- `middleName` of type `Optional<string|null>`
- `lastName` of type `Optional<string>`

If a property is not passed in request, then request object will contain `Optional::none()` for that property. If
a property is passed, request object will contain `Optional::some($value)` for that property, even if that value can be
equal to `null`.