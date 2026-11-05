<?php

declare(strict_types=1);

// ============================================================
// Palettes for custom Velo content elements
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_hero'] =
    '{type_legend},type;'.
    '{hero_legend},velo_eyebrow,velo_hero_title,velo_lead;'.
    '{buttons_legend},velo_btn_primary_label,velo_btn_primary_href,velo_btn_secondary_label,velo_btn_secondary_href;'.
    '{stats_legend},velo_stats;'.
    '{image_legend:hide},addImage;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_melde'] =
    '{type_legend},type;'.
    '{melde_legend},velo_eyebrow,headline,text,velo_btn_label,velo_melde_card;'.
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

// Lighter, shorter alternative to velo_newsletter -- inline banner instead of a full dark
// section (chosen from 3 drafts compared in /tmp, see the design-tokens repo discussion).
// Trial element for one page: fully fixed, no editable content fields at all -- headline, text
// and the form are hardcoded directly in the template (see velo_newsletter_compact.html.twig)
// rather than configurable per instance. Only the technical fields (publish/schedule) remain.
$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_newsletter_compact'] =
    '{type_legend},type;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_tabs'] =
    '{type_legend},type;'.
    '{tabs_legend},velo_tabs;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_gallery'] =
    '{type_legend},type,headline;'.
    '{images_legend},velo_gallery_images;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_feature_photo'] =
    '{type_legend},type,headline;'.
    '{content_legend},velo_eyebrow,velo_fp_text;'.
    '{image_legend},velo_fp_image,velo_fp_alt;'.
    '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['palettes']['velo_park_test'] =
    '{type_legend},type,velo_pt_title,velo_lead;'.
    '{highlights_legend},velo_pt_highlights;'.
    '{explanation_legend},velo_pt_explanation;'.
    '{note_legend},velo_pt_note;'.
    '{criteria_legend},velo_pt_crit_access,velo_pt_crit_steps_entrance,velo_pt_crit_steps_carts,velo_pt_crit_trailer_space,velo_pt_crit_free_spaces,velo_pt_crit_weather;'.
    '{map_legend},velo_pt_address,velo_pt_lat,velo_pt_lng;'.
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
    /* 'long' (not just 'clr') to match the title field it follows in every palette that uses
       it (velo_hero_title, velo_pt_title, both 'clr long') -- without it, this field fell back
       to a narrower default width instead of spanning the same full width as its neighbour,
       which read as an indent in the backend edit form. */
    'eval'      => ['rows' => 3, 'tl_class' => 'clr long'],
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

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_hero_title'] = [
    'label'     => ['H1-Titel (HTML erlaubt)', 'Titel der Seite. HTML-Tags wie <em> sind erlaubt.'],
    'inputType' => 'text',
    'eval'      => ['allowHtml' => true, 'maxlength' => 512, 'tl_class' => 'clr long'],
    'sql'       => "varchar(512) NOT NULL default ''",
];

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
    'label'     => ['Button-Text', 'Text des Buttons, der zur Meldekarte verlinkt'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 100, 'tl_class' => 'w50'],
    'sql'       => "varchar(100) NOT NULL default 'Stelle melden'",
];

/* The option VALUE is a short stable key, not the literal path -- VeloMeldeController maps it
   to the actual URL path (see MELDE_CARD_PATHS there), same indirection pattern as
   VeloParkTestController::CRITERIA_OPTIONS. Keeps the stored DB value decoupled from the path,
   so the path can change in one place without touching existing content rows, and new cards
   only ever need a new option here + a new map entry in the controller. */
$GLOBALS['TL_DCA']['tl_content']['fields']['velo_melde_card'] = [
    'label'     => ['Meldekarte', 'Auf welche Velomelder-Karte dieses Element verlinkt'],
    'inputType' => 'select',
    'options'   => [
        'gelbes-band' => 'Feedback gelbes Band',
    ],
    'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql'       => "varchar(64) NOT NULL default ''",
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

// ============================================================
// velo_park_test specific — "Veloparkplätze"-style test reports
// (standalone section: besonderheiten highlights + explanation in the main column,
// note/criteria/map summarized in a wide sidebar card — see velo_park_test.html.twig)
// ============================================================

// Dedicated plain-text title field instead of the generic headline (inputUnit) field -- this
// heading is always the page's H1, editors can't change the level. Same pattern as
// velo_hero_title (also a fixed-level, plain-text title field for exactly this reason).
$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_title'] = [
    'label'     => ['H1-Titel (HTML erlaubt)', 'Titel der Seite. HTML-Tags wie <em> sind erlaubt.'],
    'inputType' => 'text',
    'eval'      => ['allowHtml' => true, 'maxlength' => 255, 'tl_class' => 'clr long'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_highlights'] = [
    'label'     => ['Besonderheiten', 'Stichpunkte mit Einordnung: + (positiv), ~ (teils), – (negativ)'],
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'minCount'     => 1,
        'columnFields' => [
            'sentiment' => [
                'label'     => 'Einordnung',
                'inputType' => 'select',
                'options'   => ['positive' => '+ positiv', 'neutral' => '~ teils', 'negative' => '– negativ'],
                'eval'      => ['style' => 'width:110px'],
            ],
            'text' => [
                'label'     => 'Text',
                'inputType' => 'text',
                'eval'      => ['style' => 'width:420px'],
            ],
        ],
        'tl_class' => 'clr',
    ],
    'sql'  => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_explanation'] = [
    'label'     => ['Erläuterung', 'Freitext unter den Besonderheiten (Notenberechnung, Hintergrund, Links)'],
    'inputType' => 'textarea',
    'eval'      => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
    'sql'       => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_note'] = [
    'label'     => ['Note', 'Schulnote 1–6, bis zu 2 Nachkommastellen (z.B. 4.75) — kein Bindestrich/Bereich'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 4, 'tl_class' => 'w50'],
    'sql'       => "varchar(4) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_crit_access'] = [
    'label'     => ['Erreichbarkeit, Anfahrt', ''],
    'inputType' => 'select',
    'options'   => ['gut' => 'gut', 'mittel' => 'mittel', 'schlecht' => 'schlecht'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_crit_steps_entrance'] = [
    'label'     => ['Schritte bis zum Eingang', ''],
    'inputType' => 'select',
    'options'   => ['lt50' => '<50', '50-100' => '50-100', '100plus' => '100+'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_crit_steps_carts'] = [
    'label'     => ['Schritte bis zu den Einkaufswagen', ''],
    'inputType' => 'select',
    'options'   => ['lt50' => '<50', '50-100' => '50-100', '100plus' => '100+'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_crit_trailer_space'] = [
    'label'     => ['Platz für Veloanhänger & Cargobikes', ''],
    'inputType' => 'select',
    'options'   => ['ja' => 'ja', 'knapp' => 'knapp', 'nein' => 'nein'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_crit_free_spaces'] = [
    'label'     => ['Ausreichend Plätze während Stosszeiten', ''],
    'inputType' => 'select',
    'options'   => ['ja' => 'ja', 'teils' => 'teils', 'nein' => 'nein'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_crit_weather'] = [
    'label'     => ['Wettergeschützte Velo-Parkplätze', ''],
    'inputType' => 'select',
    'options'   => ['ja' => 'ja', 'wenige' => 'wenige', 'nein' => 'nein'],
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_address'] = [
    'label'     => ['Adresse', 'Beschriftung über der Karte'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_lat'] = [
    'label'     => ['Breitengrad (lat)', 'Dezimalgrad, z.B. 47.3280714'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 20, 'tl_class' => 'w50'],
    'sql'       => "varchar(20) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_pt_lng'] = [
    'label'     => ['Längengrad (lng)', 'Dezimalgrad, z.B. 8.7961513'],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 20, 'tl_class' => 'w50'],
    'sql'       => "varchar(20) NOT NULL default ''",
];

// ============================================================
// velo_gallery specific — simple photo gallery (main photo + thumbnail strip + lightbox)
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_gallery_images'] = [
    'label'     => ['Fotos', 'Mindestens 2 Fotos. Reihenfolge per Drag & Drop.'],
    'inputType' => 'multiColumnWizard',
    'eval'      => [
        'minCount'     => 2,
        'columnFields' => [
            'src' => [
                'label'     => 'Foto',
                'inputType' => 'fileTree',
                'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'style' => 'width:300px'],
            ],
            'alt' => [
                'label'     => 'Alt-Text',
                'inputType' => 'text',
                'eval'      => ['style' => 'width:300px'],
            ],
        ],
        'tl_class' => 'clr',
    ],
    'sql'  => 'blob NULL',
];

// ============================================================
// velo_feature_photo specific — standout, narrower photo+text card (photo left, text right
// on wider screens; stacks on mobile)
// ============================================================

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_fp_image'] = [
    'label'     => ['Foto', ''],
    'inputType' => 'fileTree',
    'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
    'sql'       => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_fp_alt'] = [
    'label'     => ['Alt-Text', ''],
    'inputType' => 'text',
    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['velo_fp_text'] = [
    'label'     => ['Text', '2-3 Abschnitte oder eine Liste'],
    'inputType' => 'textarea',
    'eval'      => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
    'sql'       => 'text NULL',
];
