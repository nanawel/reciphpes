
bash:
	$(MAKE) shell

shell:
	docker-compose exec -u www-data symfony bash

pull:
	docker-compose pull

start:
	docker-compose up -d

startd:
	docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down --remove-orphans
