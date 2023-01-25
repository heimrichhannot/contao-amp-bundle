<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_HOOKS']['modifyFrontendPage']['huh_amp'] = ['huh.amp.event_listener.hook_listener', 'modifyFrontendPage'];

/*
 * Frontend modules
 */
System::getContainer()->get('huh.utils.array')->insertInArrayByName(
    $GLOBALS['FE_MOD']['navigationMenu'], 'navigation',
    [\HeimrichHannot\AmpBundle\FrontendModule\AmpNavigationModule::TYPE => \HeimrichHannot\AmpBundle\FrontendModule\AmpNavigationModule::class], 1
);

$GLOBALS['TL_NOINDEX_KEYS'][] = 'amp';
