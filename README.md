## PHP Collections Library

Collections library for PHP, inspired by Scala.

### Features

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