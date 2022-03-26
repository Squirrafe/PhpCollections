## PHP Collections Library

[![Tests](https://shields.io/github/workflow/status/Squirrafe/PhpCollections/Tests?label=Tests)](https://github.com/Squirrafe/PhpCollections/actions/workflows/workflow.yaml)
[![License](https://shields.io/github/license/Squirrafe/PhpCollections)](https://github.com/Squirrafe/PhpCollections/blob/main/LICENSE)
[![Current version](https://shields.io/packagist/v/squirrafe/collections)](https://packagist.org/packages/squirrafe/collections)
![PHP Version](https://shields.io/packagist/php-v/squirrafe/collections)

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
