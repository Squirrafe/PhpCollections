## PHP Collections Library

Collections library for PHP, inspired by Scala.

Install with Composer:

```shell
composer require squirrafe/collections
```

See documentation at [GitHub Pages](https://squirrafe.github.io/PhpCollections/)

#### Development

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
