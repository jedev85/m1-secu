.PHONY: install reset-db fixtures test audit serve

install:
	composer install
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction

reset-db:
	rm -f var/auditlab.db
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction

fixtures:
	php bin/console doctrine:fixtures:load --no-interaction

test:
	php bin/console lint:container
	php bin/console lint:twig templates
	php bin/console doctrine:schema:validate

audit:
	composer audit

serve:
	symfony server:start
