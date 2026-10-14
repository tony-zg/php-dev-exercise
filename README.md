# CakePHP local development

Open **http://localhost:8080** after setup. This is a development environment with debug enabled, bound to localhost.

## Prerequisites and setup

Install Docker Desktop (running, Linux containers) or Docker Engine with Docker Compose v2. A POSIX shell and OpenSSL are needed for the setup script; host PHP, Composer and MySQL are not required.

```sh
./scripts/setup.sh
```

The script creates `.env` only if absent, generates random credentials, uses your host UID/GID, builds PHP, installs the committed Composer lock file, prepares writable directories and waits for services. Existing configuration and database data are preserved. Commit `app/composer.lock` alongside the source.

## Dinggo API configuration

Add `DINGGO_USERNAME` and `DINGGO_KEY` from your supplied credentials to the root `.env` file. Set `DINGGO_API_BASE_URL=https://app.dev.aws.dinggo.com.au/phptest`. See `.env.example` for placeholders; never commit real credentials.

Run `docker compose up -d` after changing these values so Compose passes them into the app container. Application code can read `Configure::read('Dinggo.baseUrl')`, `Configure::read('Dinggo.username')` and `Configure::read('Dinggo.key')` through `Cake\Core\Configure`. Do not display or log the credentials. This configures access only; importing cars and quotes is implemented separately.

## Daily commands

```sh
docker compose up -d --wait          # Start
docker compose stop                 # Stop, retain containers/data
docker compose down                 # Remove containers/network, retain database
docker compose up -d --build --wait # Rebuild after Dockerfile changes
docker compose exec app composer install
docker compose exec app php bin/cake.php --help
docker compose exec app php bin/cake.php migrations migrate
docker compose exec app php bin/cake.php migrations status
docker compose exec app vendor/bin/phpunit
docker compose logs --tail=100 app nginx db
```

Tests use a separate SQLite database, never the development MySQL database. Migrations are available through the included CakePHP migrations plugin; no application tables are needed for the welcome page.

## Configuration and data

- `app/`: bind-mounted CakePHP source; changes appear immediately.
- `app/composer.json` and `app/composer.lock`: application and exact dependency versions.
- `.env`: local credentials, port, `CAKE_DEBUG` setting and host UID/GID; ignored by Git. `.env.example` documents every setting. Never commit real credentials.
- `app/config/app.php`: shared settings; `app/config/docker.php`: environment-based database configuration. `app/config/app_local.php` is ignored.
- `docker/php/`: PHP image and development settings. `docker/nginx/default.conf`: webroot and front-controller routing.
- `app/tmp/` and `app/logs/`: local writable cache/logs, excluded from Git. PHP-FPM and CLI run as the host user to avoid root-owned files.
- Compose volume `mysql_data` (project-prefixed by Docker): persistent MySQL data. MySQL uses service hostname `db:3306` and is not published to the host.

Compose waits for an authenticated MySQL health check before starting PHP. Changing MySQL passwords in `.env` does not change users in an existing database: update the database user explicitly or restore matching credentials. Changing environment values requires `docker compose up -d` to recreate affected containers.

## Troubleshooting

- **Port occupied:** change `APP_PORT` in `.env`, then `docker compose up -d`; use that port in your browser.
- **Docker unavailable:** start Docker Desktop and check `docker info` / `docker compose version`.
- **Permission denied:** ensure `LOCAL_UID`/`LOCAL_GID` in `.env` match `id -u`/`id -g`. Run `chmod -R u+rwX app/tmp app/logs` and recreate containers. For files created previously by root, repair only these directories with `sudo chown -R "$(id -u):$(id -g)" app/tmp app/logs`. Do not use world-writable permissions.
- **Database unhealthy:** inspect `docker compose logs db`; initialization can take a minute. Check credentials against those used when the volume was created.
- **Missing vendor files:** run `docker compose run --rm --no-deps app composer install`.

**DESTRUCTIVE — permanently delete this Compose project's database data (back up first):**

```sh
docker compose down --volumes
docker compose up -d --wait
```

This reset was not run during setup. Ordinary stop/down commands preserve data.

## Official references

- [CakePHP latest stable release](https://github.com/cakephp/cakephp/releases/latest)
- [CakePHP installation, PHP/extensions/database requirements and Nginx routing](https://book.cakephp.org/5.x/installation.html)
- [CakePHP environment-based configuration](https://book.cakephp.org/5.x/development/configuration.html)
- [Compose database readiness and startup order](https://docs.docker.com/compose/how-tos/startup-order/)

## Installed versions and verification

Installed on 14 October 2026: CakePHP **5.4.3** (app skeleton 5.4.0), PHP **8.4.13**, Composer **2.8.12**, Nginx **1.28.0**, MySQL **8.4.6**. All base images have explicit version tags; dependencies are pinned by `app/composer.lock`. CakePHP requires PHP 8.2+, intl, mbstring, PDO and SimpleXML, and supports MySQL 5.7+. This image also supplies pdo_mysql, SQLite for tests/DebugKit, XML/DOM for PHPUnit and ZIP for Composer.

[Official CakePHP 5.4.3 release](https://github.com/cakephp/cakephp/releases/tag/5.4.3).

```sh
curl -f http://localhost:8080/
docker compose exec app php config/verify-database.php
docker compose exec app composer check-platform-reqs
docker compose exec app vendor/bin/phpunit
```

Verified: HTTP 200 for `/` and `/pages/home`; welcome-page extension, writable-directory and database checks passed; direct CakePHP MySQL query passed; Composer platform checks passed; PHPUnit passed 9 tests / 23 assertions; migrations CLI works (no migrations yet). No remaining blockers. Initial HTTP checks exposed a host `DEBUG` variable collision, corrected by using `CAKE_DEBUG`. Earlier diagnostic errors remain in local logs; subsequent HTTP requests produced no new application errors. MySQL emits standard image initialization warnings (self-signed TLS, PID directory and timezone files); initialization completed successfully.
