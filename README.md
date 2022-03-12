## PHP Collections Library

Collections library for PHP, inspired by Scala.

### Development

Library uses Docker Compose during development.

To install dependencies:
```shell
docker-compose run php composer install
```

To run tests:

```shell
docker-compose run php vendor/bin/phpunit
```