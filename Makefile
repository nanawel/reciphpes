
bash:
	$(MAKE) shell

shell:
	docker-compose exec -u www-data symfony bash

shell-root:
	docker-compose exec symfony bash

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

install:
	docker-compose exec symfony sh -c \
		'composer install \
		&& bin/console doctrine:database:create \
		&& bin/console make:migration \
		&& bin/console doctrine:migrations:migrate \
		&& bin/console doctrine:fixtures:load --append'

encore-watch:
	yarn run encore dev --watch

encore-build:
	yarn encore production
