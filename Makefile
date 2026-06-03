DC=docker compose
PHP=$(DC) exec php

.PHONY: up down install reset fixtures demo-data accounts test logs shell db audit

up:
	$(DC) up -d --build

down:
	$(DC) down

install:
	$(DC) run --rm php composer install
	$(MAKE) demo-data

reset:
	$(PHP) php bin/console doctrine:database:drop --force --if-exists
	$(MAKE) demo-data

fixtures:
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction

demo-data:
	$(PHP) php bin/console doctrine:database:create --if-not-exists
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction
	$(MAKE) accounts

accounts:
	@printf "\nComptes de test charges par les fixtures:\n"
	@printf "  user1@example.test / password\n"
	@printf "  user2@example.test / password\n"
	@printf "  admin@example.test / password\n\n"

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
