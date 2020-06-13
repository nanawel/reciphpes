<center>
<img src="./assets/images/logo.svg" style="width: 1.5em; height: 1.5em"/>

reciphpes!
==
</center>

> Author: Anaël Ollier <nanawel+reciphpes@gmail.com>

## Screenshots

- [Recipes list](docs/screenshots/recipe-grid.png)
- [Recipe page](docs/screenshots/recipe-show.png)
- [Recipe form](docs/screenshots/recipe-form.png)
- [Recipe mass create form](docs/screenshots/recipe-masscreate.png)
- [Search results](docs/screenshots/search-results.png)

## Installation

> **Notice:** Currently the application **cannot be accessed** from a URL using
> a custom path:
>
> - :ballot_box_with_check: http://reciphpes.myhost.org/
> - :x: http://something.myhost.org/reciphpes

### Run (Docker)

Create a dedicated folder (here `/opt/reciphpes`) to hold `docker-compose.yml`
and the data directory and give the latter required permissions for `www-data`
in the container.

```shell
mkdir -p /opt/reciphpes/data/db /opt/reciphpes/data/log
chgrp -R 33 /opt/reciphpes/data/*
chmod -R g+w /opt/reciphpes/data/*
```

Example of `docker-compose.yml` (here listening on port `8000`):
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
cd /opt/reciphpes
docker-compose up -d
```

Then init the database (first time only):
```shell
docker-compose exec -u www-data app make install
```

*(Optional)* You might want to generate a new secret value:
```shell
docker-compose exec app make new-secret
```

You may now access the application at http://localhost:8000/.

See next section when upgrading.

### Upgrade (Docker)

```
docker-compose pull
gzip -c data/db/app.db > data/db/app.$(date +%F_%H-%M-%S).sqlite.gz
docker-compose up -d
docker-compose exec -u www-data app bin/console doctrine:migrations:migrate -n
docker-compose exec app make new-secret
```

### Build from source

```shell
git clone https://github.com/nanawel/reciphpes.git /opt/reciphpes-src
cd /opt/reciphpes-src
make build
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
commands provided in the section below.

### Local setup

> **Requirements**
> - Apache 2 with PHP 7.1+
> - Composer
> - NodeJS 10+
> - yarn

```shell
make install

# Start assets builder watch task
yarn run encore dev --watch
```

# Licence

See [LICENSE](LICENSE).
