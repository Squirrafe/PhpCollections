## Folding and reducing

Folding and reducing are very similar processes that convert a collection of elements into a single value. There are
six methods, defined up in `IterableOnce` interface, grouped into three left-right pairs:
- [`foldLeft` and `foldRight`](#foldleft-and-foldright)
- [`reduceLeft` and `reduceRight`](#reduceleft-and-reduceright)
- [`reduceLeftOption` and `reduceRightOption`](#reduceleftoption-and-reducerightoption)

All of those methods iterate over values in collection. Remember that in case of dictionaries, iteration goes over
key-value pairs.

### foldLeft and foldRight

Folding is a process of iterating over a values of collection and combining them with a usage of some combining
operation (called "operator" here), starting with some given starting value. Difference between `foldLeft` and
`foldRight` lies in direction of iteration and order of arguments in operator.

As a first example, consider a list of integers: `[5, 4, 8, 17]`. We want to get a sum of all those integers. Because
adding is commutative, order of numbers does not matter, so both `foldLeft` and `foldRight` will give us the same result.

```php
/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);

$sumLeft = $list->foldLeft(0, fn (int $a, int $b) => $a + $b);
$sumRight = $list->foldRight(0, fn (int $a, int $b) => $a + $b);

$sumLeft === 34; // true
$sumRight === 34; // true
```

The first argument in both methods (`0` here) is a starting value. The second argument is an operator - a callable
that is used to combine all elements. From mathematical point of view, in example above, fold methods do a following
operations:
```
foldLeft:
(((0 + 5) + 4) + 8) + 17
foldRight:
5 + (4 + (8 + (17 + 0)))
```

As you can see, order of elements in list is the same. The difference lies in position of starting value and a direction
of adding.

Another example, this time with subtracting, to see a difference in results:

```php
/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);

// equivalent of (((0 - 5) - 4) - 8) - 17
$resultLeft = $list->foldLeft(0, fn (int $a, int $b) => $a - $b);
// equivalent of 5 - (4 - (8 - (17 - 0)))
$resultRight = $list->foldRight(0, fn (int $a, int $b) => $a - $b);

$resultLeft === -34; // true
$resultRight === -8; // true
```

And another one, with different starting value:

```php
/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);

// equivalent of (((15 - 5) - 4) - 8) - 17
$resultLeft = $list->foldLeft(15, fn (int $a, int $b) => $a - $b);
// equivalent of 5 - (4 - (8 - (17 - 15)))
$resultRight = $list->foldRight(15, fn (int $a, int $b) => $a - $b);

$resultLeft === -19; // true
$resultRight === 7; // true
```

#### Empty collections

If collection is empty, folding will still work properly and it will return a starting value:

```php
/** @var ArrayList<int> */
$list = ArrayList::empty();

$result = $list->foldLeft(0, fn (int $a, int $b) => $a + $b);
$result === 0; // true
```

#### Type of result

Because folding requires a starting value and that starting value is used in the beginning of folding process, there is
no problem with applying any type of starting value:
- Result of folding will have the same type as a starting type
- Result of operator call must have the same type as a starting value
- in case of `foldLeft`, first argument of operator must have the same type as a starting value and a second argument
  must have the same type as elements in collection. In case of `foldRight` the order is reversed.

Simple example, with concatenating integers into string:

```php
/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);

$resultLeft = $list->foldLeft('foo', fn (string $a, int $b) => $a.', '.$b);
$resultRight = $list->foldRight('foo', fn (int $a, string $b) => $a.', '.$b);

$resultLeft === 'foo, 5, 4, 8, 17'; // true
$resultRight === '5, 4, 8, 17, foo'; // true
```

And more advanced one:
```php
class IntContainer {
    public function __construct(
        public readonly int $content,
    ) {}
    
    public function subtract(int $anotherValue): self
    {
        return new self($this->content - $anotherValue);    
    }
}

/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);
$startingValue = new IntContainer(0);

/** @var IntContainer $resultLeft */
$resultLeft = $list->foldLeft($startingValue, fn (IntContainer $a, int $b) => $a->subtract($b));
$resultLeft->content === -34; // true
```

### reduceLeft and reduceRight

Reducing can be treated as a simpler form of folding that changes a few things:
- there is no supplied starting value
- result type will be equal to type of elements in a collection

Example with subtracting to show a direction of results:

```php
/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);

// equivalent of ((5 - 4) - 8) - 17
$resultLeft = $list->reduceLeft(fn (int $a, int $b) => $a - $b);
// equivalent of 5 - (4 - (8 - 17))
$resultRight = $list->reduceRight(fn (int $a, int $b) => $a - $b);

$resultLeft === -24; // true
$resultRight === -8; // true
```

If collection has only one element, that element will be returned as a result. If collection is empty, reducing **will
throw** `UnsupportedTraversalException`.

### reduceLeftOption and reduceRightOption

Those two methods work exactly like `reduceLeft` and `reduceRight`, but instead of returning a value, they return an
optional containing a value.

```php
/** @var ArrayList<int> */
$list = ArrayList::with([5, 4, 8, 17]);

/** @var Optional<int> $resultLeft */
$resultLeft = $list->reduceLeftOption(fn (int $a, int $b) => $a - $b);
/** @var Optional<int> $resultRight */
$resultRight = $list->reduceRightOption(fn (int $a, int $b) => $a - $b);

$resultLeft->nonEmpty(); // true
$resultLeft->get() === -24; // true
$resultRight->nonEmpty(); // true
$resultRight->get() === -8; // true
```

It can be used to safely handle a situation when a collection is empty: simple `reduceX` will throw an exception,
while `reduceXOption` will return an empty optional:

```php
/** @var ArrayList<int> */
$list = ArrayList::empty();

// this will throw UnsupportedTraversalException:
$list->reduceLeft(fn (int $a, int $b) => $a - $b);

// but this will return an empty optional:
/** @var Optional<int> $optional */
$optional = $list->reduceLeftOption(fn (int $a, int $b) => $a - $b);
$optional->isEmpty(); // true
```