DC=docker compose
PHP=$(DC) exec php

.PHONY: up down install reset fixtures test logs shell db audit

up:
	$(DC) up -d --build

down:
	$(DC) down

install:
	$(DC) run --rm php composer install
	$(PHP) php bin/console doctrine:database:create --if-not-exists
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction

reset:
	$(PHP) php bin/console doctrine:database:drop --force --if-exists
	$(PHP) php bin/console doctrine:database:create --if-not-exists
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction

fixtures:
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction

test:
	$(PHP) php vendor/bin/phpunit

logs:
	$(DC) logs -f --tail=100

shell:
	$(PHP) sh

db:
	$(DC) exec database psql -U app -d eventsecure

audit:
	$(DC) run --rm php composer audit
