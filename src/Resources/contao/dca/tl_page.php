<?php

$table = 'tl_page';
$dca = &$GLOBALS['TL_DCA'][$table];

/**
 * Selectos
 */
$dca['palettes']['__selector__'][] = 'enableAmp';

/**
 * Palettes
 */
$dca['palettes']['regular'] = str_replace('encoreEntries', 'encoreEntries,encoreEntriesAmp', $dca['palettes']['regular']);
$dca['palettes']['forward'] = str_replace('encoreEntries', 'encoreEntries,encoreEntriesAmp', $dca['palettes']['regular']);
$dca['palettes']['regular'] = str_replace('includeLayout', 'includeLayout,enableAmp', $dca['palettes']['regular']);
$dca['palettes']['forward'] = str_replace('includeLayout', 'includeLayout,enableAmp', $dca['palettes']['regular']);
$dca['palettes']['root']    = str_replace('includeLayout', 'includeLayout,enableAmp', $dca['palettes']['root']);

/**
 * Subpalettes
 */
$dca['subpalettes']['enableAmp_active'] = 'ampLayout';

/**
 * Fields
 */
$fields = [
    'encoreEntriesAmp' => $GLOBALS['TL_DCA']['tl_page']['fields']['encoreEntries'],
    'enableAmp'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_page']['enableAmp'],
        'inputType' => 'select',
        'default'   => 'inactive',
        'options'   => ['active', 'inactive'],
        'reference' => $GLOBALS['TL_LANG']['tl_page']['reference']['enableAmp'],
        'eval'      => ['includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50'],
        'sql'       => "char(8) NOT NULL default ''",
    ],
    'ampLayout'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_page']['ampLayout'],
        'exclude'          => true,
        'search'           => true,
        'inputType'        => 'select',
        'foreignKey'       => 'tl_layout.name',
        'options_callback' => ['huh.amp.manager.data_container.page', 'getAmpPageLayouts'],
        'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
        'sql'              => "int(10) unsigned NOT NULL default '0'",
        'relation'         => ['type' => 'hasOne', 'load' => 'lazy'],
    ],
];

$fields['encoreEntriesAmp']['label'] = &$GLOBALS['TL_LANG']['tl_page']['encoreEntriesAmp'];

$dca['fields'] = array_merge($fields, is_array($dca['fields']) ? $dca['fields'] : []);

//\Contao\System::getContainer()->get('huh.utils.dca')->addOverridableFields([])