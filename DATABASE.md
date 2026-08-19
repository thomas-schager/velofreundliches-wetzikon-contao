# Local database (MariaDB via Docker)

This project's local MariaDB runs in a Docker container, not installed natively. There's no
`docker-compose.yml` in this repo — the container was created by hand with a single `docker run`
(see "First-time setup" below if you ever need to recreate it). This doc covers day-to-day
starting/stopping and what to do when something's already using the port.

## Prerequisites

- **Docker CLI** (`docker`).
- A Docker **runtime**. This project's dev machine uses **[Colima](https://github.com/abiosoft/colima)**
  (`brew install colima docker`) rather than Docker Desktop — a lightweight VM that provides the
  Docker daemon on macOS. Docker Desktop works too if you have it instead; skip the `colima`
  commands below and use Docker Desktop's own start/stop.

## Quick start (container already exists)

```bash
colima start                              # starts the VM + Docker daemon (skip if using Docker Desktop)
docker ps -a --filter "name=velowetzikon" # see what's there and its current state
docker start velowetzikon-mariadb         # start the DB container (no-op if already running)
```

`colima start` can take 10-30 seconds the first time after a reboot. Once it's done, `docker ps -a`
should show a container named **`velowetzikon-mariadb`** (image `mariadb:lts`). If its `STATUS`
already says `Up ...`, you're done — skip the `docker start`.

**Verify it's actually reachable:**

```bash
docker exec velowetzikon-mariadb mariadb -uroot -pgeheim -e "SHOW DATABASES;"
```

(Use the `mariadb` client binary, not `mysql` — recent `mariadb:lts` images don't ship the
`mysql` binary/symlink anymore.) You should see a `velowetzikon` database in the list. Credentials
match `DATABASE_URL` in `.env.local` (`root` / `geheim` — a local-only dev password, never used
anywhere real).

Once it's up, run the app as usual (see `README.md` → *Local setup*):

```bash
php bin/console contao:migrate --no-interaction   # only needed after schema/version changes
php -S localhost:8000 -t public/
```

## Stopping it

```bash
docker stop velowetzikon-mariadb   # stops the DB, keeps its data
colima stop                        # optional: also shuts down the VM (frees CPU/RAM)
```

`docker stop` is enough for a normal end-of-day stop — the container's data lives in a Docker
**volume**, not in the container itself, so stopping (or even removing) the container doesn't
lose data as long as the volume isn't also removed (see the warning below).

## First-time setup (no container exists yet)

If `docker ps -a` shows no `velowetzikon-mariadb` at all — a fresh machine, or the container was
deleted — recreate it. This reproduces the exact setup already in use (confirmed via
`docker inspect` against the live container):

```bash
docker run -d \
  --name velowetzikon-mariadb \
  -p 3306:3306 \
  -e MYSQL_ROOT_PASSWORD=geheim \
  -e MYSQL_DATABASE=velowetzikon \
  -e MYSQL_CHARSET=utf8mb4 \
  -e MYSQL_COLLATION=utf8mb4_unicode_ci \
  -v velowetzikon-mariadb-data:/var/lib/mysql \
  mariadb:lts
```

Note the named volume (`velowetzikon-mariadb-data`) — give it an explicit name like this so it's
easy to find and reuse later (the original container's volume is an auto-generated hash name,
which works but is harder to identify by hand). This starts an **empty** database; Contao needs
to build its schema and you'll want an admin user:

```bash
php bin/console contao:migrate --no-interaction
php bin/console contao:user:create
```

## Troubleshooting

**`colima is not running` / commands hang or fail to connect to the Docker socket.**
Run `colima start` first — everything else in this doc assumes the daemon is up.
`colima status` tells you the current state without starting anything.

**Something else is already using port 3306 / the app can't connect / wrong data shows up.**
More than one MariaDB/MySQL container can exist on this machine. Check what's actually running
and what's competing for the port:

```bash
docker ps -a
```

Only one container can hold host port `3306` at a time — if a *different* container shows
`0.0.0.0:3306->3306/tcp` while `velowetzikon-mariadb` is `Exited`, stop the other one first:

```bash
docker stop <other-container-name>
docker start velowetzikon-mariadb
```

(This has happened before: an empty, unrelated `velowetzikon-mysql` container — `mysql:latest`,
no `velowetzikon` database in it — was found holding port 3306 while the real
`velowetzikon-mariadb` sat stopped. If you see a container you don't recognize, inspect it before
assuming it's safe to remove: `docker exec <name> mariadb -uroot -p<pw> -e "SHOW DATABASES;"` —
or the `mysql` client if that's the image — to see whether it actually holds your data.)

**Don't casually `docker rm -v` or `docker volume prune`.**
`docker rm velowetzikon-mariadb` alone is recoverable (the volume survives; recreate the
container and reattach the same volume). Adding `-v`, or running `docker volume prune`, deletes
the volume too — that's the actual site database, not a cache. If you're ever unsure, `docker
inspect velowetzikon-mariadb --format '{{json .Mounts}}'` shows exactly which volume holds the
data before you do anything destructive.

**Where the connection settings come from.**
`.env.local`'s `DATABASE_URL` (`mysql://root:geheim@127.0.0.1:3306/velowetzikon?serverVersion=mariadb-12.3.2`)
is what Contao/Symfony actually reads — this doc's `docker run`/`docker exec` commands just need
to match host (`127.0.0.1`), port (`3306`), user/password, and database name from that URL. If
you ever change one, change both.
