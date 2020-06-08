<center>
<img src="./assets/images/logo.svg" style="width: 1.5em; height: 1.5em"/>

reciphpes!
==
</center>

## Installation

### Build

```shell
make build
```

### Run (Docker)

Create a dedicated folder to hold `docker-compose.yml` and the data directory.

> Instructions for Debian-like distros where `www-data` user exists (UID = 33).

```shell
mkdir -p /opt/reciphpes/data/db /opt/reciphpes/data/log
chgrp -R www-data /opt/reciphpes/data/*
chmod -R g+w /opt/reciphpes/data/*
```

Example of `docker-compose.yml`:
```yml
version: '3.2'

services:
  app:
    image: nanawel/reciphpes
    container_name: reciphpes
    restart: always
    ports:
      - '8000:80'
    volumes:
      - './data/db:/var/www/webapp/var/db:rw'
      - './data/log:/var/www/webapp/var/log:rw'
```

Start the container with
```shell
docker-compose up -d
```

Then init the database (first time only):
```
docker-compose exec app make install
```

## Developer Notes

### Docker setup (recommended)

Copy `.env` to `.env.local` and adjust values to your environment.
You might particularly want to change `WEBAPP_UID` to your own UID
(to prevent permission issues with your files and the ones created
by Apache in the container).

```shell
make dev-build
make dev-startd HTTP_PORT=8080    # Choose any free port
```

You may now enter the container with `make shell` and issue the
commands provided in the following section.

### Local setup

> **Requirements**
> - Apache 2 with PHP 7.1+
> - Composer
> - NodeJS 10+
> - yarn

```shell
composer install
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load --append

yarn install
# Start assets builder watch task
yarn run encore dev --watch
```
