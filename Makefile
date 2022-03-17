up:
	docker-compose run --rm php composer install

phpunit:
	docker-compose run --rm php vendor/bin/phpunit

phpstan:
	docker-compose run --rm php vendor/bin/phpstan analyze