#!/bin/sh

set -eu

composer install --no-interaction --prefer-dist
php bin/console doctrine:migrations:migrate --no-interaction

if ! MYSQL_PWD="$MYSQL_PASSWORD" mysql \
    --host="$MYSQL_HOST" \
    --user="$MYSQL_USER" \
    --database="$MYSQL_DATABASE" \
    --ssl=false \
    --batch \
    --skip-column-names \
    --execute='SELECT email FROM `user` WHERE email = "admin@example.com"' \
    | grep -q 'admin@example.com'; then
    php bin/console doctrine:fixtures:load --no-interaction
fi

exec php -S 0.0.0.0:8000 -t public
