<?php

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
$dca['palettes']['navigation'] = str_replace('{template_legend', '{amp_legend},ampRenderSubitemsAsAccordions;{template_legend', $dca['palettes']['navigation']);
$dca['palettes']['customnav'] = str_replace('{template_legend', '{amp_legend},ampRenderSubitemsAsAccordions;{template_legend', $dca['palettes']['customnav']);

/**
 * Fields
 */
$fields = [
    'ampRenderSubitemsAsAccordions' => [
        'label'                   => &$GLOBALS['TL_LANG']['tl_module']['ampRenderSubitemsAsAccordions'],
        'exclude'                 => true,
        'inputType'               => 'checkbox',
        'eval'                    => ['tl_class' => 'w50'],
        'sql'                     => "char(1) NOT NULL default ''"
    ],
];

$dca['fields'] += $fields;