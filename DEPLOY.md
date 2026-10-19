# Deployment Guide — VeloWetzikon Contao

How this Contao 5.7 installation gets deployed to Hostpoint, over SSH. Three environments exist:
**local** (this machine, unstable by definition), **test**, and **production** — test and
production both live on Hostpoint under the same SSH account, in fixed, separate directories.

**This file deliberately uses placeholders (`<ssh_user>`, `<test_base_path>`, etc.) instead of
real account details.** The real SSH host/user, absolute server paths, and database names belong
in `deploy/deploy.env` (gitignored, never committed) — this file is git-tracked, so it stays
generic on purpose. Don't paste real values back into it.

This app is deployed separately from `VeloWetzikon_Backend`, which has its own `DEPLOY.md`. The
deploy system here mirrors that one closely, adapted for Contao's specifics (see §10).

> **Current phase: no test environment yet, local is temporarily the master.** Production is
> reachable for now at a temporary Hostpoint-assigned domain (e.g. `lizabize.myhostpoint.ch`)
> instead of `web.velofreundliches-wetzikon.ch` — purely a Hostpoint panel DNS/document-root
> detail, see §10.4, nothing script-related changes because of it. More importantly: **test does
> not exist yet**, and until it does, local's database and `files/div/` are the real source of
> truth, not production's — the reverse of this system's normal direction (§10.5). `deploy.sh
> test`, `promote.sh`, and `refresh-test-from-prod.sh` all refuse to run with a clear error while
> `REMOTE_BASE_PATH_TEST` is unset in `deploy.env`. Use `deploy.sh production` to ship code/schema
> and the new `push-local-to-production.sh` (§6a) to push local's data up. Once a real test
> environment exists and production has been seeded with real, live-edited content, retire
> `push-local-to-production.sh` and switch back to the normal local → test → production flow
> described below, where production/test are the masters and nothing ever pushes local data up.

---

## Overview

**No symlinks, no release history.** Each environment is one fixed, real directory (the project
root directly — no `app/` nesting), with `public/` configured directly as the document root in
Hostpoint's panel. Deploys `rsync` straight into that directory, in place.

**The workflow:**
1. `deploy.sh test` — refreshes test's database and `files/div/` from production first, *then*
   deploys the current local code and runs migrations against test.
2. You validate by hand on `https://test-web.velofreundliches-wetzikon.ch`.
3. Found a problem? Fix it locally, run `deploy.sh test` again — it always starts by
   re-refreshing from production, so every attempt begins from the same real baseline.
4. Looks good? `promote.sh` copies test's exact, already-validated files into production — not a
   second local build, so production gets exactly what was tested.

**Safety nets, applied identically on both environments, every time:**
- Every script aborts immediately if `REMOTE_BASE_PATH_TEST` and `REMOTE_BASE_PATH_PRODUCTION`
  in `deploy.env` are ever identical (`check_base_paths_distinct`) — otherwise a copy-paste
  mistake there would make every "safe" testing operation silently act on production instead.
- Before touching any database, the script verifies the `DATABASE_URL` actually found in that
  environment's live `.env.local` names the exact database `deploy.env` expects for it
  (`DB_NAME_TEST` / `DB_NAME_PRODUCTION`, via `check_db_name`) — refuses to proceed otherwise.
- A `mysqldump` backup is taken immediately before every migration run.
- Pending migrations are dry-run first (`contao:migrate --dry-run`); if the SQL contains
  `DROP COLUMN`, `DROP TABLE`, or `TRUNCATE`, you must type `DESTROY` to proceed. A heuristic, not
  a guarantee.
- Maintenance mode (Contao's own `contao:maintenance-mode` command, see §10.1) wraps the risky
  window on whichever environment is being touched. Clears automatically on success; left **on**
  on failure, with the exact command to lift it printed to the terminal.
- Anything that touches **production** — `deploy.sh production`, `promote.sh` — requires typing
  the word `production` to proceed.
- After every code sync and migration, `contao:symlinks` and `contao:filesync` run to regenerate
  `public/`'s symlinks and reconcile Contao's virtual filesystem (DBAFS) against whatever's
  actually on disk — see §10.2.

**Rollback is git, not a script.** There's no release history to flip back to. If a promoted
release breaks production: fix it locally (or `git checkout` an older commit) and redeploy the
same way. Database-side, restore from the pre-migration backup if needed (see §8).

---

## 1. Prerequisites

### Local machine
- PHP ≥ 8.1 (currently 8.5), Composer ≥ 2, `rsync`, `ssh`
- SSH key-based access to Hostpoint, working non-interactively
- Local MariaDB for the `local` environment itself

### Hostpoint
- PHP version pinned per domain in Hostpoint's panel (not yet set — see §3)
- MariaDB (MySQL 9.x is **not** compatible — reserved keyword conflicts; use MariaDB or MySQL 8.4
  LTS), `rsync` and `mysqldump`/`mysql` reachable over SSH

---

## 2. One-time local setup

```bash
cd deploy
cp deploy.env.example deploy.env
```

Fill in `SSH_HOST`/`SSH_USER`/`SSH_PORT`, `REMOTE_BASE_PATH_TEST`, `REMOTE_BASE_PATH_PRODUCTION`,
`DB_NAME_TEST`/`DB_NAME_PRODUCTION`, and `PHP_VERSION`. `deploy.env` is gitignored — never commit
it. See §10.3 for why `PHP_VERSION` is checked against the *live* server file rather than a local
one, unlike the Backend project.

---

## 3. One-time server setup

**Only production needs setting up right now** — there is no test environment yet (see the
callout near the top of this file). Skip anything below that mentions test until one is created;
`deploy.sh test`/`promote.sh`/`refresh-test-from-prod.sh` will refuse to run with a clear error
in the meantime.

1. In Hostpoint's control panel, create the subdomain (for now, the Hostpoint-assigned temporary
   domain, e.g. `lizabize.myhostpoint.ch`; later `web.velofreundliches-wetzikon.ch` and
   eventually the apex — see §10.4) with its document root pointed at `<base_path>/public`.
2. Set that domain's PHP version in the panel. This is what writes the
   `Use php-fpm <version>` line Hostpoint prepends to the live `public/.htaccess` — the thing
   `deploy.env`'s `PHP_VERSION` gets checked against (§10.3). Nothing else needs doing for this.
3. Create a MariaDB database + user for the environment.
4. `.env.local` holds secrets, so it's created once by hand, not generated by any script:
   ```bash
   # write deploy/env-local/testing.env.local and production.env.local locally first (gitignored), then:
   scp deploy/env-local/testing.env.local    <ssh_user>@<ssh_host>:<test_base_path>/.env.local
   scp deploy/env-local/production.env.local <ssh_user>@<ssh_host>:<production_base_path>/.env.local
   ```
   See §12 for the variables each needs. Both databases start empty — the first `deploy.sh`/
   `promote.sh` run against each builds the whole schema from scratch via `contao:migrate`.
5. `public/.htaccess` is **not** shipped by any deploy (§10.3) — upload one real copy once, so
   there's something for Hostpoint's panel to prepend its PHP-version header to:
   ```bash
   # after a local `composer install` has generated public/.htaccess:
   scp public/.htaccess <ssh_user>@<ssh_host>:<base_path>/public/.htaccess
   ```

---

## 4. Deploying to test

```bash
./deploy/deploy.sh test
```

In order: check `.env.local` exists at the target (aborts with a pointer back to §3 if not) →
check the live `public/.htaccess`'s PHP version matches `deploy.env` → check its database name
matches what's expected → refresh test's DB/`files/div/` from production
(`refresh-test-from-prod.sh`) → `composer install --no-dev` locally (this also regenerates
`public/` and `assets/` via Contao's own `contao-setup` hook) → `rsync` the code into test's
directory → enter maintenance mode → warm cache → back up test's DB → dry-run + check pending
migrations → run migrations → `contao:symlinks` → `contao:filesync` → warm cache again →
maintenance mode off.

No confirmation prompt for test itself (it's meant to be overwritten every time), unless a
pending migration looks destructive, in which case it stops and asks for `DESTROY` regardless of
environment.

**`./deploy/deploy.sh test --no-refresh`** skips the refresh step, leaving test's database and
`files/div/` exactly as they are instead of overwriting them from production. Everything else
still runs as normal. Production is unaffected either way — it never auto-refreshes from
anything, and the flag is rejected if you pass it there.

**First deploy only:** migrations create the schema but no backend user. Create one by hand, once,
over SSH:
```bash
ssh <ssh_user>@<ssh_host> "cd <test_base_path> && <remote_php_bin> bin/console contao:user:create"
```
Interactive — prompts for username/password/email/admin flag. Safe to run again for a second
account.

The code `rsync` excludes `.git`, `.env.local`, `var/`, `public/.htaccess` (§10.3), and
`deploy/` itself, so `deploy.env` and the generated `.env.local` files never leave your machine.
`files/div/*` is excluded the same way (real user-uploaded content, production is the master —
§10.5) except its own `.public` marker file, needed for the folder to stay publicly symlinked —
carried across via an explicit `--include`/`--exclude` pair, same in `promote.sh`.

---

## 5. Promoting to production

```bash
./deploy/promote.sh
```

Checks test has actually been deployed, checks the live `public/.htaccess` PHP version, verifies
*both* test's and production's `.env.local` name their expected database, then asks you to type
`production` to continue. Then: server-side `rsync` of test's directory straight into
production's (same excludes as §4 — no rebuild, no re-upload from your machine) → maintenance
mode → warm cache → back up production's DB → dry-run + destructive migration check → run
migrations → `contao:symlinks` → `contao:filesync` → warm cache again → maintenance mode off.

**First deploy only:** same as test (§4) — create production's own admin account, with its own
password:
```bash
ssh <ssh_user>@<ssh_host> "cd <production_base_path> && <remote_php_bin> bin/console contao:user:create"
```

Production's initial page structure/content is **not** seeded by any script — build it out
directly in the live Contao backend after the first deploy, same as any fresh Contao install.

---

## 6. Manually refreshing test (without deploying code)

```bash
./deploy/refresh-test-from-prod.sh
```

Same thing `deploy.sh test` does as its first step, callable on its own. Checks the two base
paths aren't identical, that both environments' `.env.local` exist and name their expected
database, then: drops every table currently in test's database
(`information_schema.tables`-driven, not `DROP DATABASE` — a shared-hosting DB user commonly
can't do that), `mysqldump` piped directly into test's database, `rsync` of `files/div/` between
the two directories, then `contao:filesync` on test to reconcile its virtual filesystem against
the refreshed content. No confirmation prompt — this only ever writes to test's side.

---

## 6a. Pushing local's data to production (temporary phase only)

```bash
./deploy/push-local-to-production.sh
```

**Only exists for the current temporary phase** (see the callout near the top of this file). It
does the opposite of what this system normally does: overwrites **production's** database and
`files/div/` with **local's** — because right now local is the real source of truth and there is
no test environment to validate through first.

It does **not** touch code. Run `./deploy/deploy.sh production` first (or afterwards — order
between the two doesn't matter) to ship code and run migrations; run this script to push the
actual content.

In order: checks production's `.env.local` exists and points at the expected database → asks you
to type `production` (this is a full overwrite, not a merge) → enters maintenance mode → backs up
production's current database (`remote/backup-db.sh`, same as every other backup in this system)
→ drops every table in production's database and streams a fresh `mysqldump` of local straight
into it over one SSH pipe (`remote/restore-db.sh` — the reverse of `remote/dump-db.sh`) → `rsync`s
local's `files/div/` up into production, deleting anything production has that local doesn't →
`contao:filesync` on production to reconcile its virtual filesystem against the newly-pushed
`files/div/` → leaves maintenance mode.

No `--no-refresh`-style opt-out — if you don't want production's data overwritten, don't run this
script. There's also no equivalent for pushing to `test`, since there is currently no test
environment; once one exists, stop using this script entirely (see the callout above).

---

## 7. Refreshing local from test or production

```bash
./deploy/refresh-local.sh production
# or
./deploy/refresh-local.sh test
```

No Backend-project equivalent — that system never pulls data down to a developer's machine, only
test can refresh from production. This is the opposite direction: your local database and
`files/div/` get overwritten from whichever source you name.

Backs up local's current database first (`var/backups/`, gitignored), drops all local tables,
streams the source's `mysqldump` straight into local MariaDB over one SSH pipe, pulls
`files/div/` down via `rsync`, then runs `contao:filesync` locally. No maintenance mode (local
isn't a public deploy target) and no `production`-style confirmation gate, since nothing remote
is ever modified by this script.

---

## 8. Database migrations

Contao's own migration system (`contao:migrate`) covers both Doctrine-style schema migrations and
Contao's built-in upgrade migrations in one command — no separate `doctrine:migrations:*` tooling.

Two layers of protection, both automatic, on every deploy/promote:
1. **Backup first.** `mysqldump --single-transaction --no-tablespaces`, gzipped, into
   `<base_path>/db-backups/`. Not pruned automatically.
2. **Destructive-pattern guard.** `contao:migrate --dry-run` runs first; if the SQL it would
   execute contains `DROP COLUMN`, `DROP TABLE`, or `TRUNCATE`, you must type `DESTROY` before
   the real migration runs.

To check what's actually pending on either environment at any time:
```bash
ssh <ssh_user>@<ssh_host> "cd <base_path> && <remote_php_bin> bin/console contao:migrate --dry-run"
```

---

## 9. Recovering from a failed deploy

If `deploy.sh`/`promote.sh` fails partway through *after* maintenance mode was entered, it's left
**on** deliberately — the script prints the exact command to lift it:
```bash
ssh -p 22 <ssh_user>@<ssh_host> "cd <base_path> && <remote_php_bin> bin/console contao:maintenance-mode disable"
```
Fix the actual problem first — lifting maintenance mode just makes whatever's there visible
again, it doesn't fix anything.

**Database rollback:** restore the pre-migration backup from `<base_path>/db-backups/`.
**Code rollback:** there's no release history — fix locally or `git checkout` an older commit,
then redeploy the normal way.

---

## 10. Known issues and gotchas

### 10.1 Native maintenance mode, not a file-rename trick

Unlike the Backend project (a plain Symfony app that swaps `maintenance.html`/`.html.off`
filenames, enforced via `.htaccess`), Contao ships `contao:maintenance-mode enable|disable`,
which writes `var/maintenance.html`. `public/index.php` checks for that file and serves a 503
*before Symfony — or even the vendor autoloader — boots*, so it works even if the in-flight
deploy has left the app in a half-broken state.

### 10.2 The virtual filesystem (DBAFS) needs its own reconciliation step

`files/` on disk is mirrored into the `tl_files` database table. `contao:filesync` reconciles
the two by content hash — detects added/updated/deleted/**moved** files (rename-safe, preserves
the UUIDs other tables reference) and is safe to run after an `rsync --delete`. It's idempotent
(a second run with nothing changed reports "No changes.") and only ever edits `tl_files` catalog
rows, never the files themselves, so unlike migrations it gets no `DESTROY`-style gate — a bad
sync is cheap to re-run. It has no `--dry-run` flag.

`public/`'s own symlinks (`public/assets`, `public/system/themes`, `public/files/div`,
`public/files/themes/velo/{css,fonts}`) are driven by a *different* mechanism: an empty
`.public` marker file in each folder that should be web-exposed
(`vendor/contao/core-bundle/src/Command/SymlinksCommand.php`). `contao:symlinks` regenerates them
server-side on every deploy as a cheap, idempotent safety net — the rsynced symlinks (all
relative) should already be valid, but this guards against edge cases.

### 10.3 `public/.htaccess` is deliberately never shipped by a deploy

`public/` is entirely generated (by Composer's `contao-setup` hook) and gitignored here, unlike
the Backend project where `public/.htaccess` is committed specifically to carry Hostpoint's
PHP-version header through deploys. If this project's deploy shipped a fresh `public/.htaccess`
on every run, it would silently overwrite Hostpoint's panel-managed
`Use php-fpm <version>` header with the vanilla skeleton version every time.

Instead: `public/.htaccess` is excluded from every `rsync` in `deploy.sh`/`promote.sh`, uploaded
**once** by hand during one-time server setup (§3), and `check_php_version` reads the *live*
server copy over SSH rather than a local file. If Contao's own skeleton `.htaccess` content ever
needs updating (e.g. after a Contao upgrade changes it), that's a manual one-time re-upload, not
something that happens automatically on every deploy.

### 10.4 Production's domain will change after go-live

Production is first reachable at `web.velofreundliches-wetzikon.ch`, later at the apex
`velofreundliches-wetzikon.ch`. `REMOTE_BASE_PATH_PRODUCTION` is a fixed filesystem path, never a
domain name — repointing the apex domain's document root to the same path afterward is a
Hostpoint control-panel change only. Nothing in `deploy.env` or any script needs to change.

### 10.5 `files/div/` is the only production-mastered content

Only `files/div/` (true user-uploaded content, never committed to git) flows production → test/
local and is excluded from every code deploy. `files/themes/` (theme CSS/fonts/images) is
committed to git and deploys as ordinary code, local → test/production, same as any other file —
it's never touched by a refresh in either direction.

### 10.6a `tl_page.useSSL` is an environment property, not content (temporary phase)

Local runs over plain HTTP, so its root page(s) naturally have `useSSL=0` — which travels
wholesale with every `push-local-to-production.sh` run, since that script pushes a full database
dump. Production here is HTTPS-only (Hostpoint enforces HTTP → HTTPS for this domain), and Contao's
own `RedirectController` actively redirects HTTPS requests to HTTP for any root page where
`useSSL` is false — which collides head-on with Hostpoint's enforcement into an infinite redirect
loop (symptom: endless 301s bouncing between `https://` and `http://`, same host).
`push-local-to-production.sh` forces `useSSL=1` back on production's root page(s) immediately
after every import (`remote/force-root-ssl.sh`) for exactly this reason — same category of fix as
never shipping `.env.local`, just at the data layer instead of the file layer. Only matters during
the temporary local-is-master phase (§6a); once production/test become the masters again, this
becomes real, environment-correct content and the extra step is no longer needed.

### 10.6 `parse_url()` doesn't decode percent-encoding

Every `remote/*.sh` script and `refresh-local.sh`'s local credential parsing pulls values out of
`DATABASE_URL` with PHP's `parse_url()`, which returns components exactly as written — still
percent-encoded. Every value is explicitly `rawurldecode()`d before use. Decoded credentials are
`eval`'d directly into the calling shell, never written to a temp file.

---

## 11. Checklist

### Current phase (no test environment, local is master — see callout near the top)

**One-time**
- [ ] `deploy.env` filled in, `REMOTE_BASE_PATH_TEST`/`DB_NAME_TEST` left unset (§2)
- [ ] Production's subdomain + document root + PHP version configured in Hostpoint's panel (§3)
- [ ] `.env.local` uploaded to production (§3)
- [ ] `public/.htaccess` uploaded once to production (§3)

**Every push**
- [ ] `./deploy/deploy.sh production` — ships code/schema
- [ ] `./deploy/push-local-to-production.sh` — pushes local's data (§6a)
- [ ] Spot-check the temporary production URL and
      `.../files/themes/velo/css/site.css` → 200 OK

### Once a test environment exists (normal flow resumes)

**One-time**
- [ ] `REMOTE_BASE_PATH_TEST`/`DB_NAME_TEST` filled in in `deploy.env`
- [ ] Test's subdomain + document root + PHP version configured in Hostpoint's panel (§3)
- [ ] `.env.local` uploaded to test (§3)
- [ ] `public/.htaccess` uploaded once to test (§3)
- [ ] `contao:user:create` run once per environment, after that environment's first deploy (§4/§5)
- [ ] Stop using `push-local-to-production.sh` (§6a) — production/test become the masters again
- [ ] Production's initial page structure built out in the live backend after its first real seed

**Every release**
- [ ] `./deploy/deploy.sh test`, validated by hand
- [ ] `./deploy/promote.sh`
- [ ] `contao:migrate --dry-run` on production shows nothing pending
- [ ] Spot-check `https://web.velofreundliches-wetzikon.ch` (or the apex domain, once switched)
      and `https://web.velofreundliches-wetzikon.ch/files/themes/velo/css/site.css` → 200 OK

---

## 12. Environment variables reference

| Variable | Description |
|---|---|
| `DATABASE_URL` | `mysql://user:password@host:3306/dbname?serverVersion=mariadb-X.X` — password percent-encoded |
| `APP_SECRET` | Random, distinct per environment — `openssl rand -hex 32` |
| `APP_ENV` | `prod` on both test and production |
| `APP_DEBUG` | `0` on both test and production |
| `MAILER_DSN` | Real SMTP account, per environment |
