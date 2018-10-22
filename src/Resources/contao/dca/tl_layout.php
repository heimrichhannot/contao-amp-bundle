<?php

$dca = &$GLOBALS['TL_DCA']['tl_layout'];

/**
 * Palettes
 */
$dca['palettes']['default'] = str_replace('{sections_legend', '{amp_legend},addAmp;{sections_legend', $dca['palettes']['default']);

/**
 * Fields
 */
$fields = [
    'addAmp' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['addAmp'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'eval'                    => ['tl_class' => 'w50'],
        'sql'                     => "char(1) NOT NULL default ''"
    ],
];

$dca['fields'] += $fields;