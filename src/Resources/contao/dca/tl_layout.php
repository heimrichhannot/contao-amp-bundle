<?php

$dca = &$GLOBALS['TL_DCA']['tl_layout'];

/**
 * Callbacks
 */
$dca['config']['onload_callback'][] = ['huh.amp.util.layout_util', 'modifyDca'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'addAmpAnalytics';

$dca['palettes']['default'] = str_replace('{sections_legend', '{amp_legend},addAmp;{sections_legend', $dca['palettes']['default']);

/**
 * Subpalettes
 */
$dca['subpalettes']['addAmpAnalytics'] = 'ampAnalyticsTemplate';

/**
 * Fields
 */
$fields = [
    'addAmp'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_layout']['addAmp'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'addAmpAnalytics'      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_layout']['addAmpAnalytics'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'ampAnalyticsTemplate' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_layout']['ampAnalyticsTemplate'],
        'exclude'          => true,
        'filter'           => true,
        'inputType'        => 'select',
        'options_callback' => function (\Contao\DataContainer $dc) {
            return System::getContainer()->get('huh.utils.choice.twig_template')->getCachedChoices([
                'analytics_amp_'
            ]);
        },
        'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true],
        'sql'              => "varchar(64) NOT NULL default ''"
    ],
];

$dca['fields'] += $fields;