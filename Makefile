up:
	docker-compose run php composer install

phpunit:
	docker-compose run php vendor/bin/phpunit

phpstan:
	docker-compose run php vendor/bin/phpstan analyze