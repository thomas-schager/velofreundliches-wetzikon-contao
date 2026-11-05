# Velofreundliches Wetzikon

Website of the civic initiative for cycling-friendly infrastructure in Wetzikon,
Switzerland — built with Contao 5 CMS and a custom design system.

![Contao](https://img.shields.io/badge/Contao-5.7-E4002B?logo=contao&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-12.3-003545?logo=mariadb&logoColor=white)

## What this is

A German-language website for Velofreundliches Wetzikon, an association that advocates
for safer cycling conditions in the Wetzikon area (canton Zurich).

Key features of the site:
- Content pages with a custom Twig/CSS design system
- Event calendar and news archive
- Membership sign-up form
- "Stelle melden" — a form for residents to report dangerous or problematic cycling spots

## Tech stack

| Layer | Choice |
|---|---|
| CMS | Contao 5.7 Managed Edition |
| Language | PHP 8.1+ |
| Database | MariaDB (MySQL 8.4 LTS also works) |
| Templates | Twig (Contao Modern layout) |
| Styles | Plain CSS with a custom design token system |
| Assets | Lucide icons (CDN), Google Fonts (Roboto + Source Serif) |

## Local setup

**Requirements:** PHP ≥ 8.1, Composer 2, MariaDB

MariaDB runs locally in Docker rather than installed natively — start it first, see
[DATABASE.md](DATABASE.md) for the full guide (Colima setup, troubleshooting, what to do if the
container doesn't exist yet):

```bash
colima start
docker start velowetzikon-mariadb
```

Then the app itself:

```bash
git clone git@github.com:thomas-schager/velofreundliches-wetzikon-contao.git
cd velofreundliches-wetzikon-contao

composer install

cp .env .env.local
# Edit .env.local: set DATABASE_URL and APP_SECRET

php bin/console contao:migrate --no-interaction
php bin/console contao:user:create

php -S localhost:8000 -t public public/router.php
```

The `public/router.php` argument matters: without it, PHP's built-in server never hands requests
for not-yet-existing files (e.g. Contao's deferred/on-the-fly generated image resizes under
`/assets/images/`) over to Contao at all, so they 404 immediately instead of being generated —
this also breaks backend file-picker thumbnails.

Open `http://localhost:8000` — frontend.  
Open `http://localhost:8000/contao` — Contao backend.

## Deployment

See [DEPLOY.md](DEPLOY.md) for the full guide, including the shared-hosting
(no SSH) workflow, PHP upload limits, and known gotchas around Contao's
deferred image resizing.

## Project structure

```
├── contao/          # DCA field extensions
├── files/themes/    # Custom CSS, fonts, static images
├── src/             # PHP content element controllers
├── templates/       # Twig template overrides
├── public/          # Web root (point your server here)
├── DATABASE.md      # Local MariaDB (Docker) setup guide
└── DEPLOY.md        # Deployment guide
```

## License

Private project — all rights reserved.
