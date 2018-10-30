<?php

$dca = &$GLOBALS['TL_DCA']['tl_page'];

/**
 * Palettes
 */
$dca['palettes']['regular'] = str_replace('encoreEntries', 'encoreEntries,encoreEntriesAmp', $dca['palettes']['regular']);

/**
 * Subpalettes
 */

/**
 * Fields
 */
$fields = [
    'encoreEntriesAmp' => $GLOBALS['TL_DCA']['tl_page']['fields']['encoreEntries']
];

$fields['encoreEntriesAmp']['label'] = &$GLOBALS['TL_LANG']['tl_page']['encoreEntriesAmp'];

$dca['fields'] += $fields;