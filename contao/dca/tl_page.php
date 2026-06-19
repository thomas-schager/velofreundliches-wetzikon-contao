<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

// Add nav_subtitle after the navigation label field on regular pages
PaletteManipulator::create()
    ->addField('nav_subtitle', 'navTitle', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('regular', 'tl_page');

$GLOBALS['TL_DCA']['tl_page']['fields']['nav_subtitle'] = [
    'label'     => ['Nav-Untertitel', 'Optionaler Untertitel, der im Navigationsmenü unter dem Seitentitel erscheint.'],
    'inputType' => 'text',
    'eval'      => ['tl_class' => 'w100', 'maxlength' => 255],
    'sql'       => "varchar(255) NOT NULL default ''",
];
