<?php

$dca = &$GLOBALS['TL_DCA']['tl_page'];

/**
 * Selectos
 */
$dca['palettes']['__selector__'][] = 'amp';

/**
 * Palettes
 */
$dca['palettes']['regular'] = str_replace('encoreEntries', 'encoreEntries,encoreEntriesAmp', $dca['palettes']['regular']);
$dca['palettes']['regular'] = str_replace('includeLayout', 'includeLayout,amp', $dca['palettes']['regular']);
$dca['palettes']['root']    = str_replace('includeLayout', 'includeLayout,amp', $dca['palettes']['root']);

/**
 * Subpalettes
 */
$dca['subpalettes']['amp_active'] = 'ampLayout';

/**
 * Fields
 */
$fields = [
    'encoreEntriesAmp' => $GLOBALS['TL_DCA']['tl_page']['fields']['encoreEntries'],
    'amp'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['amp'],
        'inputType' => 'select',
        'default'   => 'inactive',
        'options'   => ['active', 'inactive'],
        'reference' => $GLOBALS['TL_LANG']['tl_page']['reference']['amp'],
        'eval'      => ['includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
        'sql'       => "char(8) NOT NULL default ''",
    ],
    'ampLayout'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_page']['ampLayout'],
        'exclude'          => true,
        'search'           => true,
        'inputType'        => 'select',
        'foreignKey'       => 'tl_layout.name',
        'options_callback' => ['tl_page', 'getPageLayouts'],
        'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
        'sql'              => "int(10) unsigned NOT NULL default '0'",
        'relation'         => ['type' => 'hasOne', 'load' => 'lazy'],
    ],
];

$fields['encoreEntriesAmp']['label'] = &$GLOBALS['TL_LANG']['tl_page']['encoreEntriesAmp'];

$dca['fields'] += $fields;