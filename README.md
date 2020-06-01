<center>
<img src="./assets/images/logo.svg" style="width: 1.5em; height: 1.5em"/>

reciphpes!
==
</center>

## Installation

```shell
bin/console doctrine:database:create

# Optional, if migration scripts do not already exist in src/Migrations/
bin/console make:migrations

bin/console doctrine:migrations:migrate

```
