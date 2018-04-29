install:
	docker-compose -f ./docker-compose.yml up -d
	sleep 10
	docker-compose -f ./docker-compose.yml exec php composer install
	-docker-compose -f ./docker-compose.yml exec php php bin/console doctrine:migrations:migrate -n --no-interaction --env=dev
	-docker-compose -f ./docker-compose.yml exec php php bin/console doctrine:fixtures:load -n --no-interaction --env=dev

migrate_diff:
	docker-compose -f ./docker-compose.yml exec php php bin/console doctrine:migrations:diff --env=dev

migrate_execute:
	docker-compose -f ./docker-compose.yml exec php php bin/console doctrine:migrations:migrate  --env=dev

load_fixtures:
	docker-compose -f ./docker-compose.yml exec php php bin/console doctrine:fixtures:load --env=dev

start:
	docker-compose -f ./docker-compose.yml up -d

stop:
	docker-compose -f ./docker-compose.yml down

php_cli:
	docker-compose -f ./docker-compose.yml run php bash

req:
	docker-compose -f ./docker-compose.yml exec php php -d memory_limit=2G /usr/local/bin/composer req --dev phpunit/phpunit