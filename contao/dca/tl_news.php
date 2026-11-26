<?php

declare(strict_types=1);

// ============================================================
// "Unterüberschrift" (subheadline) is no longer rendered anywhere on the front end (see
// templates/news_full.html.twig's project override) -- drop it from every source palette too,
// so editors aren't offered a field that silently has no effect (2026-11-26).
// ============================================================

foreach (array_keys($GLOBALS['TL_DCA']['tl_news']['palettes']) as $paletteName) {
    if ('__selector__' === $paletteName) {
        continue;
    }

    $GLOBALS['TL_DCA']['tl_news']['palettes'][$paletteName] = str_replace(
        'subheadline,',
        '',
        $GLOBALS['TL_DCA']['tl_news']['palettes'][$paletteName],
    );
}
