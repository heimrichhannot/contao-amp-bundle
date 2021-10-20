<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/*
 * Palettes
 */
$dca['palettes'][\HeimrichHannot\AmpBundle\FrontendModule\AmpNavigationModule::TYPE] = str_replace('{template_legend', '{amp_legend},ampRenderSubItemsAsAccordions;{template_legend', $dca['palettes']['navigation']);

/**
 * Fields.
 */
$fields = [
    'ampRenderSubItemsAsAccordions' => [
        'label' => &$GLOBALS['TL_LANG']['tl_module']['ampRenderSubItemsAsAccordions'],
        'exclude' => true,
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50'],
        'sql' => "char(1) NOT NULL default ''",
    ],
];

$dca['fields'] = array_merge($dca['fields'] ?? [], $fields);
