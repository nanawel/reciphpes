export HTTP_PORT ?= 80

dev-%: export COMPOSE_FILE = docker-compose.dev.yml
dev-%:
	$(MAKE) $*

bash:
	$(MAKE) shell

shell:
	docker-compose exec -u www-data app bash

shell-root:
	docker-compose exec app bash

pull:
	docker-compose pull

build:
	docker-compose build

start:
	docker-compose up

startd:
	docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down --remove-orphans

logs:
	docker-compose logs -f --tail=50

ps:
	docker-compose ps

install:
	bin/console doctrine:migrations:migrate --no-interaction
	bin/console doctrine:fixtures:load --append

dev-install:
	docker-compose exec app sh -c \
		'composer install \
		&& bin/console doctrine:database:create \
		&& bin/console make:migration \
		&& bin/console doctrine:migrations:migrate \
		&& bin/console doctrine:fixtures:load --append
		&& yarn install'

dev-encore-watch:
	test -f /.dockerenv && yarn run encore dev --watch || docker-compose exec app yarn run encore dev --watch

new-secret:
	sed -i "s/APP_SECRET=.*/APP_SECRET=$$(openssl rand -base64 32 | md5sum | awk '{ print $$1 }')/" .env
