.PHONY: install reset-db fixtures test audit serve

install:
	docker compose up --build -d

reset-db:
	docker compose down -v
	docker compose up --build -d

fixtures:
	docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

test:
	docker compose exec app php bin/console lint:container
	docker compose exec app php bin/console lint:twig templates
	docker compose exec app php bin/console doctrine:schema:validate

audit:
	docker compose exec app composer audit

serve:
	docker compose up
