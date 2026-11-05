<?php

declare(strict_types=1);

/*
 * Router script for PHP's built-in web server during local development only
 * (php -S localhost:8000 -t public public/router.php) -- never used in production, where
 * Apache/Nginx handle this via their own rewrite rules (see public/.htaccess).
 *
 * Without this, PHP's built-in server serves any file that already exists on disk directly and
 * returns its own generic 404 for anything else -- it never falls through to index.php, so
 * Contao/Symfony never gets a chance to run for paths that only exist "virtually" at request
 * time. Deferred image resizing is exactly that: Contao generates a resized image the first
 * time its URL under /assets/images/ is requested, rather than upfront. Without this router,
 * every not-yet-generated variant 404s immediately instead of being generated -- this is what
 * broke the backend file-picker thumbnails and public image URls locally (see the 2026-11-05
 * investigation: the 404 page was confirmed to be PHP's own built-in-server error template, not
 * Contao's, proving the request never reached the application at all).
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__.$path;

if ('/' !== $path && is_file($file)) {
    return false; // let the built-in server serve real, already-existing static files itself
}

require __DIR__.'/index.php';
