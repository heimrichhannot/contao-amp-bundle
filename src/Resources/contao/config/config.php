<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout']['huh_amp'] = ['huh.amp.event_listener.hook_listener', 'getPageLayout'];
$GLOBALS['TL_HOOKS']['parseTemplate']['huh_amp'] = ['huh.amp.event_listener.hook_listener', 'parseTemplate'];
$GLOBALS['TL_HOOKS']['generatePage']['huh_amp'] = ['huh.amp.event_listener.hook_listener', 'generatePage'];
$GLOBALS['TL_HOOKS']['modifyFrontendPage']['huh_amp'] = ['huh.amp.event_listener.hook_listener', 'modifyFrontendPage'];

/**
 * Frontend modules
 */
System::getContainer()->get('huh.utils.array')->insertInArrayByName(
    $GLOBALS['FE_MOD']['navigationMenu'], 'navigation',
    [\HeimrichHannot\AmpBundle\FrontendModule\AmpNavigationModule::TYPE => \HeimrichHannot\AmpBundle\FrontendModule\AmpNavigationModule::class], 1
);

$GLOBALS['TL_NOINDEX_KEYS'][] = 'amp';
