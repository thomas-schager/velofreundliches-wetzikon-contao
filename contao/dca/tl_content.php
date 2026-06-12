<?php

declare(strict_types=1);

// ============================================================
// Palettes for custom Velo content elements
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_hero'] =
    '{type_legend},type,headline;'.
    '{hero_legend},velo_eyebrow,velo_lead;'.
    '{buttons_legend},velo_btn_primary_label,velo_btn_primary_href,velo_btn_secondary_label,velo_btn_secondary_href;'.
    '{stats_legend},velo_stats;'.
    '{image_legend:hide},addImage;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_melde'] =
    '{type_legend},type;'.
    '{melde_legend},velo_eyebrow,headline,text,velo_btn_label,velo_form_id;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_positions'] =
    '{type_legend},type;'.
    '{section_legend},velo_section_eyebrow,velo_section_heading,velo_section_lead;'.
    '{cards_legend},velo_cards;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_join'] =
    '{type_legend},type,headline;'.
    '{join_legend},velo_eyebrow,text;'.
    '{buttons_legend},velo_btn_primary_label,velo_btn_primary_href,velo_btn_secondary_label,velo_btn_secondary_href;'.
    '{benefits_legend},velo_benefits;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_newsletter'] =
    '{type_legend},type,headline;'.
    '{newsletter_legend},velo_eyebrow,text,velo_form_id;'.
    '{benefits_legend},velo_benefits;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_tabs'] =
    '{type_legend},type;'.
    '{tabs_legend},velo_tabs;'.
    '{invisible_legend:hide},invisible,start,stop';

// ============================================================
// Fields shared across multiple elements
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_eyebrow'] = [
    'label'     => ['Eyebrow', 'Kleiner Text über der Überschrift (z.B. «Interessengemeinschaft · Wetzikon ZH»)'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_lead'] = [
    'label'     => ['Lead-Text', 'Einleitungstext unter der Überschrift'],
    'inputType' => 'textarea',
    'eval'      => ['rte' => false, 'rows' => 3, 'tl_class' => 'clr'],
    'sql'       => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_btn_primary_label'] = [
    'label'     => ['Primär-Button Text', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 100, 'tl_class' => 'w50'],
    'sql'       => "varchar(100) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_btn_primary_href'] = [
    'label'     => ['Primär-Button URL / Seite', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_btn_secondary_label'] = [
    'label'     => ['Sekundär-Button Text', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 100, 'tl_class' => 'w50'],
    'sql'       => "varchar(100) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_btn_secondary_href'] = [
    'label'     => ['Sekundär-Button URL / Seite', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

// ============================================================
// velo_hero specific
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_stats'] = [
    'label'     => ['Statistiken', '3 Zahlen mit Beschriftung (z.B. «412 Mitglieder»)'],
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'minCount'        => 0,
        'maxCount'        => 3,
        'columnFields'    => [
            'value' => [
                'label'     => 'Wert',
                'inputType' => 'text',
                'eval'      => ['style' => 'width:100px'],
            ],
            'label' => [
                'label'     => 'Beschriftung',
                'inputType' => 'text',
                'eval'      => ['style' => 'width:200px'],
            ],
        ],
        'tl_class' => 'clr',
    ],
    'sql'  => 'blob NULL',
];

// ============================================================
// velo_melde specific
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_btn_label'] = [
    'label'     => ['Button-Text', 'Text des Buttons, der das Modal öffnet'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 100, 'tl_class' => 'w50'],
    'sql'       => "varchar(100) NOT NULL default 'Stelle melden'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_form_id'] = [
    'label'     => ['Formular', 'Das Formular, das im Modal angezeigt wird'],
    'inputType' => 'select',
    'options_callback' => static function (): array {
        $forms = \Contao\FormModel::findAll();
        $opts  = ['' => '— keines —'];
        if ($forms !== null) {
            foreach ($forms as $f) {
                $opts[(string) $f->id] = $f->title;
            }
        }
        return $opts;
    },
    'eval' => ['tl_class' => 'w50'],
    'sql'  => "varchar(10) NOT NULL default ''",
];

// ============================================================
// velo_positions specific
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_section_eyebrow'] = [
    'label'     => ['Abschnitts-Eyebrow', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 100, 'tl_class' => 'w50'],
    'sql'       => "varchar(100) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_section_heading'] = [
    'label'     => ['Abschnitts-Überschrift', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_section_lead'] = [
    'label'     => ['Abschnitts-Lead', ''],
    'inputType' => 'textarea',
    'eval'      => ['rows' => 2, 'tl_class' => 'clr'],
    'sql'       => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_cards'] = [
    'label'     => ['Positions-Karten', 'Bis zu 3 Karten mit Icon, Eyebrow, Titel, Text und Link'],
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'minCount'     => 1,
        'maxCount'     => 3,
        'columnFields' => [
            'icon'       => ['label' => 'Icon (Lucide)', 'inputType' => 'text', 'eval' => ['style' => 'width:120px']],
            'eyebrow'    => ['label' => 'Eyebrow', 'inputType' => 'text', 'eval' => ['style' => 'width:120px']],
            'title'      => ['label' => 'Titel', 'inputType' => 'text', 'eval' => ['style' => 'width:160px']],
            'body'       => ['label' => 'Text', 'inputType' => 'text', 'eval' => ['style' => 'width:200px']],
            'href'       => ['label' => 'Link-URL', 'inputType' => 'text', 'eval' => ['style' => 'width:140px']],
            'link_label' => ['label' => 'Link-Text', 'inputType' => 'text', 'eval' => ['style' => 'width:120px']],
        ],
        'tl_class' => 'clr',
    ],
    'sql'  => 'blob NULL',
];

// ============================================================
// velo_join specific
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_benefits'] = [
    'label'     => ['Stichpunkte-Liste', 'Stichpunkte (Vorteile, Newsletter-Inhalte, etc.)'],
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'columnFields' => [
            'benefit' => ['label' => 'Vorteil', 'inputType' => 'text', 'eval' => ['style' => 'width:400px']],
        ],
        'tl_class' => 'clr',
    ],
    'sql'  => 'blob NULL',
];

// ============================================================
// velo_tabs specific
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_tabs'] = [
    'label'     => ['Tabs', 'Tabs mit Label und HTML-Inhalt'],
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'minCount'     => 1,
        'maxCount'     => 6,
        'columnFields' => [
            'label'   => ['label' => 'Tab-Label', 'inputType' => 'text', 'eval' => ['style' => 'width:160px']],
            'content' => ['label' => 'Inhalt (HTML)', 'inputType' => 'textarea', 'eval' => ['style' => 'width:400px; height:120px']],
        ],
        'tl_class' => 'clr',
    ],
    'sql'  => 'blob NULL',
];
